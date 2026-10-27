<?php

namespace App\Console\Commands;

use App\Enums\PublishStatus;
use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

#[Signature('app:import-legacy
    {--dry-run : Show what would be imported without writing anything}
    {--uploads=storage/app/legacy/uploads : The plain PHP version\'s uploads directory, relative to the project root}')]
#[Description('Import posts, works, categories, tags and images from the plain PHP version (kadai09_auth)')]
class ImportLegacy extends Command
{
    /**
     * Tables filled by this command, in insert order.
     */
    private const TARGET_TABLES = ['categories', 'tags', 'posts', 'works', 'work_images', 'post_tag', 'tag_work'];

    /**
     * Problems found in the legacy data, shown before importing.
     *
     * @var array<int, string>
     */
    private array $issues = [];

    /**
     * Image files to copy into the public disk, keyed by their new path.
     *
     * @var array<string, string>
     */
    private array $copies = [];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $admin = User::oldest('id')->first();

        if (! $admin) {
            $this->error('管理者がいません。先に `sail artisan db:seed` で管理者を作ってください。');

            return self::FAILURE;
        }

        $filled = collect(self::TARGET_TABLES)->filter(fn (string $table) => DB::table($table)->exists());

        if ($filled->isNotEmpty()) {
            $this->error('移行先にデータがあるため中止しました：'.$filled->implode(', '));
            $this->line('やり直すときは、`sail artisan migrate:fresh --seed` で空にしてから実行してください。');

            return self::FAILURE;
        }

        try {
            $legacy = $this->readLegacy();
        } catch (QueryException $e) {
            $this->error('旧 DB に接続できませんでした。.env の LEGACY_DB_* を確認してください。');
            $this->line($e->getMessage());

            return self::FAILURE;
        }

        $rows = $this->convert($legacy, $admin->id);

        $this->table(['テーブル', '旧 DB', '取り込む'], collect(self::TARGET_TABLES)->map(fn (string $table) => [
            $table,
            $legacy[$table]->count(),
            count($rows[$table]),
        ]));
        $this->line('コピーする画像：'.count($this->copies).' 件');

        if ($this->issues) {
            $this->warn('確認が必要なデータ：');
            foreach ($this->issues as $issue) {
                $this->line("  - {$issue}");
            }
        }

        if ($this->option('dry-run')) {
            $this->info('--dry-run のため、書き込みはしていません。');

            return self::SUCCESS;
        }

        DB::transaction(function () use ($rows) {
            foreach (self::TARGET_TABLES as $table) {
                foreach (array_chunk($rows[$table], 500) as $chunk) {
                    DB::table($table)->insert($chunk);
                }
            }
        });

        foreach ($this->copies as $newPath => $source) {
            Storage::disk('public')->put($newPath, file_get_contents($source));
        }

        $this->info('取り込みが終わりました。');

        return self::SUCCESS;
    }

    /**
     * Read every table to import from the legacy database.
     *
     * @return array<string, Collection<int, object>>
     */
    private function readLegacy(): array
    {
        $db = DB::connection('legacy');

        return collect(self::TARGET_TABLES)
            ->mapWithKeys(fn (string $table) => [$table => $db->table($table)->get()])
            ->all();
    }

    /**
     * Convert legacy rows into rows for this application's tables.
     *
     * IDs are kept so that tag links and gallery images still match.
     *
     * @param  array<string, Collection<int, object>>  $legacy
     * @return array<string, array<int, array<string, mixed>>>
     */
    private function convert(array $legacy, int $adminId): array
    {
        $now = now();

        $taxonomy = fn (string $table) => $legacy[$table]->map(function (object $row) use ($table, $now) {
            $this->checkSlug($table, $row);

            return ['id' => $row->id, 'name' => $row->name, 'slug' => $row->slug, 'created_at' => $now, 'updated_at' => $now];
        })->all();

        $entries = fn (string $table, string $textColumn) => $legacy[$table]->map(function (object $row) use ($table, $textColumn, $adminId) {
            $this->checkSlug($table, $row);

            return [
                'id' => $row->id,
                'user_id' => $adminId,
                'category_id' => $row->category_id,
                'title' => $row->title,
                $textColumn => $row->{$textColumn},
                'thumbnail_path' => $this->thumbnail($table, $row),
                'slug' => $row->slug,
                'status' => $this->status($table, $row),
                'published_at' => substr($row->created_at, 0, 10),
                'created_at' => $row->created_at,
                'updated_at' => $row->created_at,
            ];
        })->all();

        $workImages = $legacy['work_images']->map(function (object $row) {
            [$path, $problem] = $this->image($row->file_path);

            if ($path === null) {
                $this->issues[] = "work_images #{$row->id}（works #{$row->work_id}）は取り込みません（".($problem ?? 'file_path が空').'）';

                return null;
            }

            return ['id' => $row->id, 'work_id' => $row->work_id, 'file_path' => $path, 'sort_order' => $row->sort_order];
        })->filter()->values()->all();

        $pivot = fn (string $table) => $legacy[$table]->map(fn (object $row) => (array) $row)->all();

        return [
            'categories' => $taxonomy('categories'),
            'tags' => $taxonomy('tags'),
            'posts' => $entries('posts', 'body'),
            'works' => $entries('works', 'description'),
            'work_images' => $workImages,
            'post_tag' => $pivot('post_tag'),
            'tag_work' => $pivot('tag_work'),
        ];
    }

    /**
     * Note slugs that the admin forms would now reject. They are imported as they are.
     */
    private function checkSlug(string $table, object $row): void
    {
        if (! preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $row->slug)) {
            $this->issues[] = "{$table} #{$row->id} の slug「{$row->slug}」は英小文字・数字・ハイフンの形式ではありません（編集時に直してください）";
        }
    }

    /**
     * Get a valid status, falling back to draft.
     */
    private function status(string $table, object $row): string
    {
        if (PublishStatus::tryFrom($row->status) === null) {
            $this->issues[] = "{$table} #{$row->id} の status「{$row->status}」は不明なため下書きにします";

            return PublishStatus::Draft->value;
        }

        return $row->status;
    }

    /**
     * Get the new thumbnail path, noting thumbnails whose file cannot be used.
     */
    private function thumbnail(string $table, object $row): ?string
    {
        [$path, $problem] = $this->image($row->thumbnail_path);

        if ($problem) {
            $this->issues[] = "{$table} #{$row->id} は画像なしで取り込みます（サムネイルの{$problem}）";
        }

        return $path;
    }

    /**
     * Turn "uploads/posts/x.jpg" into "posts/x.jpg" and plan copying the file.
     *
     * Returns the new path, or null with the reason when the image cannot be used.
     * An empty path is simply "no image" and has no reason.
     *
     * @return array{0: string|null, 1: string|null}
     */
    private function image(?string $legacyPath): array
    {
        if (blank($legacyPath)) {
            return [null, null];
        }

        // The plain PHP version saved "uploads/works/" when a gallery slot was left empty.
        if (str_ends_with($legacyPath, '/')) {
            return [null, "ファイル名が空：{$legacyPath}"];
        }

        $newPath = preg_replace('#^/?uploads/#', '', $legacyPath);
        $source = base_path($this->option('uploads')).'/'.$newPath;

        if (! is_file($source)) {
            return [null, "ファイルが見つからない：{$legacyPath}"];
        }

        $this->copies[$newPath] = $source;

        return [$newPath, null];
    }
}
