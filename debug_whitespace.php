<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

ob_start();
require_once 'zen-news-channel-rss.php';
$output = ob_get_clean();

if (strlen($output) > 0) {
    echo "CRITICAL: Output detected during plugin load (" . strlen($output) . " bytes):\n";
    echo "START>>>" . $output . "<<<END\n";
    echo "Hex dump:\n";
    echo bin2hex($output) . "\n";
} else {
    echo "No output detected during plugin load.\n";
}

// Also check class files individually
$files = glob('inc/*.php');
foreach ($files as $file) {
    ob_start();
    include_once $file;
    $f_out = ob_get_clean();
    if (strlen($f_out) > 0) {
        echo "CRITICAL: Output in $file:\n";
        echo "START>>>" . $f_out . "<<<END\n";
    }
}
