<?php

class Zen_RSS_Admin
{

    public function __construct()
    {
        add_action('admin_menu', array($this, 'add_plugin_page'));
        add_action('admin_init', array($this, 'page_init'));
    }

    public function add_plugin_page()
    {
        add_options_page(
            'Настройки Zen News&Channel RSS',
            'Zen RSS',
            'manage_options',
            'zen-rss-settings',
            array($this, 'create_admin_page')
        );
    }

    public function create_admin_page()
    {
        require_once ZEN_RSS_PATH . 'views/settings-form.php';
    }

    public function page_init()
    {
        // Register all settings in one go since we are using a single page

        // General
        register_setting('zen_rss_option_group', 'zen_rss_news_slug');
        register_setting('zen_rss_option_group', 'zen_rss_channel_slug');
        register_setting('zen_rss_option_group', 'zen_rss_yandex_token');
        register_setting('zen_rss_option_group', 'zen_rss_send_unique_text');

        // News
        register_setting('zen_rss_option_group', 'zen_rss_news_count');
        register_setting('zen_rss_option_group', 'zen_rss_news_max_age');
        register_setting('zen_rss_option_group', 'zen_rss_news_logo');
        register_setting('zen_rss_option_group', 'zen_rss_news_logo_square');
        register_setting('zen_rss_option_group', 'zen_rss_news_thumbnails');
        register_setting('zen_rss_option_group', 'zen_rss_news_remove_teaser');
        register_setting('zen_rss_option_group', 'zen_rss_news_remove_shortcodes');

        // Channel
        register_setting('zen_rss_option_group', 'zen_rss_channel_count');
        register_setting('zen_rss_option_group', 'zen_rss_channel_max_age');
        register_setting('zen_rss_option_group', 'zen_rss_channel_thumbnails');
        register_setting('zen_rss_option_group', 'zen_rss_channel_fulltext');
        register_setting('zen_rss_option_group', 'zen_rss_channel_related');
        register_setting('zen_rss_option_group', 'zen_rss_channel_remove_shortcodes');
    }
}
