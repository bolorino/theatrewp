<?php
/**
 * TWP_Setup class.
 *
 * Plugin setup class
 *
 * @package TheatreWP
 * @author  Jose Bolorino <jose.bolorino@gmail.com>
 */

if ( realpath(__FILE__) === realpath($_SERVER['SCRIPT_FILENAME']) )
	exit('Do not access this file directly.');

class TWP_Setup {

	protected static $plugin_dir;

	protected static $default_spectacle_name      = 'Spectacle';

	protected static $default_spectacles_name     = 'Spectacles';

	protected static $default_spectacle_slug      = 'spectacle';

	protected static $default_spectacles_slug     = 'spectacles';

	protected static $default_performance_name    = 'Performance';

	protected static $default_performances_name   = 'Performances';

	protected static $default_performance_slug    = 'performance';

	protected static $default_performances_slug   = 'performances';

	protected static $default_spectacles_number   = 5;

	protected static $default_performances_number = 5;

	public $performance;

	public $spectacle;

	public $sponsor;

	public static $default_options = array();

	/**
	 * List of available templates
	 * @var array
	 */
	public static $templates = array(
		'single-spectacle'    => 'single-spectacle.php',
		'single-performance'  => 'single-performance.php',
		'archive-spectacle'   => 'archive-spectacle.php',
		'archive-performance' => 'archive-performance.php'
		);

	public static $twp_dateformat;

	public function __construct( $plugin_dir, $spectacle, $performance, $sponsor ) {
		self::$plugin_dir = $plugin_dir;

		self::$default_spectacle_slug    = ( get_option( 'twp_spectacle_slug' ) ? get_option( 'twp_spectacle_slug' ) : self::$default_spectacle_slug );
		self::$default_spectacles_slug   = ( get_option( 'twp_spectacles_slug' ) ? get_option( 'twp_spectacles_slug' ) : self::$default_spectacles_slug );

		self::$default_performance_slug  = ( get_option( 'twp_performance_slug' ) ? get_option( 'twp_performance_slug' ) : self::$default_performance_slug );
		self::$default_performances_slug = ( get_option( 'twp_performances_slug' ) ? get_option( 'twp_performances_slug' ) : self::$default_performances_slug );

		self::$default_spectacle_name    = ( get_option( 'twp_spectacle_name' ) ? get_option( 'twp_spectacle_name' ) : self::$default_spectacle_name );
		self::$default_spectacles_name   = ( get_option( 'twp_spectacles_name' ) ? get_option( 'twp_spectacles_name' ) : self::$default_spectacles_name );

		self::$default_performance_name  = ( get_option( 'twp_performance_name' ) ? get_option( 'twp_performance_name' ) : self::$default_performance_name );
		self::$default_performances_name = ( get_option( 'twp_performances_name' ) ? get_option( 'twp_performances_name' ) : self::$default_performances_name );

		self::$default_options = array(
			'twp_version'			  => Theatre_WP::$version,
			'twp_spectacle_name'      => self::$default_spectacle_name,
			'twp_spectacles_name'     => self::$default_spectacles_name,
			'twp_spectacle_slug'      => sanitize_title( self::$default_spectacle_slug, false, 'save' ),
			'twp_spectacles_slug'     => sanitize_title( self::$default_spectacles_slug, false, 'save' ),
			'twp_performance_name'    => self::$default_performance_name,
			'twp_performances_name'   => self::$default_performances_name,
			'twp_performance_slug'    => sanitize_title( self::$default_performance_slug, false, 'save' ),
			'twp_performances_slug'   => sanitize_title( self::$default_performances_slug, false, 'save' ),
			'twp_spectacles_number'   => self::$default_spectacles_number,
			'twp_performances_number' => self::$default_performances_number,
			'twp_clean_on_uninstall'  => 0
		);

		self::$twp_dateformat = get_option( 'date_format');

		$this->spectacle   = $spectacle;
		$this->performance = $performance;
		$this->sponsor     = $sponsor;

		// Actions
		add_action( 'init', array( $this, 'init' ), 0 );
		add_action( 'plugins_loaded', array( $this, 'load_plugin_textdomain' ) );
		add_action( 'widgets_init', array( $this, 'init_widgets' ) );
	}

