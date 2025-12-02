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

        $args = array(
            'category__in' => $categories,
            'post__not_in' => array($post_id),
            'posts_per_page' => 5,
            'orderby' => 'rand',
            'post_status' => 'publish',
        );

        $query = new WP_Query($args);

        if (!$query->have_posts()) {
            return '';
        }

        $output = PHP_EOL . '<h3>Ещё по теме:</h3>' . PHP_EOL;
        // Use <p><a>...</a></p> format for Zen Channel compliance
        while ($query->have_posts()) {
            $query->the_post();
            $output .= '<p><a href="' . get_permalink() . '">' . get_the_title() . '</a></p>' . PHP_EOL;
        }

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

        // Try to split by paragraphs
        // Using a more robust regex split to handle various p tag attributes or spacing
        $paragraphs = preg_split('/(<\/p>)/i', $content, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

        // $paragraphs array will look like: [0] => "Text...", [1] => "</p>", [2] => "Text...", [3] => "</p>"
        // We want to insert after the 2nd paragraph (so after index 3: p1, /p1, p2, /p2)

        $count = 0;
        $inserted = false;
        $new_content = '';

        foreach ($paragraphs as $chunk) {
            $new_content .= $chunk;
            if (strtolower($chunk) === '</p>') {
                $count++;
                if ($count === 2) {
                    $new_content .= $related_html;
                    $inserted = true;
                }
            }
        }

        // If we couldn't insert (e.g. less than 2 paragraphs), append to the end
        if (!$inserted) {
            $new_content .= $related_html;
        }

        return $new_content;
    }
}
