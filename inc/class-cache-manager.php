<?php

class Zen_RSS_Cache_Manager
{

    const CACHE_KEY_NEWS = 'zen_rss_feed_news';
    const CACHE_KEY_CHANNEL = 'zen_rss_feed_channel';

    /**
     * Get cached feed content
     *
     * @param string $feed_type 'news' or 'channel'
     * @return string|false
     */
    public static function get_cached_feed($feed_type)
    {
        $cache_key = $feed_type === 'news' ? self::CACHE_KEY_NEWS : self::CACHE_KEY_CHANNEL;
        return get_transient($cache_key);
    }

    /**
     * Set cached feed content
     *
     * @param string $feed_type 'news' or 'channel'
     * @param string $content
     * @return bool
     */
    public static function set_cached_feed($feed_type, $content)
    {
        $cache_key = $feed_type === 'news' ? self::CACHE_KEY_NEWS : self::CACHE_KEY_CHANNEL;
        $duration = self::get_cache_duration();
        return set_transient($cache_key, $content, $duration);
    }

    /**
     * Clear feed cache
     *
     * @param string $feed_type 'news', 'channel', or 'all'
     * @return bool
     */
    public static function clear_cache($feed_type = 'all')
    {
        if ($feed_type === 'all' || $feed_type === 'news') {
            delete_transient(self::CACHE_KEY_NEWS);
        }
        if ($feed_type === 'all' || $feed_type === 'channel') {
            delete_transient(self::CACHE_KEY_CHANNEL);
        }
        return true;
    }

    /**
     * Get cache duration in seconds
     *
     * @return int
     */
    public static function get_cache_duration()
    {
        $minutes = (int) get_option('zen_rss_cache_duration', 15);
        // Allow 0 to disable cache, otherwise clamp between 1 and 1440 minutes
        if ($minutes === 0) {
            return 0;
        }
        $minutes = max(1, min(1440, $minutes));
        return $minutes * 60;
    }

    /**
     * Check if caching is enabled
     *
     * @return bool
     */
    public static function is_cache_enabled()
    {
        return self::get_cache_duration() > 0;
    }
}
