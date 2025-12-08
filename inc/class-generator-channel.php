<?php

class Zen_RSS_Generator_Channel
{

    public function render()
    {
        if (!get_option('zen_rss_channel_slug')) {
            return;
        }

        // Check cache first (if enabled)
        if (Zen_RSS_Cache_Manager::is_cache_enabled()) {
            $cached = Zen_RSS_Cache_Manager::get_cached_feed('channel');
            if ($cached !== false) {
                header('Content-Type: application/rss+xml; charset=' . get_option('blog_charset'), true);
                echo $cached;
                return;
            }
        }

        // Start output buffering for caching
        ob_start();

        header('Content-Type: application/rss+xml; charset=' . get_option('blog_charset'), true);
        echo '<?xml version="1.0" encoding="' . get_option('blog_charset') . '"?' . '>';
        ?>
        <rss version="2.0" xmlns:content="http://purl.org/rss/1.0/modules/content/" xmlns:dc="http://purl.org/dc/elements/1.1/"
            xmlns:media="http://search.yahoo.com/mrss/" xmlns:atom="http://www.w3.org/2005/Atom"
            xmlns:georss="http://www.georss.org/georss">
            <channel>
                <title><?php bloginfo_rss('name'); ?></title>
                <link><?php bloginfo_rss('url'); ?></link>
                <description><?php bloginfo_rss('description'); ?></description>
                <language>ru</language>

                <?php
                // Default to 3 days for Channel (can be overridden by user)
                $default_age = 3;
                $user_age = (int) get_option('zen_rss_channel_max_age', $default_age);

                $args = array(
                    'post_type' => 'post',
                    'post_status' => 'publish',
                    'posts_per_page' => get_option('zen_rss_channel_count', 50),
                    'date_query' => array(
                        array(
                            'after' => $user_age . ' days ago',
                        ),
                    ),
                );

                $query = new WP_Query($args);

                while ($query->have_posts()):
                    $query->the_post();
                    $post_id = get_the_ID();

                    // Skip if password protected
                    if (post_password_required()) {
                        continue;
                    }

                    $title = get_the_title_rss();
                    $link = get_permalink();
                    $guid = get_the_guid();
                    $pubDate = mysql2date('D, d M Y H:i:s +0000', get_post_time('Y-m-d H:i:s', true), false);
                    $author = get_the_author();

                    // Content
                    $content_raw = get_the_content();
                    $content_clean = Zen_RSS_Text_Cleaner::clean_for_channel($content_raw);

                    // Convert AVIF/WebP images to JPEG in content
                    $content_clean = self::convert_images_to_jpeg($content_clean);

                    // Simplify figure markup
                    $content_clean = self::simplify_figures($content_clean);

                    // Inject Related Posts
                    if (get_option('zen_rss_channel_related')) {
                        $content_clean = Zen_RSS_Block_Related::inject_related($content_clean, $post_id);
                    }

                    // Inject Custom Content Block
                    if (get_option('zen_rss_custom_content_enable')) {
                        $custom_html = get_option('zen_rss_custom_content_html');
                        $custom_pos = (int) get_option('zen_rss_custom_content_position', 3);

                        if (!empty($custom_html) && class_exists('Zen_RSS_Injector')) {
                            // We use a slightly different position logic or just reuse the injector
                            // If related posts are at pos 2, and custom at pos 3, it should work fine sequentially
                            $content_clean = Zen_RSS_Injector::inject($content_clean, $custom_html, $custom_pos);
                        }
                    }

                    // Add source attribution at the end
                    $site_name = get_bloginfo('name');
                    $content_clean .= PHP_EOL . '<p><strong>Источник:</strong> <a href="' . esc_url($link) . '">' . esc_html($site_name) . '</a></p>';

                    // Image Logic (Strict JPEG/PNG, OG priority)
                    $image_data = self::get_best_image($post_id, $content_raw);

                    ?>
                    <item>
                        <title><?php echo $title; ?></title>
                        <link><?php echo $link; ?></link>
                        <guid><?php echo $guid; ?></guid>
                        <pubDate><?php echo $pubDate; ?></pubDate>
                        <dc:creator><?php echo $author; ?></dc:creator>

                        <category>format-article</category>
                        <category>index</category>
                        <category>comment-all</category>
                        <?php
                        // Optional: Add original category as well
                        $cats = get_the_category();
                        if (!empty($cats)) {
                            echo '<category>' . esc_html($cats[0]->name) . '</category>';
                        }
                        ?>

                        <?php if ($image_data): ?>
                            <enclosure url="<?php echo esc_url($image_data['url']); ?>"
                                type="<?php echo esc_attr($image_data['type']); ?>" />
                        <?php endif; ?>
                        <content:encoded><![CDATA[<?php echo $content_clean; ?>]]></content:encoded>
                    </item>
                <?php endwhile;
                wp_reset_postdata(); ?>
            </channel>
        </rss>
        <?php

        // Cache the output (if enabled)
        $output = ob_get_clean();
        if (Zen_RSS_Cache_Manager::is_cache_enabled()) {
            Zen_RSS_Cache_Manager::set_cached_feed('channel', $output);
        }
        echo $output;
    }