	/**
	 * Init Theatre WordPress plugin.
	 *
	 * @access public
	 * @return void
	 */
	public function init() {

		// Setup custom posts
		add_action( 'init', array( $this, 'create_spectacles' ) );
		add_action( 'init', array( $this, 'create_performances' ) );
		add_action( 'init', array( $this, 'create_sponsors' ) );

		add_action( 'init', array( $this, 'twp_metaboxes' ) );

		// Filters
		// Default custom posts templates
		add_filter( 'single_template', array( $this, 'get_twp_single_template' ) );
		add_filter( 'archive_template', array( $this, 'get_twp_archive_template' ) );

		// Enable a different post_per_page param for custom post
		add_filter( 'option_posts_per_page', array( 'TWP_Setup', 'twp_option_post_per_page' ) );

		// Format title for custom post archives
		// add_filter( 'wp_title', array( 'TWP_Setup', 'twp_taxonomy_title' ) );

		// Admin menu
		if ( is_admin() ) {
			add_action( 'admin_menu', array( 'TWP_Setup', 'twp_menu' ) );
			add_action( 'admin_init', array( 'TWP_Setup', 'twp_register_settings' ) );

			add_action( 'admin_init', array( $this, 'build_taxonomies' ), 0 );

			// Update rewrite rules after Options update
			add_action( 'update_option_twp-main', array('TWP_Setup', '_update_rewrite_rules') );

			add_filter( 'manage_edit-performance_columns', array( 'TWP_Setup', 'twp_performances_columns' ) );
			add_action( 'manage_performance_posts_custom_column', array( $this, 'twp_manage_performances_columns' ), 10, 2);
		}

	}

	/**
	 * Fired when the plugin is activated.
	 *
	 * @since    0.2
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses "Network Activate" action, false if WPMU is disabled or plugin is activated on an individual blog.
	 */
	public static function activate( $network_wide ) {
		self::twp_register_settings();

		// Set default options
		foreach ( self::$default_options as $key => $value ) {
			update_option( $key, $value );
		}

		self::_update_rewrite_rules();
	}

	/**
	 * Fired when the plugin is deactivated.
	 *
	 * @since    0.2
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses "Network Deactivate" action, false if WPMU is disabled or plugin is deactivated on an individual blog.
	 */
	public static function deactivate( $network_wide ) {
		// Should custom post be removed?
		if ( get_option( 'twp_clean_on_uninstall' ) == '1' ) {
			self::_remove_all_data();
		}

		self::twp_unregister_settings();

		flush_rewrite_rules();
	}

	public function init_widgets( ) {
		register_widget( 'TWP_Spectacles_Widget' );
		register_widget( 'TWP_Upcoming_Performances_Widget' );
		register_widget( 'TWP_Show_Upcoming_Performances_Widget' );
		register_widget( 'TWP_Production_Sponsors_Widget' );
	}

	/**
	 * Update rewrite rules.
	 *
	 * @since    0.3
	 *
	 */
	private static function _update_rewrite_rules() {
		// @TODO format rule

		flush_rewrite_rules();

		// Set rewrite rules
		global $wp_rewrite;
		$spectacle_slug   = self::$default_options['twp_spectacle_slug'];
		$performance_slug = self::$default_options['twp_performance_slug'];

		// Spectacles archive and pagination
		add_rewrite_rule( self::$default_options['twp_spectacles_slug'] . '$', 'index.php?post_type=spectacle', 'top' );
		add_rewrite_rule( self::$default_options['twp_spectacles_slug'] . '/page/([0-9])*/?', 'index.php?post_type=spectacle' . '&paged=$matches[1]', 'top' );

		// Performances archive and pagination
		add_rewrite_rule( self::$default_options['twp_performances_slug'] . '$', 'index.php?post_type=performance', 'top' );
		add_rewrite_rule( self::$default_options['twp_performances_slug'] . '/page/([0-9])*/?', 'index.php?post_type=performance' . '&paged=$matches[1]', 'top' );

		// Single Spectacle and Performance
		add_rewrite_rule( "^$spectacle_slug/([^/]*)/?", 'index.php?spectacle=$matches[1]', 'top' );
		add_rewrite_rule( "^$performance_slug/([^/]*)/?", 'index.php?performance=$matches[1]', 'top' );

		// format/([^/]+)/page/?([0-9]{1,})/?$	index.php?format=$matches[1]&paged=$matches[2]
		// format/([^/]+)/?$	index.php?format=$matches[1]

		$wp_rewrite->flush_rules();
	}

