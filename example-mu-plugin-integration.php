<?php
/**
 * Example MU-Plugin Integration for Zen RSS
 * 
 * Add this code to your existing MU-plugin that generates OG:image tags
 * to ensure the Zen RSS plugin uses the correct image URL.
 */

/**
 * Provide OG:image URL to Zen RSS plugin
 * 
 * @param string $og_image Current OG image URL (empty by default)
 * @param int $post_id Post ID
 * @return string OG image URL
 */
add_filter('zen_rss_og_image', function ($og_image, $post_id) {
    // If already set by another filter, return it
    if (!empty($og_image)) {
        return $og_image;
    }

    // Get your custom OG:image URL here
    // Example: if you store it in post meta
    $custom_og = get_post_meta($post_id, 'your_custom_og_image_key', true);

    // Or if you generate it dynamically based on featured image
    if (empty($custom_og) && has_post_thumbnail($post_id)) {
        $thumbnail_id = get_post_thumbnail_id($post_id);
        // Get the full-size image URL
        $image_data = wp_get_attachment_image_src($thumbnail_id, 'full');
        if ($image_data) {
            $custom_og = $image_data[0];

            // If you have a specific OG image size, use that instead
            // Example: $custom_og = get_the_post_thumbnail_url($post_id, 'og-image-size');
        }
    }

    return $custom_og;
}, 10, 2);
