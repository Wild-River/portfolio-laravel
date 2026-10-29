<?php

namespace Tests\Feature\Support;

use App\Support\AutoLink;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class AutoLinkTest extends TestCase
{
    private function link(string $url): string
    {
        return '<a href="'.$url.'" target="_blank" rel="noopener noreferrer">'.$url.'</a>';
    }

    public function test_urls_become_links(): void
    {
        $this->assertSame(
            'コードは '.$this->link('https://github.com/Wild-River/portfolio-laravel').' で公開しています',
            (string) AutoLink::html('コードは https://github.com/Wild-River/portfolio-laravel で公開しています'),
        );
    }

    public function test_text_without_urls_is_only_escaped(): void
    {
        $this->assertSame("1行目\n&lt;b&gt;2行目&lt;/b&gt;", (string) AutoLink::html("1行目\n<b>2行目</b>"));
    }

    public function test_html_around_and_inside_urls_is_escaped(): void
    {
        $html = (string) AutoLink::html('<script>alert(1)</script> https://example.com/?a=1&b=<x>');

        $this->assertStringNotContainsString('<script>', $html);
        $this->assertStringNotContainsString('<x>', $html);
        $this->assertStringContainsString('&lt;script&gt;', $html);
        $this->assertStringContainsString('href="https://example.com/?a=1&amp;b="', $html);
    }

    public function test_only_http_and_https_are_linked(): void
    {
        $html = (string) AutoLink::html('javascript:alert(1) ftp://example.com');

        $this->assertStringNotContainsString('<a', $html);
    }

    public function test_trailing_punctuation_is_not_part_of_the_link(): void
    {
        $this->assertSame(
            '（参考：'.$this->link('https://tailadmin.com/').'）、'.$this->link('https://apexcharts.com/').'。',
            (string) AutoLink::html('（参考：https://tailadmin.com/）、https://apexcharts.com/。'),
        );
        $this->assertSame(
            'see '.$this->link('https://example.com').'.',
            (string) AutoLink::html('see https://example.com.'),
        );
    }

    public function test_site_paths_become_links_in_the_same_tab(): void
    {
        $this->assertSame(
            '後継版はこちら：<a href="'.url('/works/coffee-inventory-v2').'">/works/coffee-inventory-v2</a>。',
            (string) AutoLink::html('後継版はこちら：/works/coffee-inventory-v2。'),
        );
        $this->assertSame(
            '（<a href="'.url('/posts/hello').'">/posts/hello</a>）',
            (string) AutoLink::html('（/posts/hello）'),
        );
    }

    public function test_site_paths_link_under_the_subfolder_the_site_lives_in(): void
    {
        URL::useOrigin('http://example.com/portfolio');

        $this->assertSame(
            '<a href="http://example.com/portfolio/works/foo">/works/foo</a>',
            (string) AutoLink::html('/works/foo'),
        );
    }

    public function test_site_paths_inside_urls_or_other_paths_are_not_linked_separately(): void
    {
        $this->assertSame(
            $this->link('https://example.com/works/foo'),
            (string) AutoLink::html('https://example.com/works/foo'),
        );
        $this->assertSame('admin/works/foo /admin/works/foo', (string) AutoLink::html('admin/works/foo /admin/works/foo'));
    }

    public function test_multiple_urls_on_separate_lines(): void
    {
        $this->assertSame(
            $this->link('https://a.example')."\n".$this->link('http://b.example'),
            (string) AutoLink::html("https://a.example\nhttp://b.example"),
        );
    }
}
