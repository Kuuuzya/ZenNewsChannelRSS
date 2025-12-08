<div class="wrap zen-rss-settings">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

    <?php
    // Cache cleared messages
    if (isset($_GET['cache_cleared'])):
        $feed_type = sanitize_text_field($_GET['cache_cleared']);
        $messages = array(
            'all' => __('Cache for all feeds cleared successfully!', 'zen-news-channel-rss'),
            'news' => __('News feed cache cleared successfully!', 'zen-news-channel-rss'),
            'channel' => __('Channel feed cache cleared successfully!', 'zen-news-channel-rss'),
        );
        $message = isset($messages[$feed_type]) ? $messages[$feed_type] : $messages['all'];
        ?>
        <div class="notice notice-success is-dismissible">
            <p><?php echo esc_html($message); ?></p>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['settings-updated'])): ?>
        <div class="notice notice-success is-dismissible">
            <p><?php _e('Settings saved successfully! Remember to clear the cache to see changes.', 'zen-news-channel-rss'); ?>
            </p>
        </div>
    <?php endif; ?>

    <!-- Tabs Navigation -->
    <h2 class="nav-tab-wrapper">
        <a href="#general" class="nav-tab nav-tab-active"
            onclick="zenRssSwitchTab(event, 'general')"><?php _e('General Settings', 'zen-news-channel-rss'); ?></a>
        <a href="#news" class="nav-tab"
            onclick="zenRssSwitchTab(event, 'news')"><?php _e('News Feed Settings', 'zen-news-channel-rss'); ?></a>
        <a href="#channel" class="nav-tab"
            onclick="zenRssSwitchTab(event, 'channel')"><?php _e('Channel Feed Settings', 'zen-news-channel-rss'); ?></a>
    </h2>

    <!-- Main Settings Form -->
    <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
        <input type="hidden" name="action" value="zen_rss_save_settings" />
        <?php wp_nonce_field('zen_rss_save_settings', 'zen_rss_save_nonce'); ?>

        <!-- General Tab -->
        <div id="tab-general" class="zen-rss-tab-content active">
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label
                            for="zen_rss_news_slug"><?php _e('News Feed URL Slug', 'zen-news-channel-rss'); ?></label>
                    </th>
                    <td>
                        <input type="text" id="zen_rss_news_slug" name="zen_rss_news_slug"
                            value="<?php echo esc_attr(get_option('zen_rss_news_slug', 'zen-news')); ?>"
                            class="regular-text" />
                        <p class="description">
                            <?php _e('Custom URL path for the News feed. Default: "zen-news"', 'zen-news-channel-rss'); ?><br>
                            <?php printf(__('Full URL: %s', 'zen-news-channel-rss'), '<code>' . site_url('/feed/' . get_option('zen_rss_news_slug', 'zen-news')) . '</code>'); ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label
                            for="zen_rss_channel_slug"><?php _e('Channel Feed URL Slug', 'zen-news-channel-rss'); ?></label>
                    </th>
                    <td>
                        <input type="text" id="zen_rss_channel_slug" name="zen_rss_channel_slug"
                            value="<?php echo esc_attr(get_option('zen_rss_channel_slug', 'zen-channel')); ?>"
                            class="regular-text" />
                        <p class="description">
                            <?php _e('Custom URL path for the Channel feed. Default: "zen-channel"', 'zen-news-channel-rss'); ?><br>
                            <?php printf(__('Full URL: %s', 'zen-news-channel-rss'), '<code>' . site_url('/feed/' . get_option('zen_rss_channel_slug', 'zen-channel')) . '</code>'); ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label
                            for="zen_rss_cache_duration"><?php _e('Cache Duration', 'zen-news-channel-rss'); ?></label>
                    </th>
                    <td>
                        <input type="number" id="zen_rss_cache_duration" name="zen_rss_cache_duration"
                            value="<?php echo esc_attr(get_option('zen_rss_cache_duration', 15)); ?>" min="0" max="1440"
                            class="small-text" />
                        <?php _e('minutes', 'zen-news-channel-rss'); ?>
                        <p class="description">
                            <?php _e('How long to cache RSS feeds before regenerating (0-1440 minutes / 24 hours).', 'zen-news-channel-rss'); ?><br>
                            <strong><?php _e('Set to 0 to disable caching completely.', 'zen-news-channel-rss'); ?></strong><br>
                            <?php _e('Recommended: 15-60 minutes for most sites. Caching improves performance and reduces server load.', 'zen-news-channel-rss'); ?>
                        </p>
                    </td>
                </tr>
            </table>
        </div>

        <!-- News Tab -->
        <div id="tab-news" class="zen-rss-tab-content">
            <p class="description" style="margin-bottom: 20px;">
                <?php _e('Configuration for Yandex Zen News feed. This feed follows strict Zen News requirements (8-day limit, JPEG images, clean text).', 'zen-news-channel-rss'); ?>
            </p>

            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="zen_rss_news_count"><?php _e('Number of Posts', 'zen-news-channel-rss'); ?></label>
                    </th>
                    <td>
                        <input type="number" id="zen_rss_news_count" name="zen_rss_news_count"
                            value="<?php echo esc_attr(get_option('zen_rss_news_count', 50)); ?>" min="1" max="500"
                            class="small-text" />
                        <p class="description">
                            <?php _e('How many posts to include in the News feed (1-500).', 'zen-news-channel-rss'); ?><br>
                            <strong><?php _e('Zen requirement: Maximum 500 items.', 'zen-news-channel-rss'); ?></strong><br>
                            <?php _e('Recommended: 30-50 posts.', 'zen-news-channel-rss'); ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label
                            for="zen_rss_news_max_age"><?php _e('Maximum Age (Days)', 'zen-news-channel-rss'); ?></label>
                    </th>
                    <td>
                        <input type="number" id="zen_rss_news_max_age" name="zen_rss_news_max_age"
                            value="<?php echo esc_attr(get_option('zen_rss_news_max_age', 8)); ?>" min="1" max="8"
                            class="small-text" />
                        <p class="description">
                            <?php _e('Only include posts from the last N days (1-8).', 'zen-news-channel-rss'); ?><br>
                            <strong><?php _e('Zen News requirement: Maximum 8 days.', 'zen-news-channel-rss'); ?></strong><br>
                            <?php _e('Recommended: 3 days for fresh news content.', 'zen-news-channel-rss'); ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="zen_rss_news_logo"><?php _e('Logo URL', 'zen-news-channel-rss'); ?></label>
                    </th>
                    <td>
                        <input type="url" id="zen_rss_news_logo" name="zen_rss_news_logo"
                            value="<?php echo esc_url(get_option('zen_rss_news_logo')); ?>" class="regular-text"
                            placeholder="https://example.com/logo.jpg" />
                        <p class="description">
                            <?php _e('Optional: URL to your publication logo (JPEG or PNG format).', 'zen-news-channel-rss'); ?><br>
                            <?php _e('Used in the RSS <image> tag for feed branding.', 'zen-news-channel-rss'); ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php _e('Include Thumbnails', 'zen-news-channel-rss'); ?></th>
                    <td>
                        <label class="toggle-switch">
                            <input type="hidden" name="zen_rss_news_thumbnails" value="0" />
                            <input type="checkbox" name="zen_rss_news_thumbnails" value="1" <?php checked(get_option('zen_rss_news_thumbnails', true), true); ?> />
                            <span class="toggle-slider"></span>
                        </label>
                        <p class="description">
                            <?php _e('Add <enclosure> tags with post images to each item.', 'zen-news-channel-rss'); ?><br>
                            <?php _e('Priority: OpenGraph image → Featured image → First content image.', 'zen-news-channel-rss'); ?><br>
                            <?php _e('Automatically converts AVIF/WebP to JPEG format (Zen requirement).', 'zen-news-channel-rss'); ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php _e('Remove Teaser Paragraph', 'zen-news-channel-rss'); ?></th>
                    <td>
                        <label class="toggle-switch">
                            <input type="hidden" name="zen_rss_news_remove_teaser" value="0" />
                            <input type="checkbox" name="zen_rss_news_remove_teaser" value="1" <?php checked(get_option('zen_rss_news_remove_teaser'), true); ?> />
                            <span class="toggle-slider"></span>
                        </label>
                        <p class="description">
                            <?php _e('Remove the first paragraph from <yandex:full-text> content.', 'zen-news-channel-rss'); ?><br>
                            <?php _e('Useful if your first paragraph is a teaser/summary.', 'zen-news-channel-rss'); ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php _e('Remove Shortcodes', 'zen-news-channel-rss'); ?></th>
                    <td>
                        <label class="toggle-switch">
                            <input type="hidden" name="zen_rss_news_remove_shortcodes" value="0" />
                            <input type="checkbox" name="zen_rss_news_remove_shortcodes" value="1" <?php checked(get_option('zen_rss_news_remove_shortcodes'), true); ?> />
                            <span class="toggle-slider"></span>
                        </label>
                        <p class="description">
                            <?php _e('Strip all WordPress shortcodes from content before sending to feed.', 'zen-news-channel-rss'); ?><br>
                            <?php _e('Recommended: Enabled, as shortcodes don\'t render in RSS readers.', 'zen-news-channel-rss'); ?>
                        </p>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Channel Tab -->
        <div id="tab-channel" class="zen-rss-tab-content">
            <p class="description" style="margin-bottom: 20px;">
                <?php _e('Configuration for Yandex Zen Channel feed. Includes full HTML content with automatic JPEG conversion, figure cleanup, and "Source" attribution.', 'zen-news-channel-rss'); ?>
            </p>

            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label
                            for="zen_rss_channel_count"><?php _e('Number of Posts', 'zen-news-channel-rss'); ?></label>
                    </th>
                    <td>
                        <input type="number" id="zen_rss_channel_count" name="zen_rss_channel_count"
                            value="<?php echo esc_attr(get_option('zen_rss_channel_count', 50)); ?>" min="1" max="500"
                            class="small-text" />
                        <p class="description">
                            <?php _e('How many posts to include in the Channel feed (1-500).', 'zen-news-channel-rss'); ?><br>
                            <strong><?php _e('Recommendation: At least 10 posts for initial feed setup.', 'zen-news-channel-rss'); ?></strong>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label
                            for="zen_rss_channel_max_age"><?php _e('Maximum Age (Days)', 'zen-news-channel-rss'); ?></label>
                    </th>
                    <td>
                        <input type="number" id="zen_rss_channel_max_age" name="zen_rss_channel_max_age"
                            value="<?php echo esc_attr(get_option('zen_rss_channel_max_age', 3)); ?>" min="1" max="30"
                            class="small-text" />
                        <p class="description">
                            <?php _e('Only include posts from the last N days (1-30).', 'zen-news-channel-rss'); ?><br>
                            <strong><?php _e('Recommendation: 2-3 days for fresh content.', 'zen-news-channel-rss'); ?></strong><br>
                            <?php _e('Avoid re-publishing old posts to Zen by keeping this value low.', 'zen-news-channel-rss'); ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php _e('Include Thumbnails', 'zen-news-channel-rss'); ?></th>
                    <td>
                        <label class="toggle-switch">
                            <input type="hidden" name="zen_rss_channel_thumbnails" value="0" />
                            <input type="checkbox" name="zen_rss_channel_thumbnails" value="1" <?php checked(get_option('zen_rss_channel_thumbnails', true), true); ?> />
                            <span class="toggle-slider"></span>
                        </label>
                        <p class="description">
                            <?php _e('Add <enclosure> tags with post images to each item.', 'zen-news-channel-rss'); ?><br>
                            <?php _e('Priority: OpenGraph image → Featured image → First content image.', 'zen-news-channel-rss'); ?><br>
                            <?php _e('Automatically converts AVIF/WebP to JPEG format (Zen requirement).', 'zen-news-channel-rss'); ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php _e('Full Content (content:encoded)', 'zen-news-channel-rss'); ?></th>
                    <td>
                        <label class="toggle-switch">
                            <input type="hidden" name="zen_rss_channel_fulltext" value="0" />
                            <input type="checkbox" name="zen_rss_channel_fulltext" value="1" <?php checked(get_option('zen_rss_channel_fulltext', true), true); ?> />
                            <span class="toggle-slider"></span>
                        </label>
                        <p class="description">
                            <?php _e('Include full article HTML in <content:encoded> tag.', 'zen-news-channel-rss'); ?><br>
                            <?php _e('Automatically converts images to JPEG, simplifies <figure> markup, and adds "Source" attribution.', 'zen-news-channel-rss'); ?><br>
                            <strong><?php _e('Recommended: Enabled for Zen Channel.', 'zen-news-channel-rss'); ?></strong>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php _e('"Related Posts" Block', 'zen-news-channel-rss'); ?></th>
                    <td>
                        <label class="toggle-switch">
                            <input type="hidden" name="zen_rss_channel_related" value="0" />
                            <input type="checkbox" name="zen_rss_channel_related" value="1" <?php checked(get_option('zen_rss_channel_related'), true); ?> />
                            <span class="toggle-slider"></span>
                        </label>
                        <p class="description">
                            <?php _e('Insert "Ещё по теме:" (Related Posts) links into content.', 'zen-news-channel-rss'); ?><br>
                            <?php _e('Displays up to 5 related posts from the same categories using <p><a> format (Zen-compliant).', 'zen-news-channel-rss'); ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label
                            for="zen_rss_related_position"><?php _e('Related Posts Position', 'zen-news-channel-rss'); ?></label>
                    </th>
                    <td>
                        <input type="number" id="zen_rss_related_position" name="zen_rss_related_position"
                            value="<?php echo esc_attr(get_option('zen_rss_related_position', 2)); ?>" min="1" max="10"
                            class="small-text" />
                        <p class="description">
                            <?php _e('After which paragraph to insert the related posts block (1-10).', 'zen-news-channel-rss'); ?><br>
                            <?php _e('Default: 2 (after second paragraph). If article has fewer paragraphs, block appears at the end.', 'zen-news-channel-rss'); ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="zen_rss_related_count"><?php _e('Number of Related Posts', 'zen-news-channel-rss'); ?></label>
                    </th>
                    <td>
                        <input type="number" id="zen_rss_related_count" name="zen_rss_related_count"
                            value="<?php echo esc_attr(get_option('zen_rss_related_count', 5)); ?>" min="1" max="10"
                            class="small-text" />
                        <p class="description">
                            <?php _e('How many related posts to display (default: 5).', 'zen-news-channel-rss'); ?>
                        </p>
                    </td>
                </tr>
                
                <!-- Custom Content Block -->
                <tr style="border-top: 1px solid #ddd;">
                    <th scope="row" colspan="2" style="padding-top: 20px;">
                        <h3><?php _e('Custom Content Block', 'zen-news-channel-rss'); ?></h3>
                    </th>
                </tr>
                <tr>
                    <th scope="row"><?php _e('Enable Custom Block', 'zen-news-channel-rss'); ?></th>
                    <td>
                        <label class="toggle-switch">
                            <input type="hidden" name="zen_rss_custom_content_enable" value="0" />
                            <input type="checkbox" name="zen_rss_custom_content_enable" value="1" <?php checked(get_option('zen_rss_custom_content_enable'), true); ?> />
                            <span class="toggle-slider"></span>
                        </label>
                        <p class="description">
                            <?php _e('Insert a custom HTML block into the content (e.g. for promos or social links).', 'zen-news-channel-rss'); ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="zen_rss_custom_content_html"><?php _e('Custom HTML Content', 'zen-news-channel-rss'); ?></label>
                    </th>
                    <td>
                        <?php 
                        $default_promo = '<p>Подписывайтесь на канал <a href="https://t.me/itzineru">itzine</a> и канал подкаста <a href="https://t.me/forgeeks">ForGeeks</a> в Telegram!</p>';
                        $custom_content = get_option('zen_rss_custom_content_html', $default_promo);
                        ?>
                        <textarea id="zen_rss_custom_content_html" name="zen_rss_custom_content_html" rows="5" class="large-text code"><?php echo esc_textarea($custom_content); ?></textarea>
                        <p class="description">
                            <?php _e('HTML content to insert. Allowed tags: p, a, b, i, strong, em, br.', 'zen-news-channel-rss'); ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="zen_rss_custom_content_position"><?php _e('Custom Block Position', 'zen-news-channel-rss'); ?></label>
                    </th>
                    <td>
                        <input type="number" id="zen_rss_custom_content_position" name="zen_rss_custom_content_position"
                            value="<?php echo esc_attr(get_option('zen_rss_custom_content_position', 3)); ?>" min="1" max="20"
                            class="small-text" />
                        <p class="description">
                            <?php _e('After which paragraph to insert the custom block.', 'zen-news-channel-rss'); ?><br>
                            <?php _e('Default: 3. If paragraph doesn\'t exist, block appears at the end.', 'zen-news-channel-rss'); ?>
                        </p>
                    </td>
                </tr>

                <tr>
                    <th scope="row"><?php _e('Remove Shortcodes', 'zen-news-channel-rss'); ?></th>
                    <td>
                        <label class="toggle-switch">
                            <input type="hidden" name="zen_rss_channel_remove_shortcodes" value="0" />
                            <input type="checkbox" name="zen_rss_channel_remove_shortcodes" value="1" <?php checked(get_option('zen_rss_channel_remove_shortcodes'), true); ?> />
                            <span class="toggle-slider"></span>
                        </label>
                        <p class="description">
                            <?php _e('Strip all WordPress shortcodes from content before sending to feed.', 'zen-news-channel-rss'); ?><br>
                            <?php _e('Recommended: Enabled, as shortcodes don\'t render in RSS readers.', 'zen-news-channel-rss'); ?>
                        </p>
                    </td>
                </tr>
            </table>
        </div>

        <p class="submit">
            <?php submit_button(__('Save Settings', 'zen-news-channel-rss'), 'primary', 'submit', false); ?>
        </p>
    </form>

    <!-- Cache Controls (Outside Main Form) -->
    <div class="zen-rss-cache-controls" style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #ccc;">
        <h3><?php _e('Cache Management', 'zen-news-channel-rss'); ?></h3>
        <p><?php _e('Manually clear the cache if you made changes to posts but they are not appearing in the feed immediately.', 'zen-news-channel-rss'); ?>
        </p>

        <div style="display: flex; gap: 10px;">
            <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
                <input type="hidden" name="action" value="zen_rss_clear_cache" />
                <input type="hidden" name="feed_type" value="all" />
                <?php wp_nonce_field('zen_rss_clear_cache'); ?>
                <button type="submit"
                    class="button button-secondary"><?php _e('Clear All Cache', 'zen-news-channel-rss'); ?></button>
            </form>

            <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
                <input type="hidden" name="action" value="zen_rss_clear_cache" />
                <input type="hidden" name="feed_type" value="news" />
                <?php wp_nonce_field('zen_rss_clear_cache'); ?>
                <button type="submit"
                    class="button button-secondary"><?php _e('Clear News Cache', 'zen-news-channel-rss'); ?></button>
            </form>

            <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
                <input type="hidden" name="action" value="zen_rss_clear_cache" />
                <input type="hidden" name="feed_type" value="channel" />
                <?php wp_nonce_field('zen_rss_clear_cache'); ?>
                <button type="submit"
                    class="button button-secondary"><?php _e('Clear Channel Cache', 'zen-news-channel-rss'); ?></button>
            </form>
        </div>
    </div>

    <!-- Feed Links (Outside Main Form) -->
    <div class="zen-rss-feed-links" style="margin-top: 20px;">
        <a href="<?php echo esc_url(site_url('/feed/' . get_option('zen_rss_news_slug', 'zen-news'))); ?>"
            target="_blank" class="button">
            <?php _e('View News Feed', 'zen-news-channel-rss'); ?>
        </a>
        <a href="<?php echo esc_url(site_url('/feed/' . get_option('zen_rss_channel_slug', 'zen-channel'))); ?>"
            target="_blank" class="button">
            <?php _e('View Channel Feed', 'zen-news-channel-rss'); ?>
        </a>
    </div>

</div>

<script>
    function zenRssSwitchTab(event, tabId) {
        event.preventDefault();

        // Hide all contents
        var contents = document.getElementsByClassName('zen-rss-tab-content');
        for (var i = 0; i < contents.length; i++) {
            contents[i].style.display = 'none';
        }

        // Deactivate all tabs
        var tabs = document.getElementsByClassName('nav-tab');
        for (var i = 0; i < tabs.length; i++) {
            tabs[i].className = tabs[i].className.replace(' nav-tab-active', '');
        }

        // Show selected content
        document.getElementById('tab-' + tabId).style.display = 'block';

        // Activate selected tab
        event.currentTarget.className += ' nav-tab-active';

        // Update URL hash (optional)
        // history.pushState(null, null, '#' + tabId);
    }

    // Initialize based on hash or default
    document.addEventListener('DOMContentLoaded', function () {
        var hash = window.location.hash.replace('#', '');
        if (hash && document.getElementById('tab-' + hash)) {
            // Simulate click on the tab
            var tabLink = document.querySelector('a[href="#' + hash + '"]');
            if (tabLink) {
                tabLink.click();
            }
        } else {
            // Default to first tab
            document.getElementById('tab-general').style.display = 'block';
        }
    });
</script>