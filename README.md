Theatre WP
==========
[Theatre WP website](http://www.bolorino.net/theatre-wp-wordpress-plugin-performing-arts/)

- WordPress plugin
- Requires at least: 4.1.2
- Tested up to: 4.2.2
- Stable tag: 0.57
- License: GPLv2 or later
- License URI: http://www.gnu.org/licenses/gpl-2.0.html

Management of Productions and Performances for Theatre and Performing Arts Companies. WordPress Plugin.

Description
-----------
The plugin "Theatre WP" makes easy the work of developers and designers who use WordPress to create websites for troupes and Performing Arts Companies.

Theatre WP is intended for designers and developers using WordPress to setup a performing arts company website.
This plugin provides productions and performances management.

[Demo site](http://theatrewp.bolorino.net/)

What does the plugin include?
-----------------------------
* Custom posts for Shows, Performances and Sponsors
* Widget for upcoming Performances of a particular Show
* Widget for upcoming Performances in general
* Widget for Production Sponsors
* Translation into English, Russian and Spanish

Plugin benefits
------------------------
* Adapted to the basic needs of most Theater Companies
* Organized and well structured information of Shows and Performances to facilitate their management and accessibility
* Independent of the theme used in WordPress
* Customizable
* Translatable
* Open to improvements and suggestions

ToDo
--------------
* Better options panel
* Hookable
* Cleaner default templates
* Translation into other languages

Notes for developers/designers
------------------------------
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

Installation
------------
Use automatic installer

Frequently Asked Questions
--------------------------
Does someone have a question?

Not yet.

Screenshots
-----------
![Screenshot 1](/assets/screenshot-1.png "Design using Theatre WP Plugin")
![Screenshot 2](/assets/screenshot-2.png "Edit Show")
![Screenshot 3](/assets/screenshot-3.png "Edit Performance")
![Screenshot 4](/assets/screenshot-4.png "Types of Production")
![Screenshot 5](/assets/screenshot-5.png "Productions")

Changelog
## 0.57
* New option to select sponsors in spectacle edition as list instead of check boxes
* Minor bugfixes

### 0.56
* WordPress 4.2
* Minor bugfixes

### 0.55
* WordPress 4.1.1
* Partial Catalan translation

### 0.54
* WordPress 4.1

### 0.53
* Excerpt added to Performances
* Function to check if there are performances $theatrewp->are_there_performances();
* Minor bug fixes

### 0.52
* Fix missing translations

### 0.51
* Works now with (almost) any theme!
* Content filtered instead of using custom templates inside plugin
* Define TWP_THEME in your custom theme if using templates for productions and performances to avoid duplicate content
* Fix: return get_bloginfo instead bloginfo in get_production_cat_url
* Fix: option_post_per_page returns empty

### 0.50
* WordPress 4.0
* Custom posts info in Dashboard (At a glance)
* Cleanup activation and deactivation hooks

### 0.49
* Fix plugin version

### 0.48
* Fix performance meta update error

### 0.47
* Fix redundant object creation
* Update performances meta
* Performances templates fixes
* Fix audience array
* [Polylang] List only translated shows in performance edit

### 0.46
* Performances Polylang compatibility
* Performances fixes

### 0.45
* WordPress 3.9.2
* Performances fixes and pagination

### 0.44
* Widget API update. (Warning! You need to setup again your TheatreWP widgets after this update)

### 0.43
* Fixes
* Widget class
* Template updates
* Pot file updates

### 0.42
* WordPress 3.9
* Get Production Category URL

### 0.41
* WordPress 3.8.2
* Production duration

### 0.40
* Filter productions and performances list
* Dates localization
* Hierarchical productions taxonomies (types of productions)
* Productions widget sort options
* Bug: get_spectacle_link by title not post_name
* Remove home video custom post
* Russian translation (thanks to veshinak)

### 0.39
* Fix empty sponsors

### 0.38
* Sponsors management and widget
* Home video
* Various bug fixes

### 0.37
* Fixed custom slug spectacle links

### 0.36
* Fixed Credits display

### 0.35
* Fixed Spectacle Admin UI

### 0.33
* Fixed event's date selection

### 0.32
* WordPress 3.8

### 0.3
* Fixed multiple objects instances
* Basic plugin settings
* Display performance location's map
* Auto build of custom post permalinks

### 0.2
* First Beta version
