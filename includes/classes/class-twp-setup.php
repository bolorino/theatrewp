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

	// Plugin default options
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

	protected static $default_single_sponsor = 0;

	protected static $default_google_maps_api = '';

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

		self::$default_spectacles_number  = ( get_option( 'twp_spectacles_number' ) ? intval( get_option( 'twp_spectacles_number' ) ) : self::$default_spectacles_number );
		self::$default_performances_number  = ( get_option( 'twp_performances_number' ) ? intval( get_option( 'twp_performances_number' ) ) : self::$default_performances_number );

		self::$default_single_sponsor  = ( get_option( 'twp_single_sponsor' ) ? intval( get_option( 'twp_single_sponsor' ) ) : self::$default_single_sponsor );

		self::$default_google_maps_api  = ( get_option( 'twp_google_maps_api' ) ? get_option( 'twp_google_maps_api' ) : self::$default_google_maps_api );



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
			'twp_clean_on_uninstall'  => 0,
			'twp_single_sponsor'	  => self::$default_single_sponsor,
			'twp_google_maps_api'	  => self::$default_google_maps_api
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

		// Chek if we are using a TWP compatible theme
		if ( ! defined( 'TWP_THEME' ) ) {
			add_filter( 'the_content', array( $this, 'twp_content' ) );
		}

		// Enable a different post_per_page param for custom post
		add_filter( 'option_posts_per_page', array( 'TWP_Setup', 'twp_option_post_per_page' ) );

		// Format title for custom post archives
		// add_filter( 'wp_title', array( 'TWP_Setup', 'twp_taxonomy_title' ) );

		// Admin menu
		if ( is_admin() ) {
			add_action( 'admin_menu', array( 'TWP_Setup', 'twp_menu' ) );
			add_action( 'admin_init', array( 'TWP_Setup', 'twp_register_settings' ) );

			add_action( 'admin_init', array( $this, 'build_taxonomies' ), 0 );

			// Dashboard custom posts
			add_action( 'dashboard_glance_items' , array( 'TWP_Setup', 'twp_right_now_content_table_end' ) );

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

		self::twp_unregister_settings();

		flush_rewrite_rules();
	}

	public static function uninstall( ) {

		// Should custom post be removed?
		if ( get_option( 'twp_clean_on_uninstall' ) == '1' ) {
			self::_remove_all_data();
		}

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
			'menu_icon'		  => 'dashicons-visibility',
			'has_archive'     => self::$default_options['twp_spectacles_slug'],
			'rewrite'         => array( 'slug' => self::$default_options['twp_spectacle_slug'] ),
			'show_ui'         => true,
			'hiearchical'	  => true,
			'menu_position'   => 5,
			'taxonomies'	  => array( 'post_tag'),
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
			'menu_icon'		  => 'dashicons-tickets-alt',
			'has_archive'     => self::$default_options['twp_performances_slug'],
			'rewrite'         => array( 'slug' => self::$default_options['twp_performance_slug'] ),
			'exclude_from_search' => false,
			'capability_type' => 'post',
			'menu_position'   => 6,
			'supports'        => array( 'title', 'excerpt' )
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
			'menu_icon'		    => 'dashicons-heart',
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

			$twp_script_array = array( 'jquery-migrate', 'jquery-ui-core', 'jquery-ui-datepicker' );
			$twp_style_array = array( 'thickbox' );

			wp_register_script( 'twp-timepicker', TWP_META_BOX_URL . 'js/jquery.timepicker.min.js', $twp_script_array, false, false );
			wp_register_script( 'twp-scripts', TWP_META_BOX_URL . 'js/twp.js', $twp_script_array, false, false );

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
			wp_register_style( 'twp-datepicker-styles', TWP_META_BOX_URL . 'js/jquery.timepicker.css' );
			wp_enqueue_style( 'twp-styles' );
			wp_enqueue_style( 'twp-datepicker-styles' );
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
		$new_columns['title']      = _x( 'Performance', 'column name', 'theatrewp' );
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
		register_setting( 'twp-main', 'twp_single_sponsor', 'intval' );
		register_setting( 'twp-main', 'twp_google_maps_api' );

		register_setting( 'twp-main', 'twp_clean_on_uninstall' );
	}

	/**
	 * Unregister Settings
	 *
	 * @access public
	 * @return void
	 */
	public static function twp_unregister_settings() {
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
		unregister_setting( 'twp-main', 'twp_single_sponsor', 'intval' );
		unregister_setting( 'twp-main', 'twp_google_maps_api' );

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

		$current = ( isset( $_GET['tab'] ) ? sanitize_key( $_GET['tab'] ) : 'shows' );

		$tabs = array(
			'shows'        => __( 'Shows', 'theatrewp' ),
			'performances' => __( 'Performances', 'theatrewp' ),
			'advanced'     => __( 'Advanced', 'theatrewp' )
		);

		$links = array();

		foreach ( $tabs as $tab => $name ) {
			if ( $tab == $current ) {
				$links[] = "<a class='nav-tab nav-tab-active' href='?page=theatre-wp&tab=$tab'>$name</a>";
			} else {
				$links[] = "<a class='nav-tab' href='?page=theatre-wp&tab=$tab'>$name</a>";
			}
		}

		echo '<h2>';
		foreach ( $links as $link ) {
			echo $link;
		}
		echo '</h2> <br class="clear" /> <hr />';

		switch ( $current ) {
			case 'shows' :
				include( plugin_dir_path( __FILE__ ) . '../templates/admin/admin-options-shows.php' );
				break;
			case 'performances' :
				include( plugin_dir_path( __FILE__ ) . '../templates/admin/admin-options-performances.php' );
				break;
			case 'advanced' :
				include( plugin_dir_path( __FILE__ ) . '../templates/admin/admin-options-advanced.php' );
				break;
		}

	}

	/**
	 * Filters post per page option for custom posts
	 *
	 * @access public
	 * @return int
	 */
	public static function twp_option_post_per_page( $option_posts_per_page ) {

		if ( is_tax( 'performance' ) ) {
			$option_posts_per_page = get_option( 'twp_performances_number' );
		}

		if ( is_tax( 'spectacle' ) ) {
			$option_posts_per_page = get_option( 'twp_spectacles_number' );
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
						'options' => apply_filters( 'twp_define_audiences', TWP_Spectacle::$audience ) /* twp_define_audiences filter */
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
						'type' => ( self::$default_options['twp_single_sponsor'] == 1 ? 'sponsorselect' : 'multicheckbox' ),
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

	// Add Custom Post Type to WP-ADMIN Right Now Widget
	// Ref Link: http://wpsnipp.com/index.php/functions-php/include-custom-post-types-in-right-now-admin-dashboard-widget/
	// http://wordpress.org/support/topic/dashboard-at-a-glance-custom-post-types
	// http://halfelf.org/2012/my-custom-posttypes-live-in-mu/
	public static function twp_right_now_content_table_end() {
	    $args = array(
	        'public' => true ,
	        '_builtin' => false
	    );

	    $output = 'object';
	    $operator = 'and';

	    $post_types = get_post_types( $args , $output , $operator );

	    foreach( $post_types as $post_type ) {
	        $num_posts = wp_count_posts( $post_type->name );
	        $num = number_format_i18n( $num_posts->publish );
	        $text = _n( $post_type->labels->singular_name, $post_type->labels->name , intval( $num_posts->publish ) );

	        if ( current_user_can( 'edit_posts' ) ) {
	            $cpt_name = $post_type->name;
	        }

	        echo '<li class="'.$cpt_name.'-count"><tr><a href="edit.php?post_type='.$cpt_name.'"><td class="first b b-' . $post_type->name . '"></td>' . $num . ' <td class="t ' . $post_type->name . '">' . $text . '</td></a></tr></li>';
	    }

	    $taxonomies = get_taxonomies( $args , $output , $operator );

	    foreach( $taxonomies as $taxonomy ) {
	        $num_terms  = wp_count_terms( $taxonomy->name );
	        $num = number_format_i18n( $num_terms );
	        $text = _n( $taxonomy->labels->name, $taxonomy->labels->name , intval( $num_terms ));

	        if ( current_user_can( 'manage_categories' ) ) {
	            $cpt_tax = $taxonomy->name;
	        }

	        echo '<li class="post-count"><tr><a href="edit-tags.php?taxonomy='.$cpt_tax.'"><td class="first b b-' . $taxonomy->name . '"></td>' . $num . ' <td class="t ' . $taxonomy->name . '">' . $text . '</td></a></tr></li>';
	    }
	}

	// Content filter
	public function twp_content( $content ) {
		global $post, $theatre_wp;

		$twp_content = false;

		// Single spectacle
		// @TODO check template file exists in addition to TWP_THEME constant?
		if ( is_single() && 'spectacle' == get_post_type( get_queried_object_id() ) ) {
			$twp_content = $theatre_wp->get_single_spectacle_content( $content );
		} elseif ( is_single() && 'performance' == get_post_type( get_queried_object_id() ) ) {
			// Single performance
			$twp_content = $theatre_wp->get_single_performance_content( $content );
		}

		if ( $twp_content ) {
			return $twp_content;
		}

		return $content;
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
	        case 'Y-m-d':
	        	return( 'yy-mm-dd');
	        	break;
	     }

	     // Return default
	     return 'dd-mm-yy';
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
