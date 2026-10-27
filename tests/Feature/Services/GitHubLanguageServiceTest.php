<?php

namespace Tests\Feature\Services;

use App\Models\GitHubLanguageStat;
use App\Services\GitHubLanguageService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class GitHubLanguageServiceTest extends TestCase
{
    use RefreshDatabase;

    private function service(?string $token = 'secret-token'): GitHubLanguageService
    {
        return new GitHubLanguageService('octocat', $token);
    }

    private function fakeGitHub(): void
    {
        Http::fake([
            'api.github.com/users/octocat/repos*' => Http::response([['name' => 'blog'], ['name' => 'shop']]),
            'api.github.com/repos/octocat/blog/languages' => Http::response(['PHP' => 300, 'CSS' => 100]),
            'api.github.com/repos/octocat/shop/languages' => Http::response(['PHP' => 200, 'JavaScript' => 400]),
        ]);
    }

    /**
     * @param  array<string, int>  $totals
     */
    private function cache(array $totals, int $hoursAgo): void
    {
        foreach ($totals as $language => $bytes) {
            GitHubLanguageStat::create(['language' => $language, 'bytes' => $bytes, 'fetched_at' => now()->subHours($hoursAgo)]);
        }
    }

    public function test_fetches_and_totals_languages_of_all_repositories(): void
    {
        $this->fakeGitHub();

        $totals = $this->service()->totals();

        $this->assertSame(['PHP' => 500, 'JavaScript' => 400, 'CSS' => 100], $totals);
        $this->assertSame(3, GitHubLanguageStat::count());
        Http::assertSent(fn (Request $request) => $request->url() === 'https://api.github.com/users/octocat/repos?per_page=100');
        Http::assertSentCount(3);
        Http::assertSent(fn (Request $request) => $request->hasHeader('Authorization', 'Bearer secret-token')
            && $request->hasHeader('X-GitHub-Api-Version', '2022-11-28'));
    }

    public function test_token_is_optional(): void
    {
        $this->fakeGitHub();

        $this->service(token: null)->totals();

        Http::assertSent(fn (Request $request) => ! $request->hasHeader('Authorization'));
    }

    public function test_fresh_cache_is_used_without_calling_github(): void
    {
        Http::fake();
        $this->cache(['Ruby' => 10, 'Go' => 20], hoursAgo: 23);

        $this->assertSame(['Go' => 20, 'Ruby' => 10], $this->service()->totals());
        Http::assertNothingSent();
    }

    public function test_stale_cache_is_replaced(): void
    {
        $this->fakeGitHub();
        $this->cache(['Ruby' => 10], hoursAgo: 25);

        $this->assertSame(['PHP' => 500, 'JavaScript' => 400, 'CSS' => 100], $this->service()->totals());
        $this->assertDatabaseMissing('github_language_stats', ['language' => 'Ruby']);
        $this->assertTrue(GitHubLanguageStat::first()->fetched_at->gt(now()->subMinute()));
    }

    public function test_stale_cache_is_returned_when_github_fails(): void
    {
        Http::fake(['api.github.com/*' => Http::response(['message' => 'rate limit'], 403)]);
        $this->cache(['Ruby' => 10], hoursAgo: 25);

        $this->assertSame(['Ruby' => 10], $this->service()->totals());
        $this->assertDatabaseHas('github_language_stats', ['language' => 'Ruby']);
    }

    public function test_cache_is_kept_when_one_repository_fails(): void
    {
        Http::fake([
            'api.github.com/users/octocat/repos*' => Http::response([['name' => 'blog'], ['name' => 'shop']]),
            'api.github.com/repos/octocat/blog/languages' => Http::response(['PHP' => 300]),
            'api.github.com/repos/octocat/shop/languages' => Http::failedConnection(),
        ]);
        $this->cache(['Ruby' => 10], hoursAgo: 25);

        $this->assertSame(['Ruby' => 10], $this->service()->totals());
    }

    public function test_empty_result_when_github_fails_without_cache(): void
    {
        Http::fake(['api.github.com/*' => Http::response([], 500)]);

        $this->assertSame([], $this->service()->totals());
        $this->assertTrue($this->service()->topLanguages()->isEmpty());
    }

    public function test_top_languages_folds_the_rest_into_other(): void
    {
        $this->cache(['A' => 40, 'B' => 30, 'C' => 20, 'D' => 10], hoursAgo: 1);

        $top = $this->service()->topLanguages(limit: 2);

        $this->assertSame(['A', 'B', 'その他'], $top->pluck('language')->all());
        $this->assertEquals([40.0, 30.0, 30.0], $top->pluck('percent')->all());
    }
}
