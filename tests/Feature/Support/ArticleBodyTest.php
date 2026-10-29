<?php

namespace Tests\Feature\Support;

use App\Support\ArticleBody;
use Tests\TestCase;

class ArticleBodyTest extends TestCase
{
    public function test_headings_lists_and_paragraphs(): void
    {
        $text = "1行目\n2行目\n【工夫した点】\n・項目1\n・ 項目2\n【感想】\nおわり";

        $this->assertSame(
            '<p>1行目'."\n".'2行目</p>'
            .'<h2 class="article-body__heading">工夫した点</h2>'
            .'<ul class="article-body__list"><li>項目1</li><li>項目2</li></ul>'
            .'<h2 class="article-body__heading">感想</h2>'
            .'<p>おわり</p>',
            (string) ArticleBody::html($text),
        );
    }

    public function test_crlf_and_blank_lines_split_paragraphs(): void
    {
        $this->assertSame('<p>段落1</p><p>段落2</p>', (string) ArticleBody::html("段落1\r\n\r\n段落2\r\n"));
    }

    public function test_text_after_a_list_starts_a_new_paragraph(): void
    {
        $this->assertSame(
            '<ul class="article-body__list"><li>項目</li></ul><p>※ 注記</p>',
            (string) ArticleBody::html("・項目\n※ 注記"),
        );
    }

    public function test_brackets_inside_a_line_are_not_headings(): void
    {
        $this->assertSame('<p>【注意】は本文の一部です</p>', (string) ArticleBody::html('【注意】は本文の一部です'));
    }

    public function test_links_and_escaping_inside_blocks(): void
    {
        $html = (string) ArticleBody::html("【<b>見出し</b>】\n・<script>x</script> /works/photo-map\n参考：https://example.com/");

        $this->assertStringContainsString('<h2 class="article-body__heading">&lt;b&gt;見出し&lt;/b&gt;</h2>', $html);
        $this->assertStringContainsString('<li>&lt;script&gt;x&lt;/script&gt; <a href="'.url('/works/photo-map').'">/works/photo-map</a></li>', $html);
        $this->assertStringContainsString('<a href="https://example.com/" target="_blank" rel="noopener noreferrer">', $html);
    }
}
