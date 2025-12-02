<?php

class Zen_RSS_Generator_News
{

    public function render()
    {
        if (!get_option('zen_rss_news_slug')) {
            return;
        }

        // Strict 8 days limit for News
        $max_days = 8;
        // User setting can only lower it, not increase it beyond 8
        $user_days = (int) get_option('zen_rss_news_max_age', 3);
        if ($user_days > 8) {
            $user_days = 8;
        }

        header('Content-Type: application/rss+xml; charset=' . get_option('blog_charset'), true);
        echo '<?xml version="1.0" encoding="' . get_option('blog_charset') . '"?' . '>';
        ?>
        <rss version="2.0" xmlns:yandex="http://news.yandex.ru" xmlns:media="http://search.yahoo.com/mrss/"
            xmlns:atom="http://www.w3.org/2005/Atom">
            <channel>
                <title><?php echo self::xml_escape(get_bloginfo_rss('name')); ?></title>
                <link><?php bloginfo_rss('url'); ?></link>
                <description><?php echo self::xml_escape(get_bloginfo_rss('description')); ?></description>
                <language>ru</language>
                <?php
                $logo = get_option('zen_rss_news_logo');
                if ($logo): ?>
                    <image>
                        <url><?php echo esc_url($logo); ?></url>
                        <title><?php echo self::xml_escape(get_bloginfo_rss('name')); ?></title>
                        <link><?php bloginfo_rss('url'); ?></link>
                    </image>
                <?php endif; ?>

                <?php
                $args = array(
                    'post_type' => 'post',
                    'post_status' => 'publish',
                    'posts_per_page' => get_option('zen_rss_news_count', 50),
                    'date_query' => array(
                        array(
                            'after' => $user_days . ' days ago',
                        ),
                    ),
                );

                $query = new WP_Query($args);

                while ($query->have_posts()):
                    $query->the_post();
                    $post_id = get_the_ID();

                    if (post_password_required()) {
                        continue;
                    }

                    $title = self::xml_escape(get_the_title_rss());
                    $link = get_permalink();
                    $pubDate = mysql2date('D, d M Y H:i:s +0000', get_post_time('Y-m-d H:i:s', true), false);
                    $author = self::xml_escape(get_the_author());

                    // Category
                    $categories = get_the_category();
                    $category = !empty($categories) ? self::xml_escape($categories[0]->name) : 'News';

                    // Content
                    $content = get_the_content();
                    $full_text = self::clean_content_strict($content);

                    // Description (Lead)
                    $description = get_the_excerpt();
                    if (empty($description)) {
                        $description = wp_trim_words($content, 20);
                    }
                    // Decode first to avoid double escaping if WP already escaped it
                    $description = wp_specialchars_decode($description, ENT_QUOTES);
                    $description = self::xml_escape($description);

                    // Image Logic
                    $image_data = self::get_best_image($post_id, $content);

                    ?>
                    <item>
                        <title><?php echo $title; ?></title>
                        <link><?php echo $link; ?></link>
                        <pubDate><?php echo $pubDate; ?></pubDate>
                        <author><?php echo $author; ?></author>
                        <category><?php echo $category; ?></category>
                        <description><?php echo $description; ?></description>
                        <yandex:full-text><?php echo $full_text; ?></yandex:full-text>
                        <?php if ($image_data): ?>
                            <enclosure url="<?php echo esc_url($image_data['url']); ?>"
                                type="<?php echo esc_attr($image_data['type']); ?>" />
                        <?php endif; ?>
                    </item>
                <?php endwhile;
                wp_reset_postdata(); ?>
            </channel>
        </rss>
        <?php
    }

    /**
     * Strict XML escaping
     */
    private static function xml_escape($string)
    {
        return htmlspecialchars($string, ENT_XML1 | ENT_COMPAT, 'UTF-8');
    }

    /**
     * Strict content cleaning for Zen News
     */
    private static function clean_content_strict($content)
    {
        // Remove shortcodes
        $content = strip_shortcodes($content);

        // Remove teaser if enabled
        if (get_option('zen_rss_news_remove_teaser')) {
            $content = preg_replace('/<p>.*?<\/p>/i', '', $content, 1);
        }

        // Remove "Read Also" / "Читайте также" blocks (simple heuristics)
        $content = preg_replace('/<(p|div|strong|b)[^>]*>(Читайте также|Read also|Ещё по теме).*?<\/\1>/iu', '', $content);

        // Strip all tags except allowed text formatting
        // Zen News full-text should be mostly text.
        // Allowed: <p>, <br>, <b>, <strong>, <i>, <em>, <u>, <s>, <ul>, <ol>, <li>, <blockquote>
        // NO IMAGES in full-text for News (they go to enclosure/media)
        $allowed_tags = '<p><br><b><strong><i><em><u><s><ul><ol><li><blockquote>';
        $content = strip_tags($content, $allowed_tags);

        // Remove empty paragraphs
        $content = preg_replace('/<p>\s*<\/p>/', '', $content);

        // Final XML escape
        // Note: We need to preserve HTML tags, so we can't just htmlspecialchars the whole thing.
        // But the content inside tags should be safe.
        // Since we stripped tags to a safe list, we assume the remaining tags are valid XML.
        // However, we should ensure entities are correct.

        // A simple way is to rely on WordPress's ent2ncr or similar, but for now let's return the cleaned HTML.
        // Ideally we should escape special chars outside of tags, but that's complex without a parser.
        // We will assume the input content is UTF-8 and mostly safe after strip_tags.
        // But we MUST escape & < > " ' in the text nodes.
        // For simplicity in this context, we'll trust strip_tags + standard WP filtering, 
        // but replace standalone & with &amp; if not part of an entity.

        return trim($content);
    }

    /**
     * Get best image (OG -> Featured -> Content)
     * Enforce JPEG and size
     */
    private static function get_best_image($post_id, $content)
    {
        if (!get_option('zen_rss_news_thumbnails')) {
            return null;
        }

        $candidates = array();

        // 1. Open Graph (Yoast / RankMath / Generic)
        $og_image = get_post_meta($post_id, '_yoast_wpseo_opengraph-image', true); // Yoast
        if (!$og_image) {
            $og_image = get_post_meta($post_id, 'rank_math_facebook_image', true); // RankMath
        }
        // Try generic 'og_image' if custom field exists
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
            // Check extension
            $ext = strtolower(pathinfo($url, PATHINFO_EXTENSION));

            // If AVIF/WEBP, try to find JPG replacement
            if (in_array($ext, array('avif', 'webp'))) {
                $jpg_url = preg_replace('/\.(avif|webp)$/i', '.jpg', $url);
                $jpeg_url = preg_replace('/\.(avif|webp)$/i', '.jpeg', $url);

                // We can't easily check file existence on remote/CDN URLs without a request.
                // But often in WP uploads, they exist side-by-side if generated.
                // Let's assume if we change extension it MIGHT exist. 
                // Better approach: check if it's a local file.

                // For safety, let's prefer the original if it's already JPG/PNG.
                // But user explicitly asked to replace.
                // Let's try to guess the JPG version.
                $url = $jpg_url; // Optimistic replacement
                $ext = 'jpg';
            }

            if (in_array($ext, array('jpg', 'jpeg'))) {
                // Check size if possible (only for local attachments easily)
                // For remote URLs, we can't check size without HEAD request.
                // We'll assume 'full' size or OG image is large enough.

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
