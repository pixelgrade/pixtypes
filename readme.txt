=== PixTypes ===
Contributors: pixelgrade, euthelup, babbardel, vlad.olaru, cristianfrumusanu, razvanonofrei
Tags: custom, post-types, metadata, builder, gallery
Requires at least: 4.6.0
Tested up to: 4.8.1
Stable tag: 1.4.7
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A WordPress plugin for managing custom post types and custom meta boxes from a theme.

== Description ==

With [PixTypes](https://github.com/pixelgrade/pixtypes) you can allow your theme to define what custom post types or meta-boxes should be active when your theme is up

Note: This plugin is addressed to developers, it doesn't do nothing if it isn't [properly configured](https://github.com/pixelgrade/pixtypes#pixytpes_config).

== Installation ==

1. First you will need to configure your theme to [define PixTypes settings](https://github.com/pixelgrade/pixtypes#pixytpes_config)
2. Install PixTypes either via the WordPress.org plugin directory, or by uploading the files to your `/wp-content/plugins/` directory
3. After activating PixTypes all your custom post-types should be visible now.

== Changelog ==

= 1.4.7 =
* Small improvements to the metabox system.
* Fixed Color Picker style.

= 1.4.6 =
* Fixed HTTPS WP admin issue with regards to assets.

= 1.4.5 =
* Added advanced show_on behaviour and filtering.

= 1.4.4 =
* Added a new image field type

= 1.4.3 =
* Fixed Builder on PHP 5.2
* Fixed Builder text block.Now it doesn't lose new lines on editor switch
* Fixed ColorPicker style in normal context
* Improved assets loading. Now we won't load styles on non-PixTypes pages.

= 1.4.2 =
* Fixed Builder visuals
* Safely sanitize builder output

= 1.4.1 =
* Fixed pix_builder on old configs.
* Fixed an issue with WPJM Extended Location
* Fixed new block ids in pix_builder

= 1.4.0 =
* Improved all the fields Visuals and styles.
* Improved the pix_builder field, now will save values in the content instead of its own meta.And the editor is better now.
* Added a positioning UI for the builder blocks.
* Fixed the defaults for the textarea fields.
* Fixed the defaults for colorpicker.
* Fixed small PHP warnings and notices.

= 1.3.5 =
Improved the multicheck field

= 1.3.3 =

* Added a Playlist field
* Improved translation strings
* Fixed Galleries Icons and Style
* Quit .mo/.po files for a general .pot one

= 1.3.2 =

* WordPress 4.3 compatibility
* Fixed Sticky buttons for the PixBuilder field

= 1.3.1 =

* Allow portfolio to be a jetpack compatible type
* Small fixes to the gallery field

= 1.2.10 =

* Show / Hide options bug fix

= 1.2.9 =

* Gmap pins added

= 1.2.6 =

* Builder field added
* Support for wp 4.0
* Small fixes

= 1.2.2 =

* Small fixes to metaboxes

= 1.2.1 =

* Github Updater slug fix
* And small fixes...

= 1.2.0 =

* Ajax Update
* Gallery Metabox works now even if there is no wp-editor on page
* And small fixes...

= 1.1.0 =

* Add admin panel
* Fixes

= 1.0.0 =
* Here we go
