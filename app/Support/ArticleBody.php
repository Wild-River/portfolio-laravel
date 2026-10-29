<?php

namespace App\Support;

use Illuminate\Support\HtmlString;

/**
 * Turns plain text into article HTML: 【見出し】 lines become headings, ・ lines become lists,
 * and the remaining lines become paragraphs with their URLs linked.
 */
class ArticleBody
{
    /**
     * A heading is a line that is only 【...】.
     */
    private const HEADING_PATTERN = '/^【(.+)】$/u';

    /**
     * A list item is a line that starts with ・.
     */
    private const LIST_ITEM_PATTERN = '/^・\s*(.*)$/u';

    /**
     * Get the text as article HTML.
     */
    public static function html(string $text): HtmlString
    {
        $html = '';
        $paragraph = [];
        $list = [];

        // Blocks are joined without newlines because .article-body keeps whitespace (pre-wrap).
        $flush = function () use (&$html, &$paragraph, &$list): void {
            if ($paragraph !== []) {
                $html .= '<p>'.AutoLink::html(implode("\n", $paragraph)).'</p>';
                $paragraph = [];
            }

            if ($list !== []) {
                $html .= '<ul class="article-body__list">'.implode('', array_map(
                    fn (string $item) => '<li>'.AutoLink::html($item).'</li>',
                    $list,
                )).'</ul>';
                $list = [];
            }
        };

        foreach (preg_split('/\R/u', trim($text)) as $line) {
            $line = trim($line);

            if ($line === '') {
                $flush();
            } elseif (preg_match(self::HEADING_PATTERN, $line, $matches)) {
                $flush();
                $html .= '<h2 class="article-body__heading">'.e($matches[1]).'</h2>';
            } elseif (preg_match(self::LIST_ITEM_PATTERN, $line, $matches)) {
                if ($paragraph !== []) {
                    $flush();
                }
                $list[] = $matches[1];
            } else {
                if ($list !== []) {
                    $flush();
                }
                $paragraph[] = $line;
            }
        }

        $flush();

        return new HtmlString($html);
    }
}
