=== Theatre WP ===
Contributors: bollofino
Donate link:
Tags: theatre, troupe, dance, performing arts
Requires at least: 4.4
Tested up to: 4.5.2
Stable tag: 0.63
Text Domain: theatrewp
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Management of Productions and Performances for Performing Arts Companies.

== Description ==

The plugin "Theatre WP" makes easy the work of developers and designers who use WordPress to create websites for troupes and Performing Arts Companies.

Theatre WP is intended for designers and developers using WordPress to setup a performing arts company website.
This plugin provides productions and performances management.

**What does the plugin include?**

* Custom posts for Productions, Performances and Sponsors
* Widget for upcoming Performances of a particular Show
* Widget for upcoming Performances in general
* Widget for Production Sponsors
* Translation into English, Spanish and Russian

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
* Translation into other languages

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

= 0.56 =
* WordPress 4.2
* Minor bugfixes

= 0.55 =
* Partial Catalan translation
* WordPress 4.1.1

= 0.54 =
* WordPress 4.1

= 0.53 =
* Excerpt added to Performances
* Function to check if there are performances $theatrewp->are_there_performances();
* Minor bug fixes

= 0.52 =
* Fix translations

= 0.51 =
* Works now with (almost) any theme!
* Content filtered instead of using custom templates inside plugin
* Define TWP_THEME in your custom theme if using templates for productions and performances to avoid duplicate content
* Fix: return get_bloginfo instead bloginfo in get_production_cat_url
* Fix: option_post_per_page returns empty

Full changelog in the plugin website
http://www.bolorino.net/theatre-wp-wordpress-plugin-performing-arts/
