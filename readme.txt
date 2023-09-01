=== Bulk Change Media Author ===
Contributors: mikhno
Author URI: http://www.mikhno.org/
Plugin URL: http://www.mikhno.org/articles/en/files/wp_bulk_change_media_author
Tags: upload, bulk, attachment, author, media, change
Requires at least: 4.7
Tested up to: 6.3
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Bulk change author for multiple media files, using the default WP Media Library.

== Description ==

This is a very simple plugin that allows you to bulk change author for media files.

The action is added in the "List" view of the Media Library.

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/bulk-change-media-author` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress.


== Frequently Asked Questions ==

= Can I change the author in bulk in the "Grid" view? =

Not yet.

= There's only 20 media items per page in the "List" view, but I want to change more at a time! =

Open "Screen Options" at the top-right in the Media Library and change the value of "Number of items per page" in the "Pagination" section.

= Can I quickly change the author for all media uploaded by particular user? =

If you apply "Change Author" in the Media Library without selecting any media, it will prompt you to filter media by user. After that, you will be able to change the author for all media uploaded by that user.

== Screenshots ==

1. The new bulk action is added into Bulk Actions dropdown menu for "List" view of the Media Library.

== Changelog ==

= 1.3.2 =
* Fix: Pagination limit when filtering media by author.

= 1.3.1 =
* Fix: PHP type error when filtering media by author.

= 1.3 =
* New: Filter and bulk change media author by its current author.
* Other: Tested with WordPress 6.3.

= 1.2 =
* Other: Tested with WordPress 4.9.
* Translations: Added Spanish (thanks Kcho).

= 1.1 =
* New: Added translation support.
* Other: Tested with WordPress 4.8.
* Translations: Added German (thanks Mike).
* Translations: Added translation support to "Change" and "Cancel" buttons (thanks Mike).

= 1.0 =
* First public release.
