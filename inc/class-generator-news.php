<?php

class Zen_RSS_Generator_News
{

    public function render()
    {
        if (!get_option('zen_rss_news_slug')) {
            return;
        }

        // Check cache first (if enabled)
        if (Zen_RSS_Cache_Manager::is_cache_enabled()) {
            $cached = Zen_RSS_Cache_Manager::get_cached_feed('news');
            if ($cached !== false) {
                header('Content-Type: application/rss+xml; charset=UTF-8', true);
                header('X-Zen-Feed: cached');
                echo $cached;
                return;
            }
        }

        // Start output buffering for caching
        

        // Strict 8 days limit for News
        $max_days = 8;
        // User setting can only lower it, not increase it beyond 8
        $user_days = (int) get_option('zen_rss_news_max_age', 3);
        if ($user_days > 8) {
            $user_days = 8;
        }

        // Cap at 500 items maximum
        $item_count = min(500, (int) get_option('zen_rss_news_count', 50));

        header('Content-Type: application/rss+xml; charset=UTF-8', true);
        header('X-Zen-Feed: fresh');
        echo '<?xml version="1.0" encoding="UTF-8"?' . '>';
		ob_start();
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
                    'posts_per_page' => $item_count,
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

        // Cache the output (if enabled)
        $output = ob_get_clean();
        if (Zen_RSS_Cache_Manager::is_cache_enabled()) {
            Zen_RSS_Cache_Manager::set_cached_feed('news', $output);
        }
        echo $output;
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
     * Clean content for Zen News (yandex:full-text)
     * Zen требует ЧИСТЫЙ ТЕКСТ без HTML-тегов
     */
    private static function clean_content_strict($content)
    {
        if (!$content) {
            return '';
        }

        // Убираем шорткоды
        $content = strip_shortcodes($content);

        // Убираем скрипты, стили, формы и т.д.
        $content = preg_replace('/<(script|style|iframe|embed|object|form)[^>]*>.*?<\/\1>/si', '', $content);

        // Заменяем заголовки на текст с переводами строк
        $content = preg_replace('/<h[1-6][^>]*>(.*?)<\/h[1-6]>/si', "\n\n$1\n", $content);

        // Заменяем параграфы на текст с двойным переводом строки
        $content = preg_replace('/<p[^>]*>(.*?)<\/p>/si', "$1\n\n", $content);

        // Заменяем <br> на перевод строки
        $content = preg_replace('/<br\s*\/?>/i', "\n", $content);

        // Заменяем списки на текст с переводами строк
        $content = preg_replace('/<li[^>]*>(.*?)<\/li>/si', "- $1\n", $content);
        $content = preg_replace('/<\/?[uo]l[^>]*>/i', "\n", $content);

        // Заменяем blockquote на текст с отступом
        $content = preg_replace('/<blockquote[^>]*>(.*?)<\/blockquote>/si', "\n$1\n", $content);

        // Убираем ВСЕ оставшиеся HTML-теги
        $content = strip_tags($content);

        // Декодируем HTML-сущности
        $content = html_entity_decode($content, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        // ВАЖНО: Экранируем XML-сущности для валидного RSS
        $content = htmlspecialchars($content, ENT_XML1 | ENT_COMPAT, 'UTF-8', false);

        // Нормализуем пробелы: убираем множественные пробелы
        $content = preg_replace('/[ \t]+/', ' ', $content);

        // Нормализуем переводы строк: максимум 2 подряд
        $content = preg_replace('/\n{3,}/', "\n\n", $content);

        // Убираем пробелы в начале и конце строк
        $lines = explode("\n", $content);
        $lines = array_map('trim', $lines);
        $content = implode("\n", $lines);

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

        // 1. Allow external override via filter (for MU-plugins that add OG:image to HTML)
        $og_image = apply_filters('zen_rss_og_image', '', $post_id);

        // 2. Open Graph (Yoast / RankMath / Generic)
        if (!$og_image) {
            $og_image = get_post_meta($post_id, '_yoast_wpseo_opengraph-image', true); // Yoast
        }
        if (!$og_image) {
            $og_image = get_post_meta($post_id, 'rank_math_facebook_image', true); // RankMath
        }
        // Try generic 'og_image' if custom field exists
        if (!$og_image) {
            $og_image = get_post_meta($post_id, 'og_image', true);
        }

        // If still no OG image from meta, try to parse from rendered HTML
        if (!$og_image) {
            // Get the post permalink and fetch its HTML to extract og:image
            // This is a last resort and may be slow, but ensures we get the right image
            $post_content_full = get_post($post_id);
            if ($post_content_full) {
                // Try to get og:image from wp_head output
                // This requires rendering the post, which is expensive
                // Better approach: check if there's a filter or action we can use
                // For now, skip this and rely on Featured Image as fallback
            }
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

            // Accept JPEG/JPG/PNG/WebP directly - no conversion attempts
            if (in_array($ext, array('jpg', 'jpeg', 'png', 'webp'))) {
                $type = ($ext === 'png') ? 'image/png' : 'image/jpeg';
                return array(
                    'url' => $url,
                    'type' => $type,
                );
            }

            // Skip anything else (AVIF, etc.) and try next candidate
        }

        return null;
    }
}
