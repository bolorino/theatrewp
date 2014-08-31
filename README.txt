=== Theatre WP ===
Contributors: bollofino
Donate link:
Tags: theatre, troupe, dance, performing arts
Requires at least: 3.9
Tested up to: 3.9.2
Stable tag: 0.49
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Management of Productions and Performances for Performing Arts Companies.

== Description ==

The plugin "Theatre WP" makes easy the work of developers and designers who use WordPress to create websites for troupes and Performing Arts Companies.

Theatre WP is intended for designers and developers using WordPress to setup a performing arts company website.
This plugin provides productions and performances management.

Demo site: http://theatrewp.bolorino.net/

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

* Better options panel
* Hookable
* Cleaner default templates
* Translation into other languages

== Notes for developers/designers ==
There are six templates within the plugin, used if the current theme does not include them:

* single-spectacle.php # Single Show page
* single-performance.php # Single Performance page
* archive-spectacle.php # List of available Shows
* archive-performance.php # List of available Performances
* check-dates-form.php # To filter performances by date
* taxonomy-format.php # Productions by category

To customize these templates, copy the files located in includes/templates within the plugin directory to your theme's directory and modify it to your liking.

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
= 0.49 =
* Fix plugin version

= 0.48 =
* Fix performance meta update error
Please, upgrade!

= 0.47 =
* Fix redundant object creation
* Upgrade performances meta
* Fix audience array
* [Polylang] List only translated shows in performance edit

= 0.46 =
* Performances Polylang compatibility
* Performances fixes
* A Theatre WP theme is coming soon for you to use it as a template. If you are using this plugin in your own theme, please add the functions below in your theme functions.php to enable performances pagination:
https://gist.github.com/bolorino/6637ef0d81395590e5a3
Checkout the changes in the archive-performance.php template.

Please, see full changelog in the plugin website
http://www.bolorino.net/theatre-wp-wordpress-plugin-performing-arts/

== Upgrade Notice ==
= 0.49 =
Fix plugin version
