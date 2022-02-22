<?php
/**
 * Setup class.
 *
 * Plugin setup class
 *
 * @package TheatreWP
 * @author  Jose Bolorino <jose.bolorino@gmail.com>
 */

namespace TheatreWP;

use TheatreWP\Widgets\ProductionSponsorsWidget;
use TheatreWP\Widgets\ShowUpcomingPerformancesWidget;
use TheatreWP\Widgets\SpectaclesWidget;
use TheatreWP\Widgets\UpcomingPerformancesWidget;

if ( realpath(__FILE__) === realpath($_SERVER['SCRIPT_FILENAME']) )
	exit('Do not access this file directly.');

class Setup {

	/**
	 * Plugin version.
	 *
	 * @since   0.2
	 *
	 * @var     string
	 */
	static string $version = '0.69';

	/**
	 * @var string
	 */
	public static string $twp_prefix = 'twp_';

	// Plugin default options
	protected static string $default_spectacle_name      = 'Spectacle';

	protected static string $default_spectacles_name     = 'Spectacles';

	protected static string $default_spectacle_slug      = 'spectacle';

	protected static string $default_spectacles_slug     = 'spectacles';

	protected static string $default_performance_name    = 'Performance';

	protected static string $default_performances_name   = 'Performances';

	protected static string $default_performance_slug    = 'performance';

	protected static string $default_performances_slug   = 'performances';

	protected static int $default_spectacles_number   = 5;

	protected static int $default_performances_number = 5;

	protected static int $default_single_sponsor      = 0;

	protected static string $default_google_maps_api  = '';

	protected static int $default_tickets_info        = 0;

	public static array $default_options = array();

	// Custom objects
	public Performance $performance;

	public Spectacle $spectacle;

	public Sponsor $sponsor;

	public Metabox $metabox;

	// Widgets

	public SpectaclesWidget $spectacles_widget;
	public ShowUpcomingPerformancesWidget $show_upcoming_performances_widget;
	public UpcomingPerformancesWidget $upcoming_performances_widget;
	public ProductionSponsorsWidget $production_sponsors_widget;

	protected array $_post_types;
	protected string $_current_post_type;

	/**
	 * List of available templates
	 * @var array
	 */
	public static array $templates = array(
		'single-spectacle'    => 'single-spectacle.php',
		'single-performance'  => 'single-performance.php',
		'archive-spectacle'   => 'archive-spectacle.php',
		'archive-performance' => 'archive-performance.php'
		);

	public static $twp_dateformat;

	public function __construct() {

		self::$default_spectacle_slug      = ( get_option( 'twp_spectacle_slug' ) ? get_option( 'twp_spectacle_slug' ) : self::$default_spectacle_slug );
		self::$default_spectacles_slug     = ( get_option( 'twp_spectacles_slug' ) ? get_option( 'twp_spectacles_slug' ) : self::$default_spectacles_slug );

		self::$default_performance_slug    = ( get_option( 'twp_performance_slug' ) ? get_option( 'twp_performance_slug' ) : self::$default_performance_slug );
		self::$default_performances_slug   = ( get_option( 'twp_performances_slug' ) ? get_option( 'twp_performances_slug' ) : self::$default_performances_slug );

		self::$default_spectacle_name      = ( get_option( 'twp_spectacle_name' ) ? get_option( 'twp_spectacle_name' ) : self::$default_spectacle_name );
		self::$default_spectacles_name     = ( get_option( 'twp_spectacles_name' ) ? get_option( 'twp_spectacles_name' ) : self::$default_spectacles_name );

		self::$default_performance_name    = ( get_option( 'twp_performance_name' ) ? get_option( 'twp_performance_name' ) : self::$default_performance_name );
		self::$default_performances_name   = ( get_option( 'twp_performances_name' ) ? get_option( 'twp_performances_name' ) : self::$default_performances_name );

		self::$default_spectacles_number   = ( get_option( 'twp_spectacles_number' ) ? intval( get_option( 'twp_spectacles_number' ) ) : self::$default_spectacles_number );
		self::$default_performances_number = ( get_option( 'twp_performances_number' ) ? intval( get_option( 'twp_performances_number' ) ) : self::$default_performances_number );

		self::$default_single_sponsor      = ( get_option( 'twp_single_sponsor' ) ? intval( get_option( 'twp_single_sponsor' ) ) : self::$default_single_sponsor );

		self::$default_google_maps_api     = ( get_option( 'twp_google_maps_api' ) ? get_option( 'twp_google_maps_api' ) : self::$default_google_maps_api );

		self::$default_tickets_info        = ( get_option( 'twp_tickets_info' ) ? get_option( 'twp_tickets_info' ) : self::$default_tickets_info );

		self::$default_options = array(
			'twp_version'			  => self::$version,
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
			'twp_google_maps_api'	  => self::$default_google_maps_api,
			'twp_tickets_info'        => self::$default_tickets_info
		);

		self::$twp_dateformat = get_option( 'date_format');

		// Actions
		add_action( 'init', array( $this, 'init' ), 0 );
		add_action( 'plugins_loaded', array( $this, 'load_plugin_textdomain' ) );

		$this->spectacle   = new Spectacle;
		$this->performance = new Performance($this->spectacle);
		$this->sponsor     = new Sponsor;

		// Widgets
		$this->spectacles_widget = new SpectaclesWidget;
		$this->upcoming_performances_widget = new UpcomingPerformancesWidget;
		$this->show_upcoming_performances_widget = new ShowUpcomingPerformancesWidget;
		$this->production_sponsors_widget = new ProductionSponsorsWidget;

		add_action( 'widgets_init', array( $this, 'init_widgets' ) );

		if ( is_admin() ) {
			$this->admin_includes();
			$this->set_post_types();

            // Ensure text domain is loaded before setting metaboxes
            $this->load_plugin_textdomain();

			$this->_current_post_type = $this->twp_get_post_data();

			$this->metabox     = new Metabox($this->_current_post_type);

			$this->admin_init();
		}
	}

