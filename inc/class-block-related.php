<?php

class Zen_RSS_Block_Related
{

    /**
     * Get related posts HTML
     *
     * @param int $post_id
     * @return string
     */
    public static function get_related_block($post_id)
    {
        if (!get_option('zen_rss_channel_related')) {
            return '';
        }

        $categories = wp_get_post_categories($post_id);
        if (empty($categories)) {
            return '';
        }

        $count = (int) get_option('zen_rss_related_count', 5);
        if ($count < 1)
            $count = 5;

        $args = array(
            'category__in' => $categories,
            'post__not_in' => array($post_id),
            'posts_per_page' => $count,
            'orderby' => 'rand',
            'post_status' => 'publish',
        );

        $query = new WP_Query($args);

        if (!$query->have_posts()) {
            return '';
        }

        $output = PHP_EOL . '<h3>Ещё по теме:</h3>' . PHP_EOL;
        $output .= '<ul>' . PHP_EOL;

        while ($query->have_posts()) {
            $query->the_post();
            $output .= '<li><a href="' . get_permalink() . '">' . get_the_title() . '</a></li>' . PHP_EOL;
        }

        $output .= '</ul>' . PHP_EOL;

        wp_reset_postdata();

        return $output;
    }

    /**
     * Inject related posts into content
     *
     * @param string $content
     * @param int $post_id
     * @return string
     */
    public static function inject_related($content, $post_id)
    {
        $related_html = self::get_related_block($post_id);
        if (empty($related_html)) {
            return $content;
        }

        // Get configurable position (default: 2)
        $position = max(1, (int) get_option('zen_rss_related_position', 2));

        if (class_exists('Zen_RSS_Injector')) {
            return Zen_RSS_Injector::inject($content, $related_html, $position);
        }

        return $content . $related_html; // Fallback
    }
}
