<?php

class Zen_RSS_Injector
{
    /**
     * Inject HTML content into a string after a specific paragraph.
     *
     * @param string $content The original content.
     * @param string $html_to_inject The HTML to inject.
     * @param int $position The paragraph number to inject after (1-based).
     * @return string The modified content.
     */
    public static function inject($content, $html_to_inject, $position)
    {
        if (empty($html_to_inject)) {
            return $content;
        }

        $position = max(1, (int) $position);

        // Split content by closing paragraph tags
        // Using a robust regex to handle attributes and spacing in </p>
        $paragraphs = preg_split('/(<\/p>)/i', $content, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

        $count = 0;
        $inserted = false;
        $new_content = '';

        foreach ($paragraphs as $chunk) {
            $new_content .= $chunk;
            // Check if this chunk is a closing paragraph tag
            if (preg_match('/^<\/p>$/i', trim($chunk))) {
                $count++;
                if ($count === $position) {
                    $new_content .= PHP_EOL . $html_to_inject . PHP_EOL;
                    $inserted = true;
                }
            }
        }

        // If we couldn't insert (e.g. less paragraphs than position), append to the end
        if (!$inserted) {
            $new_content .= PHP_EOL . $html_to_inject . PHP_EOL;
        }

        return $new_content;
    }
}
