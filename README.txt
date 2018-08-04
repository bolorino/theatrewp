=== Theatre WP ===
Contributors: bollofino
Donate link:
Tags: theatre, troupe, dance, performing arts
Requires at least: 4.4
Tested up to: 4.8
Stable tag: 0.67
Text Domain: theatre-wp
Domain Path: /languages
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Management of Productions and Performances for Performing Arts Companies.

== Description ==

The plugin "Theatre WP" helps developers and designers who use WordPress to create websites for troupes and Performing Arts Companies.

Theatre WP is intended for designers and developers using WordPress to setup a performing arts company website.
This plugin provides productions and performances management.

**What does the plugin include?**

* Custom posts for Productions, Performances and Sponsors
* Widget for upcoming Performances of a particular Show
* Widget for upcoming Performances in general
* Widget for Production Sponsors

**Plugin benefits**

* Adapted to the basic needs of most Theater Companies
* Organized and well structured information of Shows and Performances to facilitate their management and accessibility
* Independent of the theme used in WordPress
* Customizable
* Translatable
* Open to improvements and suggestions

**ToDo**

* Hookable
* Cleaner default templates

== Notes for developers/designers ==

There are six templates within the plugin as examples to use it in your custom theme:

* single-spectacle.php # Single Show page
* single-performance.php # Single Performance page
* archive-spectacle.php # List of available Shows
* archive-performance.php # List of available Performances
* check-dates-form.php # To filter performances by date
* taxonomy-format.php # Productions by category

You can copy the files located in includes/templates within the plugin directory to your theme's directory and modify it to your liking.

Define TWP_THEME constant in your theme if using the templates to avoid duplicate content:
define( 'TWP_THEME', true );

Have a look into includes/classes/class-theatre-wp.php for functions to use in your templates.

== Installation ==

Use automatic installer

== Frequently Asked Questions ==

= Does someone have a question? =

Not yet.

== Screenshots ==

1. Design using Theatre WP Plugin
2. Edit Show
3. Edit Performance
4. Types of Production
5. Productions

== Changelog ==

= 0.67 =
* Localization fixes
* Tickets information fixes

= 0.66 =
* WordPress 4.7
* New setup option: Google Maps API
* Set desired thumbnail size in $theatre_wp->get_spectacle_data
* New template function to get the featured image in all available sizes (get_spectacle_thumbnail)
* Enable “tag” taxonomy for shows
* Added get_busy_dates
* Added tickets options for performances

= 0.65 =
* Fix. Display future years in performaces select box

= 0.64 =
* WordPress 4.6
* Fix limit in performances widget
* Added missing performance region field

= 0.63 =
* Fix options saving

= 0.62 =
* First extensible filter: twp_define_audiences
* Tabbed options panel

= 0.61 =
* Fix missing javascript
* Select the number of performances to display in upcoming performances widget

= 0.60 =
* Fix datepicker in performances (by now it supports default WordPress date formats)
* Fix draft performances displaying
* Fix performance map language
* French translation (merci to Jean-Bernard Huet)

= 0.59 =
* WordPress 4.4
* Fix productions list in admin with multiple languages
* Fix custom posts dashboard icons

= 0.58 =
* WordPress 4.3

= 0.57 =
* New option to select sponsors in spectacle edition as list instead of check boxes
* Minor bugfixes

Full changelog in the plugin website
https://www.bolorino.net/theatre-wp-wordpress-plugin-performing-arts/

== Upgrade Notice ==
= 0.66 =
* New setup option: Google Maps API. Add your API key to display map in performance.
