<?php

class Zen_RSS_Generator_Channel
{

    public function render()
    {
        if (!get_option('zen_rss_channel_slug')) {
            return;
        }

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
                <language><?php bloginfo_rss('language'); ?></language>

                <?php
                $args = array(
                    'post_type' => 'post',
                    'post_status' => 'publish',
                    'posts_per_page' => get_option('zen_rss_channel_count', 50),
                    'date_query' => array(
                        array(
                            'after' => get_option('zen_rss_channel_max_age', 30) . ' days ago',
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

                    // Category
                    $categories = get_the_category();
                    $category = !empty($categories) ? $categories[0]->name : 'native-draft'; // Default to draft if no cat? Or just 'News'
        
                    // Content
                    $content_raw = get_the_content();
                    $content_clean = Zen_RSS_Text_Cleaner::clean_for_channel($content_raw);

                    // Inject Related Posts
                    if (get_option('zen_rss_channel_related')) {
                        $content_clean = Zen_RSS_Block_Related::inject_related($content_clean, $post_id);
                    }

                    // Enclosure
                    $thumbnail_id = get_post_thumbnail_id();
                    $enclosure_url = '';
                    $enclosure_type = '';

                    if ($thumbnail_id && get_option('zen_rss_channel_thumbnails')) {
                        $img = wp_get_attachment_image_src($thumbnail_id, 'full');
                        if ($img) {
                            $enclosure_url = $img[0];
                            $enclosure_type = get_post_mime_type($thumbnail_id);
                        }
                    } elseif (get_option('zen_rss_channel_thumbnails')) {
                        // Try to find image in content
                        $first_img = Zen_RSS_Text_Cleaner::get_first_image($content_raw);
                        if ($first_img) {
                            $enclosure_url = $first_img;
                            $enclosure_type = 'image/jpeg';
                        }
                    }

                    ?>
                    <item>
                        <title><?php echo $title; ?></title>
                        <link><?php echo $link; ?></link>
                        <guid><?php echo $guid; ?></guid>
                        <pubDate><?php echo $pubDate; ?></pubDate>
                        <dc:creator><?php echo $author; ?></dc:creator>
                        <category><?php echo $category; ?></category>
                        <?php if ($enclosure_url): ?>
                            <enclosure url="<?php echo esc_url($enclosure_url); ?>"
                                type="<?php echo esc_attr($enclosure_type); ?>" />
                        <?php endif; ?>
                        <content:encoded><![CDATA[<?php echo $content_clean; ?>]]></content:encoded>
                    </item>
                <?php endwhile;
                wp_reset_postdata(); ?>
            </channel>
        </rss>
        <?php
    }
}
