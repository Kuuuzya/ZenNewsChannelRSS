<?php
// Mock WordPress environment
define('ABSPATH', '/tmp/');

function add_action($hook, $callback)
{
}
function register_activation_hook($file, $callback)
{
}
function register_deactivation_hook($file, $callback)
{
}
function plugin_dir_path($file)
{
    return __DIR__ . '/';
}
function plugin_dir_url($file)
{
    return 'http://localhost/wp-content/plugins/zen-news-channel-rss/';
}
function is_admin()
{
    return true;
}
function get_option($name, $default = false)
{
    return $default;
}
function add_options_page()
{
}
function register_setting()
{
}
function add_feed()
{
}
function flush_rewrite_rules()
{
}
function esc_attr($s)
{
    return $s;
}
function checked()
{
}
function submit_button()
{
}
function settings_fields()
{
}
function do_settings_sections()
{
}
function site_url()
{
    return 'http://localhost';
}
function esc_html($s)
{
    return $s;
}
function esc_url($s)
{
    return $s;
}

// Load the plugin
require_once 'zen-news-channel-rss.php';

echo "Plugin loaded successfully!\n";
