<?php
/**
 * Comprehensive verification script for Zen RSS Plugin
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "=== ZEN RSS PLUGIN VERIFICATION ===\n\n";

// 1. Check for whitespace output
echo "1. Checking for whitespace output...\n";
ob_start();
require_once 'zen-news-channel-rss.php';
$output = ob_get_clean();
if (strlen($output) > 0) {
    echo "   ❌ FAIL: Output detected (" . strlen($output) . " bytes)\n";
    echo "   This will cause 'headers already sent' errors!\n";
} else {
    echo "   ✓ PASS: No output detected\n";
}

// 2. Verify class existence
echo "\n2. Verifying class loading...\n";
$required_classes = [
    'Zen_RSS_Admin',
    'Zen_RSS_Cache_Manager',
    'Zen_RSS_Generator_News',
    'Zen_RSS_Generator_Channel',
    'Zen_RSS_Block_Related',
    'Zen_RSS_Text_Cleaner'
];

foreach ($required_classes as $class) {
    if (class_exists($class)) {
        echo "   ✓ $class loaded\n";
    } else {
        echo "   ❌ $class NOT FOUND\n";
    }
}

// 3. Check cache manager methods
echo "\n3. Verifying cache manager...\n";
if (class_exists('Zen_RSS_Cache_Manager')) {
    $methods = ['get_cached_feed', 'set_cached_feed', 'clear_cache', 'get_cache_duration', 'is_cache_enabled'];
    foreach ($methods as $method) {
        if (method_exists('Zen_RSS_Cache_Manager', $method)) {
            echo "   ✓ Method $method exists\n";
        } else {
            echo "   ❌ Method $method MISSING\n";
        }
    }

    // Test cache disable logic
    echo "\n   Testing cache disable (duration = 0):\n";
    update_option('zen_rss_cache_duration', 0);
    $is_enabled = Zen_RSS_Cache_Manager::is_cache_enabled();
    echo "   Cache enabled: " . ($is_enabled ? "YES (❌ FAIL)" : "NO (✓ PASS)") . "\n";

    echo "\n   Testing cache enable (duration = 15):\n";
    update_option('zen_rss_cache_duration', 15);
    $is_enabled = Zen_RSS_Cache_Manager::is_cache_enabled();
    echo "   Cache enabled: " . ($is_enabled ? "YES (✓ PASS)" : "NO (❌ FAIL)") . "\n";
}

// 4. Check admin class methods
echo "\n4. Verifying admin class...\n";
if (class_exists('Zen_RSS_Admin')) {
    $admin_methods = ['sanitize_and_clear_cache', 'sanitize_boolean_and_clear', 'sanitize_news_age'];
    foreach ($admin_methods as $method) {
        if (method_exists('Zen_RSS_Admin', $method)) {
            echo "   ✓ Method $method exists\n";
        } else {
            echo "   ❌ Method $method MISSING\n";
        }
    }

    // Test sanitize_news_age
    if (method_exists('Zen_RSS_Admin', 'sanitize_news_age')) {
        $admin = new Zen_RSS_Admin();
        echo "\n   Testing sanitize_news_age:\n";
        $test_cases = [
            ['input' => 10, 'expected' => 8],
            ['input' => 5, 'expected' => 5],
            ['input' => 0, 'expected' => 1],
            ['input' => -5, 'expected' => 1],
        ];

        foreach ($test_cases as $test) {
            $result = $admin->sanitize_news_age($test['input']);
            $status = ($result === $test['expected']) ? '✓' : '❌';
            echo "   $status Input: {$test['input']} → Output: $result (Expected: {$test['expected']})\n";
        }
    }
}

// 5. Check generator classes
echo "\n5. Verifying feed generators...\n";
if (class_exists('Zen_RSS_Generator_News')) {
    echo "   ✓ News generator exists\n";
    if (method_exists('Zen_RSS_Generator_News', 'render')) {
        echo "   ✓ News render method exists\n";
    }
}
if (class_exists('Zen_RSS_Generator_Channel')) {
    echo "   ✓ Channel generator exists\n";
    if (method_exists('Zen_RSS_Generator_Channel', 'render')) {
        echo "   ✓ Channel render method exists\n";
    }
}

// 6. Check related posts block
echo "\n6. Verifying related posts block...\n";
if (class_exists('Zen_RSS_Block_Related')) {
    if (method_exists('Zen_RSS_Block_Related', 'inject_related')) {
        echo "   ✓ inject_related method exists\n";
    }
}

// 7. Verify default settings
echo "\n7. Checking default settings...\n";
$defaults = [
    'zen_rss_news_max_age' => 8,
    'zen_rss_cache_duration' => 15,
    'zen_rss_news_slug' => 'zen-news',
    'zen_rss_channel_slug' => 'zen-channel',
];

foreach ($defaults as $option => $expected) {
    $value = get_option($option, 'NOT_SET');
    if ($value === 'NOT_SET') {
        echo "   ⚠ $option not set (will use default: $expected)\n";
    } else {
        echo "   ✓ $option = $value\n";
    }
}

echo "\n=== VERIFICATION COMPLETE ===\n";
