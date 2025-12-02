<?php

class Zen_RSS_Text_Cleaner
{

    /**
     * Clean content for Zen News
     *
     * @param string $content
     * @return string
     */
    public static function clean_for_news($content)
    {
        // Remove shortcodes if enabled
        if (get_option('zen_rss_news_remove_shortcodes')) {
            $content = strip_shortcodes($content);
        }

        // Remove teaser if enabled (first paragraph)
        if (get_option('zen_rss_news_remove_teaser')) {
            $content = preg_replace('/<p>.*?<\/p>/i', '', $content, 1);
        }

        // Remove unwanted tags for News (scripts, styles, forms, iframes, etc.)
        $content = preg_replace('/<(script|style|iframe|embed|object|form)[^>]*>.*?<\/\1>/si', '', $content);

        // Remove attributes like style, class, onclick, etc.
        // This is a basic cleanup; DOMDocument is robust but can be slow/memory intensive.
        // For now, we'll use a simpler approach or just rely on strip_tags if needed, 
        // but Zen News allows some HTML.
        // Allowed: p, br, strong, b, em, i, u, s, ul, ol, li, table, tr, td, th, blockquote, img

        $allowed_tags = '<p><br><strong><b><em><i><u><s><ul><ol><li><table><tr><td><th><blockquote><img>';
        $content = strip_tags($content, $allowed_tags);

        // Remove empty paragraphs
        $content = preg_replace('/<p>\s*<\/p>/', '', $content);

        return trim($content);
    }

    /**
     * Clean content for Zen Channel
     *
     * @param string $content
     * @return string
     */
    public static function clean_for_channel($content)
    {
        // Remove shortcodes if enabled
        if (get_option('zen_rss_channel_remove_shortcodes')) {
            $content = strip_shortcodes($content);
        }

        // Allowed tags for Channel:
        // p, a, b, i, u, s, h1-h4, blockquote, ul, ol, li, img, figure, figcaption
        $allowed_tags = '<p><a><b><i><u><s><h1><h2><h3><h4><blockquote><ul><ol><li><img><figure><figcaption>';

        // Note: We might want to keep some structure, but strip dangerous tags.
        $content = strip_tags($content, $allowed_tags);

        // Ensure images have absolute URLs (WordPress usually handles this, but good to ensure)

        return trim($content);
    }

    /**
     * Extract first image from content
     *
     * @param string $content
     * @return string|null URL of the image
     */
    public static function get_first_image($content)
    {
        if (preg_match('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $content, $matches)) {
            return $matches[1];
        }
        return null;
    }
}