	/**
	 * Set the available post types for metaboxes
	 * @return void
	 */
	public function set_post_types() {
		$this->_post_types = array('spectacle', 'performance', 'sponsor');
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

		// Filters

		// Check if we are using a TWP compatible theme
		if ( ! defined( 'TWP_THEME' ) ) {
			add_filter( 'the_content', array( $this, 'twp_content' ) );
		}

		// Enable a different post_per_page param for custom post
		add_filter( 'option_posts_per_page', array( 'TheatreWP\Setup', 'twp_option_post_per_page' ) );

		// Format title for custom post archives
		// add_filter( 'wp_title', array( 'Setup', 'twp_taxonomy_title' ) );
	}

	public function admin_init() {
		add_action( 'admin_menu', array( 'TheatreWP\Setup', 'twp_menu' ) );
		add_action( 'admin_init', array( 'TheatreWP\Setup', 'twp_register_settings' ) );

		add_action( 'admin_init', array( $this, 'build_taxonomies' ), 0 );

		// Metaboxes
		add_action( 'add_meta_boxes_' . $this->_current_post_type, array( $this, 'add_metaboxes' ) );
		add_action( 'save_post', array( $this, 'save_metabox' ) );

		// Dashboard custom posts
		add_action( 'dashboard_glance_items' , array( 'TheatreWP\Setup', 'twp_right_now_content_table_end' ) );

		// Update rewrite rules after Options update
		add_action( 'update_option_twp-main', array( 'TheatreWP\Setup', '_update_rewrite_rules') );

		// Adds link from plugins page to Theatre WP settings
		add_filter( 'plugin_action_links_' . TWP_PLUGIN_BASENAME, array( $this, 'twp_plugin_action_links' ), 10, 1 );

		add_filter( 'manage_edit-performance_columns', array( 'TheatreWP\Setup', 'twp_performances_columns' ) );
		add_action( 'manage_performance_posts_custom_column', array( $this, 'twp_manage_performances_columns' ), 10, 2);
	}

	/**
	 * Include admin required files.
	 *
	 * @access public
	 * @return void
	 */
	private function admin_includes() {
		// Enqueue admin scripts
		add_action( 'admin_enqueue_scripts', array( $this, 'twp_scripts' ), 10 );
	}

	/**
	 * Create metaboxes
	 * @return void
	 */
	public function add_metaboxes() {
		$this->metabox->add();
	}

	public function save_metabox($post_id) {
		$this->metabox->save($post_id);
	}

    /**
     * Get the post type from edit post and create new post pages in dashboard
     *
     * @return false|string
     */
	public function twp_get_post_data() {
		global $pagenow;

		if ( 'post.php' === $pagenow && isset( $_GET['post'] ) ){
			$post_id = intval( $_GET['post'] );
            return get_post_type($post_id);
		}

        if ( 'post-new.php' === $pagenow && isset( $_GET['post_type'] ) ) {
            if ( in_array( $_GET['post_type'], $this->_post_types ) ) {
                return $_GET['post_type'];
            }
        }

        return false;
	}

