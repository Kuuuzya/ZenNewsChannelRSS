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

    <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
        <input type="hidden" name="action" value="zen_rss_save_settings" />
        <?php wp_nonce_field('zen_rss_save_settings', 'zen_rss_save_nonce'); ?>

        <!-- General Settings -->
        <h2><?php _e('General Settings', 'zen-news-channel-rss'); ?></h2>
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="zen_rss_news_slug"><?php _e('News Feed URL Slug', 'zen-news-channel-rss'); ?></label>
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
                    <label for="zen_rss_cache_duration"><?php _e('Cache Duration', 'zen-news-channel-rss'); ?></label>
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
                    <div class="zen-rss-cache-controls">
                        <form method="post" action="<?php echo admin_url('admin-post.php'); ?>"
                            style="display: inline;">
                            <input type="hidden" name="action" value="zen_rss_clear_cache" />
                            <input type="hidden" name="feed_type" value="all" />
                            <?php wp_nonce_field('zen_rss_clear_cache'); ?>
                            <button type="submit"
                                class="button"><?php _e('Clear All Cache', 'zen-news-channel-rss'); ?></button>
                        </form>
                    </div>
                </td>
            </tr>
        </table>

        <!-- News Feed Settings -->
        <h2><?php _e('News Feed Settings (Zen News)', 'zen-news-channel-rss'); ?></h2>
        <p class="description" style="margin-top: -10px; margin-bottom: 15px;">
            <?php _e('Configuration for Yandex Zen News feed. This feed follows strict Zen News requirements (8-day limit, JPEG images, clean text).', 'zen-news-channel-rss'); ?>
        </p>
        <div class="zen-rss-feed-links" style="margin-bottom: 15px;">
            <a href="<?php echo esc_url(site_url('/feed/' . get_option('zen_rss_news_slug', 'zen-news'))); ?>"
                target="_blank">
                <?php _e('View News Feed', 'zen-news-channel-rss'); ?> &rarr;
            </a>
            <form method="post" action="<?php echo admin_url('admin-post.php'); ?>" style="display: inline;">
                <input type="hidden" name="action" value="zen_rss_clear_cache" />
                <input type="hidden" name="feed_type" value="news" />
                <?php wp_nonce_field('zen_rss_clear_cache'); ?>
                <button type="submit" class="button"
                    style="background: transparent; color: #2271b1; border: 1px solid #2271b1;">
                    <?php _e('Clear News Cache', 'zen-news-channel-rss'); ?>
                </button>
            </form>
        </div>
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
                    <label for="zen_rss_news_max_age"><?php _e('Maximum Age (Days)', 'zen-news-channel-rss'); ?></label>
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

        <!-- Channel Feed Settings -->
        <h2><?php _e('Channel Feed Settings (Zen Channel)', 'zen-news-channel-rss'); ?></h2>
        <p class="description" style="margin-top: -10px; margin-bottom: 15px;">
            <?php _e('Configuration for Yandex Zen Channel feed. Includes full HTML content with automatic JPEG conversion, figure cleanup, and "Source" attribution.', 'zen-news-channel-rss'); ?>
        </p>
        <div class="zen-rss-feed-links" style="margin-bottom: 15px;">
            <a href="<?php echo esc_url(site_url('/feed/' . get_option('zen_rss_channel_slug', 'zen-channel'))); ?>"
                target="_blank">
                <?php _e('View Channel Feed', 'zen-news-channel-rss'); ?> &rarr;
            </a>
            <form method="post" action="<?php echo admin_url('admin-post.php'); ?>" style="display: inline;">
                <input type="hidden" name="action" value="zen_rss_clear_cache" />
                <input type="hidden" name="feed_type" value="channel" />
                <?php wp_nonce_field('zen_rss_clear_cache'); ?>
                <button type="submit" class="button"
                    style="background: transparent; color: #2271b1; border: 1px solid #2271b1;">
                    <?php _e('Clear Channel Cache', 'zen-news-channel-rss'); ?>
                </button>
            </form>
        </div>
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="zen_rss_channel_count"><?php _e('Number of Posts', 'zen-news-channel-rss'); ?></label>
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

        <?php submit_button(__('Save Settings', 'zen-news-channel-rss')); ?>
    </form>
</div>