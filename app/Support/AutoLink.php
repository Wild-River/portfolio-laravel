<?php

namespace App\Support;

use Illuminate\Support\HtmlString;

/**
 * Turns http(s) URLs and site paths (/works/..., /posts/...) in plain text into links, escaping everything else.
 */
class AutoLink
{
    /**
     * A URL runs until whitespace, quotes, angle brackets or Japanese punctuation.
     * A site path is /works/{slug} or /posts/{slug} that is not part of a longer path or word.
     */
    private const URL_PATTERN = '~(https?://[^\s<>"\'（）「」『』、。，．！？]+|(?<![A-Za-z0-9_./:-])/(?:works|posts)/[A-Za-z0-9-]+)~u';

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

            // Site paths open in the same tab; external URLs open in a new one.
            // url() keeps the link working when the site lives in a subfolder (/portfolio/works/...).
            if (str_starts_with($part, '/')) {
                $html .= '<a href="'.e(url($part)).'">'.e($part).'</a>';

                continue;
            }

            $url = rtrim($part, self::TRAILING_PUNCTUATION);
            $trailing = substr($part, strlen($url));

            $html .= '<a href="'.e($url).'" target="_blank" rel="noopener noreferrer">'.e($url).'</a>'.e($trailing);
        }

        return new HtmlString($html);
    }
}
