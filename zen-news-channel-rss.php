<?php
/**
 * Plugin Name: Zen News&Channel RSS
 * Description: Generates two independent RSS feeds for Yandex Zen (News and Channel) with a comprehensive admin interface.
 * Version:           1.1.1.3
 * Author: Sergey Kuznetsov (Kuuuzya)
 * Text Domain: zen-news-channel-rss
 * Domain Path: /languages
 */

if (!defined('ABSPATH')) {
    exit;
}

define('ZEN_RSS_PATH', plugin_dir_path(__FILE__));
define('ZEN_RSS_URL', plugin_dir_url(__FILE__));
define('ZEN_RSS_VERSION', '1.1.1.3');

// Load text domain for i18n
add_action('plugins_loaded', 'zen_rss_load_textdomain');
function zen_rss_load_textdomain()
{
    load_plugin_textdomain('zen-news-channel-rss', false, dirname(plugin_basename(__FILE__)) . '/languages');
}

// Add Settings link to plugins list
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'zen_rss_add_settings_link');
function zen_rss_add_settings_link($links)
{
    $settings_link = '<a href="' . admin_url('admin.php?page=zen-rss-settings') . '">' . __('Settings', 'zen-news-channel-rss') . '</a>';
    array_unshift($links, $settings_link);
    return $links;
}

// Include required files
require_once ZEN_RSS_PATH . 'admin/settings-page.php';
require_once ZEN_RSS_PATH . 'inc/class-cache-manager.php';
require_once ZEN_RSS_PATH . 'inc/class-text-cleaner.php';
require_once ZEN_RSS_PATH . 'inc/class-injector.php';
require_once ZEN_RSS_PATH . 'inc/class-block-related.php';
require_once ZEN_RSS_PATH . 'inc/class-generator-news.php';
require_once ZEN_RSS_PATH . 'inc/class-generator-channel.php';

/**
 * Activation hook: Flush rewrite rules
 */
register_activation_hook(__FILE__, 'zen_rss_activate');
function zen_rss_activate()
{
    zen_rss_add_feed_rules();
    // flush_rewrite_rules(); // Manual flush required to avoid activation errors
}

/**
 * Deactivation hook: Flush rewrite rules
 */
register_deactivation_hook(__FILE__, 'zen_rss_deactivate');
function zen_rss_deactivate()
{
    // flush_rewrite_rules();
}

/**
 * Initialize the plugin
 */
add_action('init', 'zen_rss_init');
function zen_rss_init()
{
    zen_rss_add_feed_rules();
}

/**
 * Force correct Content-Type for our feeds
 */
add_action('template_redirect', 'zen_rss_force_content_type');
function zen_rss_force_content_type()
{
    if (!is_feed()) {
        return;
    }

    $news_slug = get_option('zen_rss_news_slug', 'zen-news');
    $channel_slug = get_option('zen_rss_channel_slug', 'zen-channel');

    // Check if this is one of our feeds
    if (get_query_var('feed') === $news_slug || get_query_var('feed') === $channel_slug) {
        header('Content-Type: application/rss+xml; charset=UTF-8', true);
    }
}

/**
 * Add feed rewrite rules
 */
function zen_rss_add_feed_rules()
{
    $news_slug = get_option('zen_rss_news_slug', 'zen-news');
    $channel_slug = get_option('zen_rss_channel_slug', 'zen-channel');

    add_feed($news_slug, 'zen_rss_render_news_feed');
    add_feed($channel_slug, 'zen_rss_render_channel_feed');
}

/**
 * Render News Feed
 */
function zen_rss_render_news_feed()
{
    if (class_exists('Zen_RSS_Generator_News')) {
        $generator = new Zen_RSS_Generator_News();
        $generator->render();
    }
}

/**
 * Render Channel Feed
 */
function zen_rss_render_channel_feed()
{
    if (class_exists('Zen_RSS_Generator_Channel')) {
        $generator = new Zen_RSS_Generator_Channel();
        $generator->render();
    }
}

// Initialize Admin
if (is_admin() && class_exists('Zen_RSS_Admin')) {
    new Zen_RSS_Admin();
}
