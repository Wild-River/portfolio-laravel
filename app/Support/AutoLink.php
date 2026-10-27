<?php

namespace App\Support;

use Illuminate\Support\HtmlString;

/**
 * Turns http(s) URLs in plain text into links, escaping everything else.
 */
class AutoLink
{
    /**
     * A URL runs until whitespace, quotes, angle brackets or Japanese punctuation.
     */
    private const URL_PATTERN = '~(https?://[^\s<>"\'（）「」『』、。，．！？]+)~u';

    /**
     * Punctuation that ends a sentence rather than the URL.
     */
    private const TRAILING_PUNCTUATION = '.,!?:;)]';

    /**
     * Get the text as HTML with its URLs linked.
     */
    public static function html(string $text): HtmlString
    {
        $parts = preg_split(self::URL_PATTERN, $text, -1, PREG_SPLIT_DELIM_CAPTURE);
        $html = '';

        foreach ($parts as $index => $part) {
            // preg_split puts each captured URL at an odd index.
            if ($index % 2 === 0) {
                $html .= e($part);

                continue;
            }

            $url = rtrim($part, self::TRAILING_PUNCTUATION);
            $trailing = substr($part, strlen($url));

            $html .= '<a href="'.e($url).'" target="_blank" rel="noopener noreferrer">'.e($url).'</a>'.e($trailing);
        }

        return new HtmlString($html);
    }
}
