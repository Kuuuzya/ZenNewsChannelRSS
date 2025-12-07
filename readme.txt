=== Zen News & Channel RSS ===
Contributors: kuuuzya
Tags: rss, yandex, zen, feed, news
Requires at least: 5.0
Tested up to: 6.4
Stable tag: 1.1.0.1
Requires PHP: 7.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Professional RSS feed generator for Yandex Zen with full platform compliance. Creates News and Channel feeds with smart caching and modern UI.

== Description ==

**Zen News & Channel RSS** is a comprehensive WordPress plugin that generates two independent RSS feeds specifically designed for Yandex Zen platform:

* **Zen News Feed** - Strict compliance with Zen.News requirements
* **Zen Channel Feed** - Full-featured feed for article publishing

= Key Features =

**Zen News Feed:**
* Strict 8-day age limit (Yandex requirement)
* Automatic JPEG conversion for images
* Maximum 500 items limit
* Content cleaning (forbidden elements removed)
* Proper character escaping (no double-escaping)
* Whitespace normalization

**Zen Channel Feed:**
* Full HTML content in content:encoded
* Automatic AVIF/WebP to JPEG conversion
* Simplified figure markup (removes nesting and wp-classes)
* Source attribution footer
* Configurable "Related Posts" block (1-10 paragraph position)
* Special Zen categories: format-article, index, comment-all
* Recommended: 2-3 day fresh content

**Smart Caching System:**
* Configurable cache duration (0-1440 minutes)
* Set to 0 to disable caching completely
* Separate cache clearing for each feed
* Uses WordPress transients API

**Modern Admin Interface:**
* iOS-style toggle switches
* Detailed descriptions for every setting
* "View Feed" buttons in each section
* Separate cache clear buttons
* Full Russian localization

= Perfect for =

* News websites publishing to Yandex Zen
* Blogs and magazines using Zen platform
* Multi-channel content distribution
* Sites requiring strict RSS compliance

== Installation ==

= Automatic Installation =

1. Go to Plugins → Add New
2. Search for "Zen News Channel RSS"
3. Click "Install Now"
4. Activate the plugin
5. Go to Settings → Zen RSS to configure

= Manual Installation =

1. Upload the `zen-news-channel-rss` folder to `/wp-content/plugins/`
2. Activate the plugin through the 'Plugins' menu
3. Go to Settings → Zen RSS
4. **Important**: After activation, save Permalinks (Settings → Permalinks → Save)

= First-Time Setup =

1. Configure feed URLs (defaults: zen-news, zen-channel)
2. Set cache duration (recommended: 15-30 minutes)
3. Configure each feed according to your needs
4. Save settings and clear cache

Your feeds will be available at:
* `https://yoursite.com/feed/zen-news`
* `https://yoursite.com/feed/zen-channel`

== Frequently Asked Questions ==

= Why aren't my feeds updating? =

1. Clear the plugin cache (button in settings)
2. Clear WordPress cache
3. Check CDN/hosting cache settings

= Why are images not in JPEG format? =

The plugin automatically converts AVIF/WebP to JPEG by replacing file extensions. Ensure your server has JPEG versions of images available.

= How do I disable caching? =

Set Cache Duration to 0 minutes in General Settings. This disables caching completely but may impact performance.

= Can I customize the Related Posts block position? =

Yes! In Channel Feed Settings, use "Related Posts Position" to choose after which paragraph (1-10) the block should appear.

= The plugin interface is in English, not Russian =

Ensure your WordPress language is set to Russian (ru_RU) in Settings → General or in wp-config.php (`define('WPLANG', 'ru_RU');`)

= Do you have any requirements for image sizes? =

For Zen News feed, images should be at least 400×800 pixels. The plugin prioritizes OpenGraph images, then featured images, then first content image.

== Screenshots ==

1. General Settings - Configure feed URLs and caching
2. News Feed Settings - Zen News specific configuration
3. Channel Feed Settings - Full content feed options  
4. Modern toggle switches and detailed descriptions
5. Example News Feed output
6. Example Channel Feed output

== Changelog ==

= 1.1.0 - 2025-12-02 =
**Major Update:**
* Added: Smart caching system with 0-1440 minute duration (0 to disable)
* Added: Separate cache clear buttons for News/Channel/All
* Added: Automatic AVIF/WebP to JPEG conversion in content:encoded
* Added: Simplified figure markup for Zen compliance
* Added: Source attribution footer in Channel feed
* Added: Configurable Related Posts position (1-10 paragraphs)
* Improved: Modern iOS-style toggle switches
* Improved: Comprehensive setting descriptions
* Improved: Full Russian localization (.po/.mo files)
* Fixed: Double-escaping in News feed description
* Fixed: Whitespace normalization in yandex:full-text
* Removed: Yandex Webmaster integration (unused)

= 1.0.2 =
* Fixed: Strict Zen News requirements compliance
* Improved: OG image priority handling

= 1.0.0 =
* Initial release

== Upgrade Notice ==

= 1.1.0 =
Major update with caching system, AVIF/WebP conversion, and modern UI. Clear cache after upgrade to see changes.

== Technical Details ==

= Image Processing Pipeline =

1. Check OpenGraph image (Yoast/RankMath/generic)
2. Fallback to featured image (full size)
3. Fallback to first content image
4. Convert AVIF/WebP → JPEG
5. Return with correct MIME type

= Supported Image Formats =

* Input: JPEG, PNG, AVIF, WebP
* Output: JPEG (News & Channel), PNG (Channel only)

= Cache Keys =

* News Feed: `zen_rss_feed_news`
* Channel Feed: `zen_rss_feed_channel`

= Minimum Requirements =

* WordPress 5.0+
* PHP 7.0+
* manage_options capability for settings

== Privacy & GDPR ==

This plugin does not:
* Collect any user data
* Set any cookies
* Connect to external services
* Track users in any way

All generated feeds are public RSS feeds based on your published WordPress content.

== Support ==

For support requests, bug reports, or feature suggestions:
* GitHub Issues: https://github.com/kuuuzya/zen-news-channel-rss/issues
* Email: Kuuuzya@ya.ru (replace with actual)

== Credits ==

Developed by Sergey Kuznetsov (Kuuuzya)
