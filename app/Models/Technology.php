<?php

namespace App\Models;

use App\Enums\TechnologyCategory;
use Database\Factories\TechnologyFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['name', 'category', 'icon', 'color', 'sort_order'])]
class Technology extends Model
{
    /** @use HasFactory<TechnologyFactory> */
    use HasFactory;

    /**
     * The Simple Icons release the icons are loaded from.
     */
    private const ICON_BASE_URL = 'https://cdn.jsdelivr.net/npm/simple-icons@16.34.0/icons/';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'category' => TechnologyCategory::class,
        ];
    }

    /**
     * Order the query as the technologies are shown: by category, then sort order.
     */
    #[Scope]
    protected function ordered(Builder $query): void
    {
        $query->orderByRaw($this->categoryOrderSql($query->qualifyColumn('category')))
            ->orderBy($query->qualifyColumn('sort_order'))
            ->orderBy($query->qualifyColumn('id'));
    }

    /**
     * The folder under public/ that holds icon files not in Simple Icons.
     */
    private const ICON_FILE_DIRECTORY = 'images/technologies/';

    /**
     * Get the URL of the icon: a file in public/images/technologies when the icon
     * is a file name (sakura-internet.svg), otherwise the Simple Icons SVG (react).
     *
     * @return Attribute<string|null, never>
     */
    protected function iconUrl(): Attribute
    {
        return Attribute::get(fn () => match (true) {
            ! $this->icon => null,
            $this->has_icon_file => asset(self::ICON_FILE_DIRECTORY.$this->icon),
            default => self::ICON_BASE_URL.$this->icon.'.svg',
        });
    }

    /**
     * Determine whether the icon is a file of its own, shown in its original colors.
     *
     * @return Attribute<bool, never>
     */
    protected function hasIconFile(): Attribute
    {
        return Attribute::get(fn () => str_contains((string) $this->icon, '.'));
    }

    /**
     * Get the works that use this technology.
     *
     * @return BelongsToMany<Work, $this>
     */
    public function works(): BelongsToMany
    {
        return $this->belongsToMany(Work::class);
    }

    /**
     * Get an ORDER BY expression that follows the order of the category cases.
     */
    private function categoryOrderSql(string $column): string
    {
        $whens = collect(TechnologyCategory::cases())
            ->map(fn (TechnologyCategory $category, int $index) => "when '{$category->value}' then {$index}")
            ->implode(' ');

        return "case {$column} {$whens} end";
    }
}
