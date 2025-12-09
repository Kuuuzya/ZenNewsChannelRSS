<?php

class Zen_RSS_Admin
{

    public function __construct()
    {
        add_action('admin_menu', array($this, 'add_plugin_page'));
        add_action('admin_init', array($this, 'page_init'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_styles'));
        add_action('admin_post_zen_rss_clear_cache', array($this, 'handle_clear_cache'));
        add_action('admin_post_zen_rss_save_settings', array($this, 'save_settings'));
    }

    public function add_plugin_page()
    {
        add_options_page(
            __('Zen RSS Settings', 'zen-news-channel-rss'),
            __('Zen RSS', 'zen-news-channel-rss'),
            'manage_options',
            'zen-rss-settings',
            array($this, 'create_admin_page')
        );
    }

    public function enqueue_admin_styles($hook)
    {
        if ($hook !== 'settings_page_zen-rss-settings') {
            return;
        }
        wp_enqueue_style(
            'zen-rss-admin-styles',
            ZEN_RSS_URL . 'admin/css/admin-styles.css',
            array(),
            ZEN_RSS_VERSION
        );
    }

    public function create_admin_page()
    {
        // Clear cache if settings were just updated
        if (isset($_GET['settings-updated']) && $_GET['settings-updated']) {
            Zen_RSS_Cache_Manager::clear_cache('all');
        }

        require_once ZEN_RSS_PATH . 'views/settings-form.php';
    }

    public function save_settings()
    {
        // Security check
        if (!isset($_POST['zen_rss_save_nonce']) || !wp_verify_nonce($_POST['zen_rss_save_nonce'], 'zen_rss_save_settings')) {
            wp_die(__('Security check failed', 'zen-news-channel-rss'));
        }

        if (!current_user_can('manage_options')) {
            wp_die(__('Unauthorized', 'zen-news-channel-rss'));
        }

        // List of all fields to save
        $fields = array(
            'zen_rss_news_slug' => 'text',
            'zen_rss_channel_slug' => 'text',
            'zen_rss_cache_duration' => 'int',
            'zen_rss_news_count' => 'int',
            'zen_rss_news_max_age' => 'int_age_news', // Special handler
            'zen_rss_news_logo' => 'url',
            'zen_rss_news_logo_square' => 'url',
            'zen_rss_news_thumbnails' => 'bool',
            'zen_rss_news_remove_teaser' => 'bool',
            'zen_rss_news_remove_shortcodes' => 'bool',
            'zen_rss_channel_count' => 'int',
            'zen_rss_channel_max_age' => 'int',
            'zen_rss_channel_thumbnails' => 'bool',
            'zen_rss_channel_fulltext' => 'bool',
            'zen_rss_channel_related' => 'bool',
            'zen_rss_related_position' => 'int',
            'zen_rss_related_count' => 'int', // New
            'zen_rss_custom_content_enable' => 'bool', // New
            'zen_rss_custom_content_html' => 'html', // New
            'zen_rss_custom_content_position' => 'int', // New
            'zen_rss_custom_content_2_enable' => 'bool', // Second block
            'zen_rss_custom_content_2_html' => 'html', // Second block
            'zen_rss_custom_content_2_position' => 'int', // Second block
            'zen_rss_channel_remove_shortcodes' => 'bool',
        );

        foreach ($fields as $field => $type) {
            $value = isset($_POST[$field]) ? $_POST[$field] : null;

            switch ($type) {
                case 'text':
                    update_option($field, sanitize_text_field($value));
                    break;
                case 'int':
                    update_option($field, absint($value));
                    break;
                case 'url':
                    update_option($field, esc_url_raw($value));
                    break;
                case 'bool':
                    // Checkboxes send '1' if checked, nothing if unchecked.
                    // Hidden fields send '0'.
                    // We simply cast to bool.
                    update_option($field, (bool) $value);
                    break;
                case 'html':
                    // Allow safe HTML for custom content
                    update_option($field, wp_kses_post($value));
                    break;
                case 'int_age_news':
                    $age = absint($value);
                    if ($age > 8)
                        $age = 8;
                    if ($age < 1)
                        $age = 1;
                    update_option($field, $age);
                    break;
            }
        }

        // Clear cache
        Zen_RSS_Cache_Manager::clear_cache('all');

        // Redirect back
        wp_redirect(add_query_arg('settings-updated', 'true', admin_url('options-general.php?page=zen-rss-settings')));
        exit;
    }

    public function handle_clear_cache()
    {
        // Security check
        if (!isset($_POST['_wpnonce']) || !wp_verify_nonce($_POST['_wpnonce'], 'zen_rss_clear_cache')) {
            wp_die(__('Security check failed', 'zen-news-channel-rss'));
        }

        if (!current_user_can('manage_options')) {
            wp_die(__('Unauthorized', 'zen-news-channel-rss'));
        }

        // Check which feed to clear
        $feed_type = isset($_POST['feed_type']) ? sanitize_text_field($_POST['feed_type']) : 'all';
        Zen_RSS_Cache_Manager::clear_cache($feed_type);

        // Redirect back with success message
        wp_redirect(add_query_arg(
            array(
                'page' => 'zen-rss-settings',
                'cache_cleared' => $feed_type,
            ),
            admin_url('options-general.php')
        ));
        exit;
    }

    public function page_init()
    {
        // Fix invalid max age if present in DB
        $current_age = get_option('zen_rss_news_max_age');
        if ($current_age && $current_age > 8) {
            update_option('zen_rss_news_max_age', 8);
        }

        // Register settings (only for default values and REST API, not used for saving now)

        // General
        register_setting('zen_rss_option_group', 'zen_rss_news_slug', array('default' => 'zen-news'));
        register_setting('zen_rss_option_group', 'zen_rss_channel_slug', array('default' => 'zen-channel'));
        register_setting('zen_rss_option_group', 'zen_rss_cache_duration', array('default' => 15));

        // News
        register_setting('zen_rss_option_group', 'zen_rss_news_count', array('default' => 50));
        register_setting('zen_rss_option_group', 'zen_rss_news_max_age', array('default' => 8));
        register_setting('zen_rss_option_group', 'zen_rss_news_logo');
        register_setting('zen_rss_option_group', 'zen_rss_news_logo_square');
        register_setting('zen_rss_option_group', 'zen_rss_news_thumbnails', array('default' => true));
        register_setting('zen_rss_option_group', 'zen_rss_news_remove_teaser');
        register_setting('zen_rss_option_group', 'zen_rss_news_remove_shortcodes');

        // Channel
        register_setting('zen_rss_option_group', 'zen_rss_channel_count', array('default' => 50));
        register_setting('zen_rss_option_group', 'zen_rss_channel_max_age', array('default' => 3));
        register_setting('zen_rss_option_group', 'zen_rss_channel_thumbnails', array('default' => true));
        register_setting('zen_rss_option_group', 'zen_rss_channel_fulltext', array('default' => true));
        register_setting('zen_rss_option_group', 'zen_rss_channel_related');
        register_setting('zen_rss_option_group', 'zen_rss_related_position', array('default' => 2));
        register_setting('zen_rss_option_group', 'zen_rss_channel_remove_shortcodes');
    }

    public function sanitize_news_age($input)
    {
        $age = absint($input);
        if ($age > 8) {
            return 8;
        }
        if ($age < 1) {
            return 1;
        }
        return $age;
    }
}
