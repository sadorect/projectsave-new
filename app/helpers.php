<?php

if (!function_exists('routeHas')) {
    function routeHas($name) {
        return Route::has($name);
    }
}

/**
 * Sanitize rich-text HTML to prevent stored XSS.
 *
 * Use this before persisting any admin/user-submitted rich text (descriptions,
 * lesson content, blog post details, FAQ answers, event details, etc.).
 *
 * Usage in controllers:
 *   $post->details   = sanitize_html($request->details);
 *   $lesson->content = sanitize_html($request->content);
 *
 * For plain text fields with NO HTML expected, use sanitize_text() instead.
 */
if (!function_exists('sanitize_html')) {
    function sanitize_html(?string $html): string
    {
        return \App\Services\HtmlSanitizer::clean($html);
    }
}

/**
 * Strictly escape a value to plain text (no HTML allowed).
 *
 * Use for names, phone numbers, addresses, and any field where HTML is unexpected.
 */
if (!function_exists('sanitize_text')) {
    function sanitize_text(?string $input): string
    {
        return \App\Services\HtmlSanitizer::plainText($input);
    }
}