	/**
	 * Remove all plugin generated custom posts.
	 *
	 * @access private
	 * @return void
	 */
	private static function _remove_all_data() {
		// Remove productions

		$twp_spectacle_custom_posts = get_posts( array(
			'numberposts'	=> -1,
			'post_type'		=> 'spectacle',
			'post_status'	=> 'any'
			)
		);

		foreach ( $twp_spectacle_custom_posts as $twp_spectacle ) {
			// Delete post meta
			delete_post_meta( $twp_spectacle->ID, Theatre_WP::$twp_prefix . 'synopsis' );
			delete_post_meta( $twp_spectacle->ID, Theatre_WP::$twp_prefix . 'audience' );
			delete_post_meta( $twp_spectacle->ID, Theatre_WP::$twp_prefix . 'duration' );
			delete_post_meta( $twp_spectacle->ID, Theatre_WP::$twp_prefix . 'credits' );
			delete_post_meta( $twp_spectacle->ID, Theatre_WP::$twp_prefix . 'sheet' );
			delete_post_meta( $twp_spectacle->ID, Theatre_WP::$twp_prefix . 'prod-sponsor' );
			delete_post_meta( $twp_spectacle->ID, Theatre_WP::$twp_prefix . 'video' );

			// Delete post
			wp_delete_post( $twp_spectacle->ID, true );
		}

		// Remove performances
		$twp_performance_custom_posts = get_posts( array(
			'numberposts'	=> -1,
			'post_type'		=> 'performance',
			'post_status'	=> 'any'
			)
		);

		foreach ( $twp_performance_custom_posts as $twp_performance ) {
			// Delete post meta
			delete_post_meta( $twp_performance->ID, Theatre_WP::$twp_prefix . 'spectacle_id' );
			delete_post_meta( $twp_performance->ID, Theatre_WP::$twp_prefix . 'date_first' );
			delete_post_meta( $twp_performance->ID, Theatre_WP::$twp_prefix . 'date_last' );
			delete_post_meta( $twp_performance->ID, Theatre_WP::$twp_prefix . 'event' );
			delete_post_meta( $twp_performance->ID, Theatre_WP::$twp_prefix . 'place' );
			delete_post_meta( $twp_performance->ID, Theatre_WP::$twp_prefix . 'address' );
			delete_post_meta( $twp_performance->ID, Theatre_WP::$twp_prefix . 'postal_code' );
			delete_post_meta( $twp_performance->ID, Theatre_WP::$twp_prefix . 'town' );
			delete_post_meta( $twp_performance->ID, Theatre_WP::$twp_prefix . 'region' );
			delete_post_meta( $twp_performance->ID, Theatre_WP::$twp_prefix . 'country' );
			delete_post_meta( $twp_performance->ID, Theatre_WP::$twp_prefix . 'display_map' );

			// Delete post
			wp_delete_post( $twp_performance->ID, true );
		}

		// Remove sponsors
		$twp_sponsor_custom_posts = get_posts( array(
			'numberposts'	=> -1,
			'post_type'		=> 'sponsor',
			'post_status'	=> 'any'
			)
		);

		foreach ( $twp_sponsor_custom_posts as $twp_sponsor ) {
			delete_post_meta( $twp_sponsor->ID, Theatre_WP::$twp_prefix . 'sponsor-url' );
			delete_post_meta( $twp_sponsor->ID, Theatre_WP::$twp_prefix . 'sponsor-weight' );

			// Delete post
			wp_delete_post( $twp_sponsor->ID, true );
		}

		// Delete plugin options
		foreach ( self::$default_options as $key => $value ) {
			delete_option( $key );
		}
	}

	/**
	 * Define Spectacles custom post.
	 *
	 * @access public
	 * @return void
	 */
	public function create_spectacles() {
		$spectacles_args = array(
			'labels' => array(
				'name'          => self::$default_options['twp_spectacles_name'],
				'singular_name' => self::$default_options['twp_spectacle_name'],
				'add_new'       => __( 'Add new', 'theatrewp' ),
				'add_new_item'  => __( 'Add new Show', 'theatrewp' ),
				'edit_item'     => __( 'Edit Show', 'theatrewp' ),
				'new_item'      => __( 'New Show', 'theatrewp' ),
				'view'          => __( 'View Show', 'theatrewp' ),
				'view_item'     => __( 'View Show', 'theatrewp' ),
				'search_items'  => __( 'Search Shows', 'theatrewp' )
				),
			'singular_label'  => __( 'Show', 'theatrewp' ),
			'public'          => true,
			'has_archive'     => self::$default_options['twp_spectacles_slug'],
			'rewrite'         => array( 'slug' => self::$default_options['twp_spectacle_slug'] ),
			'show_ui'         => true,
			'hiearchical'	  => true,
			//'taxonomies'	  => array( 'format' ),
			'menu_position'   => 5,
			'supports'        => array( 'title', 'editor', 'excerpt', 'thumbnail' )
			);

		register_post_type( 'spectacle', $spectacles_args );

		$this->build_taxonomies();

		return;
	}