    /**
     * Convert AVIF/WebP images to JPEG in content
     *
     * @param string $content
     * @return string
     */
    private static function convert_images_to_jpeg($content)
    {
        // Replace .avif and .webp with .jpg in src attributes
        $content = preg_replace('/(<img[^>]+src=["\'])([^"\']+\.(avif|webp))(["\'][^>]*>)/i', '$1' . '$2' . '$4', $content);
        $content = preg_replace('/\.(avif|webp)(["\'][^>]*>)/i', '.jpg$2', $content);

        // Replace in href attributes
        $content = preg_replace('/(<a[^>]+href=["\'])([^"\']+\.(avif|webp))(["\'][^>]*>)/i', '$1' . '$2' . '$4', $content);
        $content = preg_replace('/(<a[^>]+href=["\'][^"\']+)\.(avif|webp)(["\'])/i', '$1.jpg$3', $content);

        return $content;
    }

    /**
     * Simplify figure markup for Zen compatibility
     *
     * @param string $content
     * @return string
     */
    private static function simplify_figures($content)
    {
        // Remove figure classes (wp-block-*, size-*, etc.)
        $content = preg_replace('/<figure[^>]+class="[^"]*"([^>]*)>/i', '<figure$1>', $content);

        // Remove nested figures (flatten them)
        $content = preg_replace('/<figure[^>]*>\s*<figure[^>]*>/i', '<figure>', $content);
        $content = preg_replace('/<\/figure>\s*<\/figure>/i', '</figure>', $content);

        // Remove anchor wrapping around images inside figures
        $content = preg_replace('/<figure>(\s*)<a[^>]*>(\s*<img[^>]*>)\s*<\/a>/i', '<figure>$1$2', $content);

        // Clean up any remaining wp-* classes in img tags
        $content = preg_replace('/(<img[^>]+)class="[^"]*wp-[^"]*"([^>]*>)/i', '$1$2', $content);

        return $content;
    }

    /**
     * Get best image (OG -> Featured -> Content)
     * Enforce JPEG/PNG and size
     * (Duplicated from News Generator for isolation)
     */
    private static function get_best_image($post_id, $content)
    {
        if (!get_option('zen_rss_channel_thumbnails')) {
            return null;
        }

        $candidates = array();

        // 1. Open Graph (Yoast / RankMath / Generic)
        $og_image = get_post_meta($post_id, '_yoast_wpseo_opengraph-image', true); // Yoast
        if (!$og_image) {
            $og_image = get_post_meta($post_id, 'rank_math_facebook_image', true); // RankMath
        }
        if (!$og_image) {
            $og_image = get_post_meta($post_id, 'og_image', true);
        }

        if ($og_image) {
            $candidates[] = $og_image;
        }

        // 2. Featured Image
        $thumb_id = get_post_thumbnail_id($post_id);
        if ($thumb_id) {
            $img_src = wp_get_attachment_image_src($thumb_id, 'full');
            if ($img_src) {
                $candidates[] = $img_src[0];
            }
        }

        // 3. Content Image
        if (preg_match('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $content, $matches)) {
            $candidates[] = $matches[1];
        }

        // Process candidates
        foreach ($candidates as $url) {
            $ext = strtolower(pathinfo($url, PATHINFO_EXTENSION));

            // If AVIF/WEBP, try to find JPG replacement
            if (in_array($ext, array('avif', 'webp'))) {
                $jpg_url = preg_replace('/\.(avif|webp)$/i', '.jpg', $url);
                // Optimistic replacement
                $url = $jpg_url;
                $ext = 'jpg';
            }

            if (in_array($ext, array('jpg', 'jpeg'))) {
                return array(
                    'url' => $url,
                    'type' => 'image/jpeg',
                );
            }

            if ($ext === 'png') {
                return array(
                    'url' => $url,
                    'type' => 'image/png',
                );
            }
        }

        return null;
    }
}
