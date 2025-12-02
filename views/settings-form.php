<div class="wrap zen-rss-settings">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

    <?php if (isset($_GET['cache_cleared'])): ?>
        <div class="notice notice-success is-dismissible">
            <p><?php _e('Cache cleared successfully!', 'zen-news-channel-rss'); ?></p>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['settings-updated'])): ?>
        <div class="notice notice-success is-dismissible">
            <p><?php _e('Settings saved successfully!', 'zen-news-channel-rss'); ?></p>
        </div>
    <?php endif; ?>

    <!-- Feed Links -->
    <div class="zen-rss-feed-links">
        <h3><?php _e('RSS Feed Links', 'zen-news-channel-rss'); ?></h3>
        <p>
            <a href="<?php echo esc_url(site_url('/feed/' . get_option('zen_rss_news_slug', 'zen-news'))); ?>"
                target="_blank">
                <?php _e('Zen News Feed', 'zen-news-channel-rss'); ?> &rarr;
            </a>
            <a href="<?php echo esc_url(site_url('/feed/' . get_option('zen_rss_channel_slug', 'zen-channel'))); ?>"
                target="_blank">
                <?php _e('Zen Channel Feed', 'zen-news-channel-rss'); ?> &rarr;
            </a>
        </p>
    </div>

    <form method="post" action="options.php">
        <?php settings_fields('zen_rss_option_group'); ?>

        <!-- General Settings -->
        <h2><?php _e('General Settings', 'zen-news-channel-rss'); ?></h2>
        <table class="form-table">
            <tr>
                <th scope="row"><?php _e('News Feed Slug', 'zen-news-channel-rss'); ?></th>
                <td>
                    <input type="text" name="zen_rss_news_slug"
                        value="<?php echo esc_attr(get_option('zen_rss_news_slug', 'zen-news')); ?>"
                        class="regular-text" />
                    <p class="description">
                        <?php _e('URL path for News feed (e.g., "zen-news" → /feed/zen-news)', 'zen-news-channel-rss'); ?>
                    </p>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Channel Feed Slug', 'zen-news-channel-rss'); ?></th>
                <td>
                    <input type="text" name="zen_rss_channel_slug"
                        value="<?php echo esc_attr(get_option('zen_rss_channel_slug', 'zen-channel')); ?>"
                        class="regular-text" />
                    <p class="description">
                        <?php _e('URL path for Channel feed (e.g., "zen-channel" → /feed/zen-channel)', 'zen-news-channel-rss'); ?>
                    </p>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Cache Duration', 'zen-news-channel-rss'); ?></th>
                <td>
                    <input type="number" name="zen_rss_cache_duration"
                        value="<?php echo esc_attr(get_option('zen_rss_cache_duration', 15)); ?>" min="1" max="1440"
                        class="small-text" />
                    <?php _e('minutes', 'zen-news-channel-rss'); ?>
                    <p class="description">
                        <?php _e('How long to cache feed output (1-1440 minutes)', 'zen-news-channel-rss'); ?></p>
                    <div class="zen-rss-cache-controls">
                        <form method="post" action="<?php echo admin_url('admin-post.php'); ?>"
                            style="display: inline;">
                            <input type="hidden" name="action" value="zen_rss_clear_cache" />
                            <?php wp_nonce_field('zen_rss_clear_cache'); ?>
                            <button type="submit"
                                class="button"><?php _e('Clear Cache Now', 'zen-news-channel-rss'); ?></button>
                        </form>
                    </div>
                </td>
            </tr>
        </table>

        <!-- News Feed Settings -->
        <h2><?php _e('News Feed Settings', 'zen-news-channel-rss'); ?></h2>
        <table class="form-table">
            <tr>
                <th scope="row"><?php _e('Number of Posts', 'zen-news-channel-rss'); ?></th>
                <td>
                    <input type="number" name="zen_rss_news_count"
                        value="<?php echo esc_attr(get_option('zen_rss_news_count', 50)); ?>" min="1" max="500"
                        class="small-text" />
                    <p class="description"><?php _e('Maximum 500 items (Zen requirement)', 'zen-news-channel-rss'); ?>
                    </p>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Maximum Age (Days)', 'zen-news-channel-rss'); ?></th>
                <td>
                    <input type="number" name="zen_rss_news_max_age"
                        value="<?php echo esc_attr(get_option('zen_rss_news_max_age', 3)); ?>" min="1" max="8"
                        class="small-text" />
                    <p class="description">
                        <?php _e('Only include posts from last N days (maximum 8 for News)', 'zen-news-channel-rss'); ?>
                    </p>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Logo URL', 'zen-news-channel-rss'); ?></th>
                <td>
                    <input type="url" name="zen_rss_news_logo"
                        value="<?php echo esc_url(get_option('zen_rss_news_logo')); ?>" class="regular-text" />
                    <p class="description"><?php _e('Optional logo for the feed', 'zen-news-channel-rss'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Include Thumbnails', 'zen-news-channel-rss'); ?></th>
                <td>
                    <label class="toggle-switch">
                        <input type="checkbox" name="zen_rss_news_thumbnails" value="1" <?php checked(get_option('zen_rss_news_thumbnails', true), true); ?> />
                        <span class="toggle-slider"></span>
                    </label>
                    <p class="description">
                        <?php _e('Add image enclosure tags (JPEG format, OG image priority)', 'zen-news-channel-rss'); ?>
                    </p>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Remove Teaser', 'zen-news-channel-rss'); ?></th>
                <td>
                    <label class="toggle-switch">
                        <input type="checkbox" name="zen_rss_news_remove_teaser" value="1" <?php checked(get_option('zen_rss_news_remove_teaser'), true); ?> />
                        <span class="toggle-slider"></span>
                    </label>
                    <p class="description">
                        <?php _e('Remove first paragraph from full-text content', 'zen-news-channel-rss'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Remove Shortcodes', 'zen-news-channel-rss'); ?></th>
                <td>
                    <label class="toggle-switch">
                        <input type="checkbox" name="zen_rss_news_remove_shortcodes" value="1" <?php checked(get_option('zen_rss_news_remove_shortcodes'), true); ?> />
                        <span class="toggle-slider"></span>
                    </label>
                    <p class="description">
                        <?php _e('Strip all WordPress shortcodes from content', 'zen-news-channel-rss'); ?></p>
                </td>
            </tr>
        </table>

        <!-- Channel Feed Settings -->
        <h2><?php _e('Channel Feed Settings', 'zen-news-channel-rss'); ?></h2>
        <table class="form-table">
            <tr>
                <th scope="row"><?php _e('Number of Posts', 'zen-news-channel-rss'); ?></th>
                <td>
                    <input type="number" name="zen_rss_channel_count"
                        value="<?php echo esc_attr(get_option('zen_rss_channel_count', 50)); ?>" min="1" max="500"
                        class="small-text" />
                    <p class="description">
                        <?php _e('Recommended: at least 10 posts for initial setup', 'zen-news-channel-rss'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Maximum Age (Days)', 'zen-news-channel-rss'); ?></th>
                <td>
                    <input type="number" name="zen_rss_channel_max_age"
                        value="<?php echo esc_attr(get_option('zen_rss_channel_max_age', 3)); ?>" min="1" max="30"
                        class="small-text" />
                    <p class="description">
                        <?php _e('Recommended: 2-3 days for fresh content', 'zen-news-channel-rss'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Include Thumbnails', 'zen-news-channel-rss'); ?></th>
                <td>
                    <label class="toggle-switch">
                        <input type="checkbox" name="zen_rss_channel_thumbnails" value="1" <?php checked(get_option('zen_rss_channel_thumbnails', true), true); ?> />
                        <span class="toggle-slider"></span>
                    </label>
                    <p class="description">
                        <?php _e('Add image enclosure tags (JPEG format, OG image priority)', 'zen-news-channel-rss'); ?>
                    </p>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Full Content', 'zen-news-channel-rss'); ?></th>
                <td>
                    <label class="toggle-switch">
                        <input type="checkbox" name="zen_rss_channel_fulltext" value="1" <?php checked(get_option('zen_rss_channel_fulltext', true), true); ?> />
                        <span class="toggle-slider"></span>
                    </label>
                    <p class="description">
                        <?php _e('Generate content:encoded with full article HTML (images converted to JPEG)', 'zen-news-channel-rss'); ?>
                    </p>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Related Posts Block', 'zen-news-channel-rss'); ?></th>
                <td>
                    <label class="toggle-switch">
                        <input type="checkbox" name="zen_rss_channel_related" value="1" <?php checked(get_option('zen_rss_channel_related'), true); ?> />
                        <span class="toggle-slider"></span>
                    </label>
                    <p class="description">
                        <?php _e('Insert "Related Posts" links into content', 'zen-news-channel-rss'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Related Posts Position', 'zen-news-channel-rss'); ?></th>
                <td>
                    <input type="number" name="zen_rss_related_position"
                        value="<?php echo esc_attr(get_option('zen_rss_related_position', 2)); ?>" min="1" max="10"
                        class="small-text" />
                    <p class="description">
                        <?php _e('After which paragraph to insert related posts (1-10)', 'zen-news-channel-rss'); ?>
                    </p>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Remove Shortcodes', 'zen-news-channel-rss'); ?></th>
                <td>
                    <label class="toggle-switch">
                        <input type="checkbox" name="zen_rss_channel_remove_shortcodes" value="1" <?php checked(get_option('zen_rss_channel_remove_shortcodes'), true); ?> />
                        <span class="toggle-slider"></span>
                    </label>
                    <p class="description">
                        <?php _e('Strip all WordPress shortcodes from content', 'zen-news-channel-rss'); ?></p>
                </td>
            </tr>
        </table>

        <?php submit_button(__('Save Settings', 'zen-news-channel-rss')); ?>
    </form>
</div>