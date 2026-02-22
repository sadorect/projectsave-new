<?php

namespace App\Services;

/**
 * HTML Sanitizer Service
 *
 * Sanitizes rich-text HTML before saving to the database to prevent
 * stored XSS attacks. This is a zero-dependency implementation using
 * PHP's built-in DOMDocument.
 *
 * For production, consider replacing with `mews/purifier` or `ezyang/htmlpurifier`
 * for a more battle-tested and configurable solution:
 *
 *   composer require mews/purifier
 *
 * Usage in controllers:
 *   use App\Services\HtmlSanitizer;
 *   $clean = HtmlSanitizer::clean($request->input('description'));
 */
class HtmlSanitizer
{
    /**
     * Allowed HTML elements and their permitted attributes.
     */
    protected static array $allowedTags = [
        'p'          => [],
        'br'         => [],
        'strong'     => [],
        'em'         => [],
        'u'          => [],
        's'          => [],
        'ul'         => [],
        'ol'         => [],
        'li'         => [],
        'blockquote' => [],
        'h1'         => [],
        'h2'         => [],
        'h3'         => [],
        'h4'         => [],
        'h5'         => [],
        'h6'         => [],
        'a'          => ['href', 'title', 'target'],
        'img'        => ['src', 'alt', 'title', 'width', 'height'],
        'table'      => ['class'],
        'thead'      => [],
        'tbody'      => [],
        'tr'         => [],
        'th'         => ['colspan', 'rowspan'],
        'td'         => ['colspan', 'rowspan'],
        'span'       => ['class'],
        'div'        => ['class'],
        'pre'        => [],
        'code'       => [],
    ];

    /**
     * Sanitize an HTML string, stripping disallowed tags and attributes.
     *
     * @param  string|null  $html
     * @return string
     */
    public static function clean(?string $html): string
    {
        if (empty($html)) {
            return '';
        }

        // Use strip_tags with an allowlist as a first pass
        $allowedTagString = '<' . implode('><', array_keys(static::$allowedTags)) . '>';
        $stripped = strip_tags($html, $allowedTagString);

        // Second pass: strip disallowed attributes using DOMDocument
        libxml_use_internal_errors(true);
        $dom = new \DOMDocument();
        $dom->loadHTML(
            '<?xml encoding="utf-8" ?><div>' . $stripped . '</div>',
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
        );
        libxml_clear_errors();

        static::stripDisallowedAttributes($dom);

        // Extract inner content of the wrapper div
        $wrapper = $dom->getElementsByTagName('div')->item(0);
        $result  = '';

        if ($wrapper) {
            foreach ($wrapper->childNodes as $child) {
                $result .= $dom->saveHTML($child);
            }
        }

        return $result;
    }

    /**
     * Strictly escape to plain text (use for contexts where no HTML is expected).
     */
    public static function plainText(?string $input): string
    {
        return htmlspecialchars((string) $input, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    /**
     * Walk DOM and remove attributes not in the allowed list.
     */
    protected static function stripDisallowedAttributes(\DOMDocument $dom): void
    {
        $xpath = new \DOMXPath($dom);

        foreach ($xpath->query('//*[@*]') as $node) {
            /** @var \DOMElement $node */
            $tagName = strtolower($node->nodeName);
            $allowed = static::$allowedTags[$tagName] ?? [];

            $attrsToRemove = [];
            foreach ($node->attributes as $attr) {
                $attrName = strtolower($attr->nodeName);

                // Block all event handler attributes (onclick, onerror, etc.)
                if (str_starts_with($attrName, 'on')) {
                    $attrsToRemove[] = $attrName;
                    continue;
                }

                // Block javascript: URIs in href/src
                if (in_array($attrName, ['href', 'src'], true)) {
                    $value = trim(strtolower($attr->nodeValue));
                    if (str_starts_with(str_replace([' ', "\t", "\n"], '', $value), 'javascript:')) {
                        $attrsToRemove[] = $attrName;
                        continue;
                    }
                }

                if (!in_array($attrName, $allowed, true)) {
                    $attrsToRemove[] = $attrName;
                }
            }

            foreach ($attrsToRemove as $attrName) {
                $node->removeAttribute($attrName);
            }
        }
    }
}
