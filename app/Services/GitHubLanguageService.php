<?php

namespace App\Services;

use App\Models\GitHubLanguageStat;
use Illuminate\Database\QueryException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Pool;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Totals the languages of a GitHub user's repositories, cached in github_language_stats.
 */
class GitHubLanguageService
{
    /**
     * How long the cached totals stay fresh.
     */
    private const TTL_HOURS = 24;

    public function __construct(
        private readonly string $username,
        private readonly ?string $token = null,
    ) {}

    /**
     * Get bytes per language, largest first.
     *
     * Uses the cache while it is fresh. When GitHub or the database fails,
     * the stale cache is returned so the page can still show something.
     *
     * @return array<string, int>
     */
    public function totals(): array
    {
        $cached = GitHubLanguageStat::orderByDesc('bytes')->get();

        if ($cached->isNotEmpty() && $cached->min('fetched_at')->gt(now()->subHours(self::TTL_HOURS))) {
            return $cached->pluck('bytes', 'language')->all();
        }

        try {
            $totals = $this->fetchTotals();
            $this->saveTotals($totals);

            return $totals;
        } catch (ConnectionException|RequestException|QueryException $e) {
            Log::warning('Failed to refresh GitHub language stats.', ['exception' => $e]);

            return $cached->pluck('bytes', 'language')->all();
        }
    }

    /**
     * Get the largest languages with their share, folding the rest into "その他".
     *
     * @return Collection<int, array{language: string, bytes: int, percent: float}>
     */
    public function topLanguages(int $limit = 6): Collection
    {
        $totals = collect($this->totals());
        $grandTotal = $totals->sum();

        if ($grandTotal === 0) {
            return collect();
        }

        $top = $totals->take($limit);
        $otherBytes = $grandTotal - $top->sum();

        if ($otherBytes > 0) {
            $top->put('その他', $otherBytes);
        }

        return $top->map(fn (int $bytes, string $language) => [
            'language' => $language,
            'bytes' => $bytes,
            'percent' => $bytes / $grandTotal * 100,
        ])->values();
    }

    /**
     * Fetch every repository's languages from the GitHub REST API and add them up.
     *
     * @return array<string, int>
     *
     * @throws ConnectionException
     * @throws RequestException
     */
    private function fetchTotals(): array
    {
        $repos = $this->request()
            ->get("users/{$this->username}/repos", ['per_page' => 100])
            ->throw()
            ->collect()
            ->pluck('name');

        $responses = Http::pool(fn (Pool $pool) => $repos->map(
            fn (string $repo) => $this->request($pool->as($repo))->get("repos/{$this->username}/{$repo}/languages"),
        )->all());

        $totals = [];

        foreach ($responses as $response) {
            if ($response instanceof \Throwable) {
                throw $response;
            }

            foreach ($response->throw()->json() as $language => $bytes) {
                $totals[$language] = ($totals[$language] ?? 0) + $bytes;
            }
        }

        arsort($totals);

        return $totals;
    }

    /**
     * Replace the cached totals.
     *
     * @param  array<string, int>  $totals
     */
    private function saveTotals(array $totals): void
    {
        $now = now();

        DB::transaction(function () use ($totals, $now) {
            GitHubLanguageStat::query()->delete();

            GitHubLanguageStat::insert(collect($totals)->map(fn (int $bytes, string $language) => [
                'language' => $language,
                'bytes' => $bytes,
                'fetched_at' => $now,
            ])->values()->all());
        });
    }

    /**
     * Build a request with the headers GitHub expects.
     */
    private function request(?PendingRequest $request = null): PendingRequest
    {
        return ($request ?? Http::createPendingRequest())
            ->baseUrl('https://api.github.com')
            ->accept('application/vnd.github+json')
            ->withHeaders(['X-GitHub-Api-Version' => '2022-11-28'])
            ->withUserAgent('portfolio-laravel')
            ->timeout(10)
            ->when($this->token, fn (PendingRequest $request, string $token) => $request->withToken($token));
    }
}
