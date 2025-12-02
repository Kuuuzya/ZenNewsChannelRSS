<?php

class Zen_RSS_Admin
{

    public function __construct()
    {
        add_action('admin_menu', array($this, 'add_plugin_page'));
        add_action('admin_init', array($this, 'page_init'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_styles'));
        add_action('admin_post_zen_rss_clear_cache', array($this, 'handle_clear_cache'));
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
        require_once ZEN_RSS_PATH . 'views/settings-form.php';
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

        Zen_RSS_Cache_Manager::clear_cache();

        // Redirect back with success message
        wp_redirect(add_query_arg(
            array(
                'page' => 'zen-rss-settings',
                'cache_cleared' => '1',
            ),
            admin_url('options-general.php')
        ));
        exit;
    }

    public function page_init()
    {
        // Register all settings

        // General
        register_setting('zen_rss_option_group', 'zen_rss_news_slug', array('default' => 'zen-news'));
        register_setting('zen_rss_option_group', 'zen_rss_channel_slug', array('default' => 'zen-channel'));
        register_setting('zen_rss_option_group', 'zen_rss_cache_duration', array('default' => 15));

        // News
        register_setting('zen_rss_option_group', 'zen_rss_news_count', array('default' => 50));
        register_setting('zen_rss_option_group', 'zen_rss_news_max_age', array('default' => 3));
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
}