	/**
	* Adds link from plugins page to Theatre WP Settings page.
	* @since 0.67
	* @param array $links The current links.
	*
	* @return array
	*/
	public function twp_plugin_action_links( array $links ) {

		$twp_links = array();

		$twp_links['settings'] = '<a href="' . esc_url( admin_url( 'options-general.php?page=theatre-wp' ) ) . '">' . __( 'Settings', 'theatre-wp' ) . '</a>';
		$twp_links['home'] = '<a href="' . esc_url( 'https://www.bolorino.net/theatre-wp-wordpress-plugin-performing-arts/' ) . '" target="_blank">' . __( 'Plugin Site', 'theatre-wp' ) . '</a>';

		return array_merge( $twp_links, $links );
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
		register_widget( $this->spectacles_widget );
		register_widget( $this->upcoming_performances_widget );
		register_widget( $this->show_upcoming_performances_widget );
		register_widget( $this->production_sponsors_widget );
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
			delete_post_meta( $twp_spectacle->ID, self::$twp_prefix . 'synopsis' );
			delete_post_meta( $twp_spectacle->ID, self::$twp_prefix . 'audience' );
			delete_post_meta( $twp_spectacle->ID, self::$twp_prefix . 'duration' );
			delete_post_meta( $twp_spectacle->ID, self::$twp_prefix . 'credits' );
			delete_post_meta( $twp_spectacle->ID, self::$twp_prefix . 'sheet' );
			delete_post_meta( $twp_spectacle->ID, self::$twp_prefix . 'prod-sponsor' );
			delete_post_meta( $twp_spectacle->ID, self::$twp_prefix . 'video' );

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
			delete_post_meta( $twp_performance->ID, self::$twp_prefix . 'spectacle_id' );
			delete_post_meta( $twp_performance->ID, self::$twp_prefix . 'date_first' );
			delete_post_meta( $twp_performance->ID, self::$twp_prefix . 'date_last' );
			delete_post_meta( $twp_performance->ID, self::$twp_prefix . 'event' );
			delete_post_meta( $twp_performance->ID, self::$twp_prefix . 'place' );
			delete_post_meta( $twp_performance->ID, self::$twp_prefix . 'address' );
			delete_post_meta( $twp_performance->ID, self::$twp_prefix . 'postal_code' );
			delete_post_meta( $twp_performance->ID, self::$twp_prefix . 'town' );
			delete_post_meta( $twp_performance->ID, self::$twp_prefix . 'region' );
			delete_post_meta( $twp_performance->ID, self::$twp_prefix . 'country' );
			delete_post_meta( $twp_performance->ID, self::$twp_prefix . 'display_map' );
			delete_post_meta( $twp_performance->ID, self::$twp_prefix . 'tickets_url' );
			delete_post_meta( $twp_performance->ID, self::$twp_prefix . 'tickets_price' );
			delete_post_meta( $twp_performance->ID, self::$twp_prefix . 'free_entrance' );

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
			delete_post_meta( $twp_sponsor->ID, self::$twp_prefix . 'sponsor-url' );
			delete_post_meta( $twp_sponsor->ID, self::$twp_prefix . 'sponsor-weight' );

			// Delete post
			wp_delete_post( $twp_sponsor->ID, true );
		}

