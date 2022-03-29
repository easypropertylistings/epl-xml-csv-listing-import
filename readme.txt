=== Import into Easy Property Listings ===
Author URI: https://www.realestateconnected.com.au/
Plugin URI: https://wordpress.org/plugins/easy-property-listings-xml-csv-import/
Contributors: mervb1
Donate link: https://easypropertylistings.com.au/support-the-site/
Tags: real estate, easy property listings, wp all import, csv, xml, xls, import, reaxml, jupix, BLM, MLS
Requires at least: 3.3
Tested up to: 5.9.2
Stable Tag: 2.0.7
License: GNU Version 2 or Any Later Version

Import listings into Easy Property Listings with this WP All Import add-on for WordPress. Created for maximum performance.

== Description ==

Import listings into [Easy Property Listings](https://easypropertylistings.com.au/?utm_source=readme&utm_medium=description_tab&utm_content=description&utm_campaign=wordpressorg_import) with this advanced WP All Import add-on with ease and faster than ever. This plugin has over 150 custom fields pre-configured for you and supports XML, CSV, XLS files and you can fully automate imports with <a href="http://www.wpallimport.com/">WP All Import Pro</a> server cron jobs.

Our goal with this add-on is not to just be able to import listings into Easy Property Listings but to improve import speed especially with images.

Supported formats are CSV, XML and XLS files with full support for the Australian REAXML format when using the [FeedSync Pre-Processor](https://easypropertylistings.com.au/extensions/feedsync/?utm_source=readme&utm_medium=description_tab&utm_content=feedsync&utm_campaign=wordpressorg_import_feedsync) and Jupix UK formats. We have implemented an image and date/time skipping to minimise image imports so they are only updated when changed. We are seeing a 78% speed improvement using this plugin.

* Requires Easy Property Listings plugin for WordPress.
* Requires WP All Import.

> <strong>Support</strong><br>
> Need help configuring your imports? Not a problem, head over to the [Support Pricing](https://easypropertylistings.com.au/support-ticket/pricing/?utm_source=readme&utm_medium=description_tab&utm_content=support_pricing&utm_campaign=wordpressorg_import) page and purchase a plan or installation service.

== Installation ==

1. Install the plugin from Dashboard > Plugins > Add New > and search for Easy Property Listings Import, install and activate the plugin.
2. Download [Import Scripts](https://easypropertylistings.com.au/extensions/import-into-easy-property-listings/?utm_source=readme&utm_medium=description_tab&utm_content=import_scripts&utm_campaign=wordpressorg_import) from Easy Property Listings.
3. Import the pre-configured scripts for WP All Import -> Dashboard > All Import > Settings > Templates.
4. Configure your imports from Dashboard > All Import > New Import.
5. Enable: Activate once initial import is set option from Dashboard > Easy Property Listings > Extensions > WP All Import Add-On

== Screenshots ==

1. Advanced Import Record Skipping Setting
2. Import Job Easy Property Listings Field Section
3. Example of a few of the 150+ custom fields
4. Detailed log entries during imports
5. Extended log entry only updating listing if necessary saving space and time
6. Recommend Import settings

== Change log ==

= 2.0.7 March 29, 2022 =

* Fix: PHP warning notice for $epl_meta_box['post_type'] when it's string by typecasting to array.

= 2.0.6 March 23, 2022 =

* Tweak: Treat EPL core post types differently vs extension post types, loading all the meta fields of core if post type is from EPL core. So a single import script can be used for all core post types.
* Fix: Undefined download_image notice and related warnings.

= 2.0.5 February 23, 2022 =

* Tweak: Better support for Easy Property Listings extensions custom fields in importer.

= 2.0.4 January 13, 2022 =

* Fix: Check if post type is allowed in epl all import.

= 2.0.3 July 10, 2020 =

* Fix: Updated image processing code for WP All Import Pro >= 4.6.1 with compatibility for lower versions.
* Fix: Fields are now correctly skipping when they are unchecked to update and record skipping is disabled.

= 2.0.2 February 26, 2020 =

* Fix: Fields with empty values '', false, 0 can be updated.

= 2.0.1 February 7, 2020 =

* Tweak: Removed global loading field meta.
* Tweak: Altered code loading to prevent conflict with PilotPress plugin.

= 2.0.0 February 5, 2020 =

* New: Select and specifically override Easy Property Listings Custom Fields. You can now update all, update specific fields, or leave some alone.
* New: Filter epl_wpimport_default_skip_fields_list allowing record skipping of specific fields when importer is set to update everything. This allows you to use the featured listing system in EPL with imported data and by default will not update property_featured, property_year_built, property_owner custom fields. Add more to the epl_wpimport_default_skip_fields_list array as required.
* New: Modified date/time filter epl_import_mod_time added allowing support for other data formats.
* New: Better logging output so you know specifically what is happening to all EPL fields during import.
* New: Support for custom meat fields to use 'import' => 'preserve' which will automatically enable record skipping of custom added meta fields.
* New: Image filter epl_import_image_new_mod_date enabling image record skipping process for custom feeds.
* Tweak: WP All Import rapid-addon updated to 1.1.1 version.
* Tweak: Revised log messaging for better clarity on exactly what is happing during the import process.
* Tweak: Updated fallback meta fields to EPL 3.4.21.
* Fix: Bold formatting issue in log.
* Fix: Security improvements.

= 1.0.11 October 10, 2017 =

* Fix: Critical fix for WP All Import Pro 4.5, this fix is backward compatible with 4.4.5 and earlier versions. Ensure you update the importer add-on to minimise listing processing data.

= 1.0.10 July 7, 2017 =

* Fix: Implemented loading files for cron processing.
* Fix: Implemented additional checks for alternate image modified time node in REAXML.
* Tweak: Image checking on new FeedSync Image Modified field.

= 1.0.9 May 9, 2017 =

* Fix: Image Modified Time Filter.

= 1.0.8 March 29, 2017 =

* Fix: Altered plugin loading order to prevent it appearing disabled in certain cases.

= 1.0.7 January 17, 2017 =

* Tweak: Check for missing images during processing and re-import if necessary.
* Fix: Undefined error if Easy Property Listings is not activated.
* New: Date Processing function for EAC date format.

= 1.0.6 November 28, 2016 =

* New: Additional fields added for Easy Property Listings 3.1 compatibility. Pet Friendly, Open Spaces.
* Tweak: Exclude importing empty fields.

= 1.0.5 March 7, 2016 =

* Fix: Reduced queries and implemented further optimisations.

= 1.0.4 December 22, 2015 =

* New: Added translation files and set textdomain to strings.
* New: Added WP All Import notice if not activated.
* Tweak: Log entry wording adjusted and translation strings added.
* Fix: Additional plugin loading order tweaks made.

= 1.0.3 December 19, 2015 =

* Fix: Corrected plugin loading order which occurred in some cases.

= 1.0.2 December 9, 2015 =

* Tweak: Included meta-boxes from Easy Property Listings core for import to correctly run with cron.
* Fix: Import corrected while doing cron job import.

= 1.0.1 December 3, 2015 =

* Tweak: Added action date field.

= 1.0 December 2, 2015 =

* Full Release Version.
* New: Logger added.
* New: Image Skipping and updating implemented.

= 0.9 October 19, 2015 =

* Beta release.