	/**
	 * Define Performances custom post.
	 *
	 * @access public
	 * @return void
	 */
	public function create_performances() {
		$performances_args = array(
			'labels' => array(
				'name'          => self::$default_options['twp_performances_name'],
				'singular_name' => self::$default_options['twp_performance_name'],
				'add_new'       => __('Add new', 'theatrewp'),
				'add_new_item'  => __('Add new Performance', 'theatrewp'),
				'edit_item'     => __('Edit Performance', 'theatrewp'),
				'new_item'      => __('New Performance', 'theatrewp'),
				'view'          => __('View Performances', 'theatrewp'),
				'view_item'     => __('View Performance', 'theatrewp'),
				'search_items'  => __('Search Performance', 'theatrewp')
				),
			'singular_label'  => __('Performance', 'theatrewp'),
			'public'          => true,
			'has_archive'     => self::$default_options['twp_performances_slug'],
			'rewrite'         => array( 'slug' => self::$default_options['twp_performance_slug'] ),
			'exclude_from_search' => false,
			'capability_type' => 'post',
			'menu_position'   => 6,
			'supports'        => array( 'title' )
			);

		register_post_type( 'performance', $performances_args );

		return;
	}

	/**
	 * Define Sponsor custom post.
	 *
	 * @access public
	 * @return void
	 */
	public function create_sponsors() {
		$sponsors_args = array(
			'labels' => array(
				'name'          => __( 'Sponsors', 'theatrewp' ),
				'singular_name' => __( 'Sponsor', 'theatrewp' ),
				'add_new'       => __( 'Add new', 'theatrewp' ),
				'add_new_item'  => __( 'Add new Sponsor', 'theatrewp' ),
				'edit_item'     => __( 'Edit Sponsor', 'theatrewp' ),
				'new_item'      => __( 'New Sponsor', 'theatrewp' ),
				'view'          => __( 'View Sponsors', 'theatrewp' ),
				'view_item'     => __( 'View Sponsor', 'theatrewp' ),
				'search_items'  => __( 'Search Sponsors', 'theatrewp' )
				),
			'singular_label'    => __( 'Sponsor', 'theatrewp' ),
			'public'            => true,
			'show_in_nav_menus' => true,
			'_builtin'          => false,
			'menu_position'     => 8,
			'supports'          => array( 'title', 'editor', 'thumbnail' )
			);

		register_post_type( 'sponsor', $sponsors_args );

		return;
	}

	/**
	 * Define and register Production Format Taxonomy.
	 *
	 * @access public
	 * @return void
	 */
	public function build_taxonomies () {
		$labels = array(
			'name'			=> _x( 'Types of Production', 'taxonomy general name', 'theatrewp' ),
			'singular_name'	=> _x( 'Type of Production', 'taxonomy singular name', 'theatrewp' ),
			'add_new_item'	=> __( 'New format of Production', 'theatrewp' ),
			'new_item_name' => __( 'New format name', 'theatrewp' ),
			'menu_name'		=> __( 'Format', 'theatrewp' )
		);

		$args = array(
			'hierarchical'      => true,
			'public'            => true,
			'label'             => __( 'Format', 'theatrewp' ),
			'labels'            => $labels,
			'show_ui'           => true,
			'show_in_nav_menus' => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'format' ),
		);

		register_taxonomy( 'format', 'spectacle', $args );
		register_taxonomy_for_object_type( 'format', 'spectacle' );