		// Delete plugin options
		foreach ( self::$default_options as $key => $value ) {
			delete_option( $key );
		}
	}

	/**
	 * Define productions custom post.
	 *
	 * @access public
	 * @return void
	 */
	public function create_spectacles() {
		$spectacles_args = array(
			'labels' => array(
				'name'          => self::$default_options['twp_spectacles_name'],
				'singular_name' => self::$default_options['twp_spectacle_name'],
				'add_new'       => __( 'Add new', 'theatre-wp' ),
				'add_new_item'  => __( 'Add new Show', 'theatre-wp' ),
				'edit_item'     => __( 'Edit Show', 'theatre-wp' ),
				'new_item'      => __( 'New Show', 'theatre-wp' ),
				'view'          => __( 'View Show', 'theatre-wp' ),
				'view_item'     => __( 'View Show', 'theatre-wp' ),
				'search_items'  => __( 'Search Shows', 'theatre-wp' )
				),
			'singular_label'  => __( 'Show', 'theatre-wp' ),
			'public'          => true,
			'menu_icon'		  => 'dashicons-visibility',
			'has_archive'     => self::$default_options['twp_spectacles_slug'],
			'rewrite'         => array( 'slug' => self::$default_options['twp_spectacle_slug'] ),
			'show_ui'         => true,
			'hierarchical'	  => true,
			'menu_position'   => 5,
			'taxonomies'	  => array( 'post_tag'),
			'supports'        => array( 'title', 'editor', 'excerpt', 'thumbnail' )
			);

		register_post_type( 'spectacle', $spectacles_args );

		$this->build_taxonomies();
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
				'add_new'       => __('Add new', 'theatre-wp'),
				'add_new_item'  => __('Add new Performance', 'theatre-wp'),
				'edit_item'     => __('Edit Performance', 'theatre-wp'),
				'new_item'      => __('New Performance', 'theatre-wp'),
				'view'          => __('View Performances', 'theatre-wp'),
				'view_item'     => __('View Performance', 'theatre-wp'),
				'search_items'  => __('Search Performance', 'theatre-wp')
				),
			'singular_label'  => __('Performance', 'theatre-wp'),
			'public'          => true,
			'menu_icon'		  => 'dashicons-tickets-alt',
			'has_archive'     => self::$default_options['twp_performances_slug'],
			'rewrite'         => array( 'slug' => self::$default_options['twp_performance_slug'] ),
			'exclude_from_search' => false,
			'capability_type' => 'post',
			'menu_position'   => 6,
			'supports'        => array( 'title', 'excerpt', 'thumbnail' )
			);

		register_post_type( 'performance', $performances_args );

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
				'name'          => __( 'Sponsors', 'theatre-wp' ),
				'singular_name' => __( 'Sponsor', 'theatre-wp' ),
				'add_new'       => __( 'Add new', 'theatre-wp' ),
				'add_new_item'  => __( 'Add new Sponsor', 'theatre-wp' ),
				'edit_item'     => __( 'Edit Sponsor', 'theatre-wp' ),
				'new_item'      => __( 'New Sponsor', 'theatre-wp' ),
				'view'          => __( 'View Sponsors', 'theatre-wp' ),
				'view_item'     => __( 'View Sponsor', 'theatre-wp' ),
				'search_items'  => __( 'Search Sponsors', 'theatre-wp' )
				),
			'singular_label'    => __( 'Sponsor', 'theatre-wp' ),
			'public'            => true,
			'menu_icon'		    => 'dashicons-heart',
			'show_in_nav_menus' => true,
			'_builtin'          => false,
			'menu_position'     => 8,
			'supports'          => array( 'title', 'editor', 'thumbnail' )
			);

		register_post_type( 'sponsor', $sponsors_args );

	}

	/**
	 * Define and register Production Format Taxonomy.
	 *
	 * @access public
	 * @return void
	 */
	public function build_taxonomies () {
		$labels = array(
			'name'			=> _x( 'Types of Production', 'taxonomy general name', 'theatre-wp' ),
			'singular_name'	=> _x( 'Type of Production', 'taxonomy singular name', 'theatre-wp' ),
			'add_new_item'	=> __( 'New format of Production', 'theatre-wp' ),
			'new_item_name' => __( 'New format name', 'theatre-wp' ),
			'menu_name'		=> __( 'Format', 'theatre-wp' )
		);

		$args = array(
			'hierarchical'      => true,
			'public'            => true,
			'label'             => __( 'Format', 'theatre-wp' ),
			'labels'            => $labels,
			'show_ui'           => true,
			'show_in_nav_menus' => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'format' ),
		);

		register_taxonomy( 'format', 'spectacle', $args );
		register_taxonomy_for_object_type( 'format', 'spectacle' );

	}

	/**
	 * Localisation.
	 *
	 * @access public
	 * @return void
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain( 'theatre-wp', false, TWP_DIR . '/languages' );
        apply_filters( 'plugin_locale', get_locale(), 'theatre-wp' );
	}

	/**
	* Adding scripts and styles
	*
	* @access public
	* @return void
	*
	*/
	public function twp_scripts( $hook ) {
		global $wp_locale;

		// only enqueue our scripts/styles on the proper pages
		if ( 'post.php' == $hook || 'post-new.php' == $hook ) {
			$twp_script_array = array( 'jquery-migrate', 'jquery-ui-core', 'jquery-ui-datepicker' );
			$twp_style_array = array( 'thickbox' );

			wp_register_script( 'twp-timepicker', TWP_META_BOX_URL . 'js/jquery.timepicker.min.js', $twp_script_array, false, false );
			wp_register_script( 'twp-scripts', TWP_META_BOX_URL . 'js/twp.js', $twp_script_array, false, false );

			wp_localize_script( 'twp-scripts', 'twp_ajax_data', array( 'ajax_nonce' => wp_create_nonce( 'ajax_nonce' ), 'post_id' => get_the_ID() ) );

			// Localize js
		    $localize_args = array(
		        'closeText'         => __( 'Done', 'theatre-wp' ),
		        'currentText'       => __( 'Today', 'theatre-wp' ),
		        'monthNames'        => $this->_strip_array_index( $wp_locale->month ),
		        'monthNamesShort'   => $this->_strip_array_index( $wp_locale->month_abbrev ),
		        'monthStatus'       => __( 'Show a different month', 'theatre-wp' ),
		        'dayNames'          => $this->_strip_array_index( $wp_locale->weekday ),
		        'dayNamesShort'     => $this->_strip_array_index( $wp_locale->weekday_abbrev ),
		        'dayNamesMin'       => $this->_strip_array_index( $wp_locale->weekday_initial ),
		        // set the date format to match the WP general date settings
		        'dateFormat'        => self::date_format_php_to_js( get_option('date_format') ),
		        // get the start of week from WP general setting
		        'firstDay'          => get_option( 'start_of_week' ),
		        // is Right to left language? default is false
		        'isRTL'             => $wp_locale->text_direction == 'rtl',
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
		$new_columns['title']      = _x( 'Performance', 'column name', 'theatre-wp' );
		$new_columns['spectacle']  = __( 'Spectacle', 'theatre-wp' );
		$new_columns['first_date'] = __( 'First Date', 'theatre-wp' );
		$new_columns['last_date']  = __( 'Last Date', 'theatre-wp' );
		$new_columns['event']      = __( 'Event', 'theatre-wp' );

		return $new_columns;
	}

	/**
	 * Performances dashboard columns data
	 *
	 * @access public
	 * @return void
	 */
	public function twp_manage_performances_columns( string $column_name, int $ID) {
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
		$hook = add_options_page( __('Theatre WP Options', 'theatre-wp'), 'Theatre WP', 'manage_options', 'theatre-wp', array( 'TheatreWP\Setup', 'twp_options' ) );

		// Add an action to check if plugin options are updated. Update rewrite rules when they are
		add_action( 'load-' . $hook, array( 'TheatreWP\Setup', 'twp_check_plugin_options_update' ) );
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
		register_setting( 'twp-main', 'twp_spectacles_number', ['intval'] );
		register_setting( 'twp-main', 'twp_performances_number', ['intval'] );
		register_setting( 'twp-main', 'twp_single_sponsor', ['intval'] );
		register_setting( 'twp-main', 'twp_google_maps_api' );
		register_setting( 'twp-main', 'twp_tickets_info', ['intval'] );

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
		unregister_setting( 'twp-main', 'twp_spectacles_number');
		unregister_setting( 'twp-main', 'twp_performances_number' );
		unregister_setting( 'twp-main', 'twp_single_sponsor' );
		unregister_setting( 'twp-main', 'twp_google_maps_api' );
		unregister_setting( 'twp-main', 'twp_tickets_info' );

		unregister_setting( 'twp-main', 'twp_clean_on_uninstall' );
	}

	public static function create_slug( array $input ) {
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
			'shows'        => __( 'Shows', 'theatre-wp' ),
			'performances' => __( 'Performances', 'theatre-wp' ),
			'advanced'     => __( 'Advanced', 'theatre-wp' )
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
				include( TWP_BASE_PATH . 'includes/templates/admin/admin-options-shows.php' );
				break;
			case 'performances' :
				include( TWP_BASE_PATH . 'includes/templates/admin/admin-options-performances.php' );
				break;
			case 'advanced' :
				include( TWP_BASE_PATH . 'includes/templates/admin/admin-options-advanced.php' );
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
	            return'mm/dd/yy';
            case  'm/d/Y':
                return 'mm/dd/yy';
	        case 'j F, Y' || 'j \d\e F \d\e Y' || 'd/m/Y':
	        	return( 'dd-mm-yy');
	        	break;
	        case 'Y/m/d':
	            return( 'yy/mm/dd' );
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
	            return'm/d/Y';
            case 'm/d/Y':
                return 'm/d/Y';
	        case ('j F, Y' || 'j \d\e F \d\e Y' || 'd/m/Y'):
	        	return( 'd-m-Y');
	        case ('Y/m/d'):
	            return( 'Y/m/d' );
	     }
	}

}