		return;
	}

	/**
	 * Localisation.
	 *
	 * @access public
	 * @return void
	 */
	public function load_plugin_textdomain() {
		$locale = apply_filters( 'plugin_locale', get_locale(), 'theatrewp' );

		load_plugin_textdomain( 'theatrewp', false, self::$plugin_dir . '/languages' );

	}

	/**
	 * Get the path to the custom posts (spectacle/performance) single templates
	 *
	 * @access public
	 * @return string
	 */
	public function get_twp_single_template( $template ) {

		if ( 'spectacle' == get_post_type( get_queried_object_id() ) && ! $this->_check_theme_templates(self::$templates['single-spectacle']) ) {
			$template = TWP_BASE_PATH . '/includes/templates/single-spectacle.php';
		}

		if ( 'performance' == get_post_type( get_queried_object_id() ) && ! $this->_check_theme_templates(self::$templates['single-performance']) ) {
			$template = TWP_BASE_PATH . '/includes/templates/single-performance.php';
		}

		return $template;

	}

	/**
	 * Get the path to the custom posts (spectacle/performance) archive templates
	 *
	 * @access public
	 * @return string
	 */
	public function get_twp_archive_template( $template ) {
		// Custom post archive pages
		if ( is_post_type_archive( 'performance' ) && ! $this->_check_theme_templates(self::$templates['archive-performance']) ) {
			$template = plugin_dir_path(__FILE__) . '../templates/archive-performance.php';
		}

		if ( is_post_type_archive( 'spectacle' ) && ! $this->_check_theme_templates(self::$templates['archive-spectacle']) ) {
			$template = plugin_dir_path(__FILE__) . '../templates/archive-spectacle.php';
		}

		return $template;
	}

	/**
	 * Checks if template files exists
	 *
	 * @access private
	 * @return bool
	 */
	private function _check_theme_templates( $template)  {
		if ( ! locate_template( $template, false ) ) {
			return false;
		}

		return true;
	}

	/**
	* Adding scripts and styles
	*
	* @access public
	* @return void
	*
	*/
	public function twp_scripts( $hook ) {
		global $wp_version, $wp_locale;

		// only enqueue our scripts/styles on the proper pages
		if ( 'post.php' == $hook || 'post-new.php' == $hook ) {
			$twp_script_array = array( 'jquery-migrate', 'jquery-ui-datepicker' );
			$twp_style_array = array( 'thickbox' );

			wp_register_script( 'twp-timepicker', TWP_META_BOX_URL . 'js/jquery.timePicker.min.js', $twp_script_array, '1.10.3' );
			wp_register_script( 'twp-scripts', TWP_META_BOX_URL . 'js/twp.js', $twp_script_array, '1.10.3', true );

			wp_localize_script( 'twp-scripts', 'twp_ajax_data', array( 'ajax_nonce' => wp_create_nonce( 'ajax_nonce' ), 'post_id' => get_the_ID() ) );

			// Localize js
		    $localize_args = array(
		        'closeText'         => __( 'Done', 'theatrewp' ),
		        'currentText'       => __( 'Today', 'theatrewp' ),
		        'monthNames'        => $this->_strip_array_index( $wp_locale->month ),
		        'monthNamesShort'   => $this->_strip_array_index( $wp_locale->month_abbrev ),
		        'monthStatus'       => __( 'Show a different month', 'theatrewp' ),
		        'dayNames'          => $this->_strip_array_index( $wp_locale->weekday ),
		        'dayNamesShort'     => $this->_strip_array_index( $wp_locale->weekday_abbrev ),
		        'dayNamesMin'       => $this->_strip_array_index( $wp_locale->weekday_initial ),
		        // set the date format to match the WP general date settings
		        'dateFormat'        => self::date_format_php_to_js( get_option('date_format') ),
		        // get the start of week from WP general setting
		        'firstDay'          => get_option( 'start_of_week' ),
		        // is Right to left language? default is false
		        'isRTL'             => ( $wp_locale->text_direction == 'rtl' ? true : false ),
		    );

			wp_localize_script( 'twp-timepicker', 'objectL10n', $localize_args );

			wp_enqueue_script( 'twp-timepicker' );
			wp_enqueue_script( 'twp-scripts' );

			wp_register_style( 'twp-styles', TWP_META_BOX_URL . 'style.css', $twp_style_array );
			wp_enqueue_style( 'twp-styles' );
		}

		// Dashboard custom post icons
		if ( is_admin() ) {
			wp_register_style( 'twp-dashboard', TWP_META_BOX_URL . 'dashboard.css', false );
			wp_enqueue_style( 'twp-dashboard' );
		}

		return true;
	}

	/**
	 * Performances dashboard columns
	 *
	 * @access public
	 * @return array
	 */
	public static function twp_performances_columns( $performance_columns ) {
		$new_columns['cb'] = '<input type="checkbox" />';

		$new_columns['id']         = __( 'ID' );
		$new_columns['title']      = _x( 'Performance', 'column name' );
		$new_columns['spectacle']  = __( 'Spectacle', 'theatrewp' );
		$new_columns['first_date'] = __( 'First Date', 'theatrewp' );
		$new_columns['last_date']  = __( 'Last Date', 'theatrewp' );
		$new_columns['event']      = __( 'Event', 'theatrewp' );

		return $new_columns;
	}

	/**
	 * Performances dashboard columns data
	 *
	 * @access public
	 * @return void
	 */
	public function twp_manage_performances_columns( $column_name, $ID) {
		$meta = $this->performance->get_performance_custom( $this->spectacle, $ID );

		switch ( $column_name ) {
			case 'id':
				echo $ID;
				break;

			case 'spectacle':
				echo $meta['spectacle_title'];
				break;

			case 'first_date':
				echo date( 'd-F-Y', $meta['date_first'] );
				break;

			case 'last_date':
				if ( ! empty( $meta['date_last'] ) && $meta['date_last'] != $meta['date_first'] ) {
					echo date( 'd-F-Y', $meta['date_last'] );
				}
				break;

			case 'event':
				echo $meta['event'];
				break;

			default:
				break;
		}

	}

	/**
	 * TWP Options menu
	 *
	 * @access public
	 * @return void
	 */
	public static function twp_menu() {
		$hook = add_options_page( __('Theatre WP Options', 'theatrewp'), 'Theatre WP', 'manage_options', 'theatre-wp', array( 'TWP_Setup', 'twp_options' ) );

		// Add an action to check if plugin options are updated. Update rewrite rules when they are
		add_action( 'load-' . $hook, array( 'TWP_Setup', 'twp_check_plugin_options_update' ) );
	}

	/**
	 * Check if plugin options were updated
	 *
	 * @access public
	 * @return void
	 */
	public static function twp_check_plugin_options_update() {
		if ( isset( $_GET['settings-updated'] ) && $_GET['settings-updated'] ) {
			self::_update_rewrite_rules();
		}
	}

	/**
	 * Register Settings
	 *
	 * @access public
	 * @return void
	 */
	public static function twp_register_settings() {
		register_setting( 'twp-main', 'twp_version' );
		register_setting( 'twp-main', 'twp_spectacle_name' );
		register_setting( 'twp-main', 'twp_spectacles_name' );
		register_setting( 'twp-main', 'twp_spectacle_slug' );
		register_setting( 'twp-main', 'twp_spectacles_slug' );
		register_setting( 'twp-main', 'twp_performance_name' );
		register_setting( 'twp-main', 'twp_performances_name' );
		register_setting( 'twp-main', 'twp_performance_slug' );
		register_setting( 'twp-main', 'twp_performances_slug' );
		register_setting( 'twp-main', 'twp_spectacles_number', 'intval' );
		register_setting( 'twp-main', 'twp_performances_number', 'intval' );
		register_setting( 'twp-main', 'twp_clean_on_uninstall' );
	}

	/**
	 * Unregister Settings
	 *
	 * @access public
	 * @return void
	 */
	public static function twp_unregister_settings() {
		unregister_setting( 'twp-main', 'twp_version' );
		unregister_setting( 'twp-main', 'twp_spectacle_name' );
		unregister_setting( 'twp-main', 'twp_spectacles_name' );
		unregister_setting( 'twp-main', 'twp_spectacle_slug' );
		unregister_setting( 'twp-main', 'twp_spectacles_slug' );
		unregister_setting( 'twp-main', 'twp_performance_name' );
		unregister_setting( 'twp-main', 'twp_performances_name' );
		unregister_setting( 'twp-main', 'twp_performance_slug' );
		unregister_setting( 'twp-main', 'twp_performances_slug' );
		unregister_setting( 'twp-main', 'twp_spectacles_number', 'intval' );
		unregister_setting( 'twp-main', 'twp_performances_number', 'intval' );
		unregister_setting( 'twp-main', 'twp_clean_on_uninstall' );
	}

	public static function create_slug( $input ) {
		$new_input = array();

		if ( isset( $input['twp_spectacle_name'] ) ) {
			$new_input['twp_spectacle_slug'] = sanitize_title( $input['twp_spectacle_name'], false, 'save' );
		}

		if ( isset( $input['twp_performance_name'] ) ) {
			$new_input['twp_performance_slug'] = sanitize_title( $input['twp_performance_name'], false, 'save' );
		}

		return $new_input;
	}

	/**
	 * Admin Options
	 *
	 * @access public
	 * @return void
	 */
	public static function twp_options() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( __( 'You do not have sufficient permissions to access this page.') );
		}

		include( plugin_dir_path( __FILE__ ) . '../templates/admin/admin-options.php' );
	}

	/**
	 * Filters post per page option for custom posts
	 *
	 * @access public
	 * @return int
	 */
	public static function twp_option_post_per_page( $value ) {
		global $option_posts_per_page;

		if ( is_tax( 'performance' ) ) {
			return get_option( 'twp_performances_number' );
		}

		if ( is_tax( 'spectacle' ) ) {
			return get_option( 'twp_spectacles_number' );
		}

		return $option_posts_per_page;
	}

	// @TODO nice archives title
	// public static function twp_taxonomy_title( $title ) {
	// 	global $paged, $page, $wp_query;
	// 	echo 'twp_taxonomy_title ';

	// 	if ( is_tax( 'performance' ) ) {
	// 		//$title .= ;
	// 		echo 'is_tax ';

	// 		if ( session_id() ) {
	// 			echo 'session_id ';
	// 			$title .= sprintf( __( 'Performances for %s %s' ), $_SESSION['year'], $_SESSION['month'] );
	// 		}
	// 	}

	// 	return $title;
	// }

	/**
	 * Define metaboxes
	 *
	 * @access public
	 * @return void
	 */
	public function twp_metaboxes( ) {
		$TWP_meta_boxes = array(
			array(
				'id'       => 'spectacle-meta-box',
				'title'    => __('Spectacle Options', 'theatrewp' ),
				'pages'    => array( 'spectacle' ),
				'context'  => 'normal',
				'priority' => 'high',
				'fields'   => array(
					array(
						'name' => __( 'Synopsis', 'theatrewp' ),
						'desc' => __( 'Short description', 'theatrewp' ),
						'id' => Theatre_WP::$twp_prefix . 'synopsis',
						'type' => 'textarea',
						'std' => ''
						),
					array(
						'name' => __( 'Audience', 'theatrewp' ),
						'desc' => __( 'Intended Audience', 'theatrewp' ),
						'id' => Theatre_WP::$twp_prefix . 'audience',
						'type' => 'select',
						'options' => TWP_Spectacle::$audience
						),
					array(
						'name' => __( 'Duration', 'theatrewp' ),
						'desc' => __( 'Duration in minutes', 'theatrewp' ),
						'id' => Theatre_WP::$twp_prefix . 'duration',
						'type' => 'text',
						'std' => ''
						),
					array(
						'name' => __( 'Credits', 'theatrewp' ),
						'desc' => __( 'Credits Titles', 'theatrewp' ),
						'id'   => Theatre_WP::$twp_prefix . 'credits',
						'type' => 'wysiwyg',
						'std'  => ''
						),
					array(
						'name' => __( 'Sheet', 'theatrewp' ),
						'desc' => __( 'Technical Sheet', 'theatrewp' ),
						'id'   => Theatre_WP::$twp_prefix . 'sheet',
						'type' => 'textarea',
						'std'  => ''
						),
					array(
						'name' => __( 'Sponsors', 'theatrewp' ),
						'desc' => __( 'Sponsors', 'theatrewp' ),
						'id' => Theatre_WP::$twp_prefix . 'prod-sponsor',
						'type' => 'multicheckbox',
						'options' => $this->sponsor->get_sponsors_titles()
					),
					array(
						'name' => __( 'Video', 'theatrewp' ),
						'desc' => __( 'Video Code. The code of the video in YouTube or Vimeo', 'theatrewp' ),
						'id'   => Theatre_WP::$twp_prefix . 'video',
						'type' => 'textarea',
						'std'  => ''
						)
					)
				),
			array (
				'id'       => 'performance-meta-box',
				'title'    => __( 'Performance Options', 'theatrewp' ),
				'pages'    => array( 'performance' ),
				'context'  => 'normal',
				'priority' => 'high',
				'fields'   => array(
					array(
						'name'    => __( 'Show', 'theatrewp' ),
						'desc'    => __( 'Performing Show', 'theatrewp' ),
						'id'      => Theatre_WP::$twp_prefix . 'spectacle_id',
						'type'    => 'select',
						'options' => $this->spectacle->get_spectacles_array()
						),
					array(
						'name' => __( 'First date', 'theatrewp' ),
						'desc' => __( 'First performing date. [Date selection / Time]', 'theatrewp' ),
						'id'   => Theatre_WP::$twp_prefix . 'date_first',
						'type' => 'text_datetime_timestamp',
						'std'  => '',
						// jQuery date picker options. See here http://jqueryui.com/demos/datepicker
						'js_options' => array(
							'appendText'	=> '(yyyy-mm-dd)',
							'autoSize'		=> true,
							'buttonText'	=> __( 'Select Date', 'theatrewp' ),
							'dateFormat'	=> __( 'dd-mm-yyyy', 'theatrewp' ),
							'showButtonPanel' => true
							)
						),
					array(
						'name' => __( 'Last date', 'theatrewp' ),
						'desc' => __( 'Last performing date. [Date selection / Time]', 'theatrewp' ),
						'id'   => Theatre_WP::$twp_prefix . 'date_last',
						'type' => 'text_datetime_timestamp',
						'std'  => ''
						),
					array(
						'name' => __( 'Event', 'theatrewp' ),
						'desc' => __( 'Event in which the show is performed (Festival, Arts Program...)', 'theatrewp' ),
						'id'   => Theatre_WP::$twp_prefix . 'event',
						'type' => 'text',
						'std'  => ''
						),
					array(
						'name' => __( 'Stage', 'theatrewp' ),
						'desc' => __( 'Where is the Show to be played (Theatre)', 'theatrewp' ),
						'id'   => Theatre_WP::$twp_prefix . 'place',
						'type' => 'text',
						'std'  => ''
						),
					array(
						'name' => __( 'Theatre Address', 'theatrewp' ),
						'desc' => '',
						'id'   => Theatre_WP::$twp_prefix . 'address',
						'type' => 'text',
						'std'  => ''
						),
					array(
						'name' => __( 'Postal Code', 'theatrewp' ),
						'desc' => '',
						'id'   => Theatre_WP::$twp_prefix . 'postal_code',
						'type' => 'text',
						'std'  => ''
						),
					array(
						'name' => __( 'Town', 'theatrewp' ),
						'desc' => __( 'Performing in this Town', 'theatrewp' ),
						'id'   => Theatre_WP::$twp_prefix . 'town',
						'type' => 'text',
						'std'  => ''
						),
					array(
						'name' => __( 'Region', 'theatrewp' ),
						'desc' => __( 'e.g. Province, County...', 'theatrewp' ),
						'id'   => Theatre_WP::$twp_prefix . 'region',
						'type' => 'text',
						'std'  => ''
						),
					array(
						'name' => __( 'Country', 'theatrewp' ),
						'desc' => '',
						'id'   => Theatre_WP::$twp_prefix . 'country',
						'type' => 'text',
						'std'  => ''
						),
					array(
						'name' => __( 'Display Map', 'theatrewp' ),
						'desc' => __( 'Check to display map', 'theatrewp' ),
						'id'   => Theatre_WP::$twp_prefix . 'display_map',
						'type' => 'checkbox',
						'std'  => ''
						)
					)
			),
			array(
				'id' => 'sponsor-meta-box',
			    'title' => __( 'Sponsor', 'theatrewp' ),
			    'pages' => array( 'sponsor' ),
			    'context' => 'normal',
			    'priority' => 'high',
			    'fields' => array(
			        array(
			            'name' => __( 'Link', 'theatrewp' ),
			            'desc' => __( 'Sponsor Link', 'theatrewp' ),
			            'id' => Theatre_WP::$twp_prefix . 'sponsor-url',
			            'type' => 'text',
			            'std' => 'http://'
			        ),
			        array(
			            'name' => __( 'Weight', 'theatrewp' ),
			            'desc' => __( 'A number between 0 and 99 to set the importance. 99 is higher', 'theatrewp' ),
			            'id'   => Theatre_WP::$twp_prefix . 'sponsor-weight',
			            'type' => 'text',
			            'std' => '0'
			        )
			    )
			)
		);

		foreach ( $TWP_meta_boxes as $meta_box ) {
		    $my_box = new TWP_Metaboxes( $meta_box );
		}

	}

	private function _strip_array_index( $array_to_strip ) {
		foreach( $array_to_strip as $array_item ) {
        	$new_array[] =  $array_item;
	    }

	    return $new_array;
	}

	public static function date_format_php_to_js( $date_format ) {
		switch( $date_format ) {
	        //Predefined WP date formats
	        case 'F j, Y':
	            return( 'mm/dd/yy' );
	            break;
	        case 'j F, Y':
	        	return( 'dd-mm-yy');
	        	break;
	        case 'Y/m/d':
	            return( 'yy/mm/dd' );
	            break;
	        case 'm/d/Y':
	            return( 'mm/dd/yy' );
	            break;
	        case 'd/m/Y':
	            return( 'dd-mm-yy' );
	            break;
	     }
	}

	public static function date_format_php_to_form( $date_format ) {
		switch( $date_format ) {
	        //Predefined WP date formats
	        case 'F j, Y':
	            return( 'm/d/Y' );
	            break;
	        case 'j F, Y':
	        	return( 'd-m-Y');
	        	break;
	        case 'Y/m/d':
	            return( 'Y/m/d' );
	            break;
	        case 'm/d/Y':
	            return( 'm/d/Y' );
	            break;
	        case 'd/m/Y':
	            return( 'd-m-Y' );
	            break;
	     }
	}

}
