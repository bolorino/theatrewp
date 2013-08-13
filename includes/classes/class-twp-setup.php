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
	protected $performance;

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

	public function __construct($plugin_dir) {
		self::$plugin_dir = $plugin_dir;

		// Actions
		add_action( 'init', array( $this, 'init' ), 0 );

		$this->performance = new TWP_Performance;
	}

	/**
	 * Init Theatre WordPress plugin.
	 *
	 * @access public
	 * @return void
	 */
	public function init() {
		// Set up localisation @TODO
		// $this->load_plugin_textdomain();

		// Setup custom posts
		add_action( 'init', array( $this, 'create_spectacles' ) );
		add_action( 'init', array( $this, 'create_performances' ) );

		// Filters
		// Default custom posts templates
		add_filter( 'single_template', array( $this, 'get_twp_single_template' ) );
		add_filter( 'archive_template', array( $this, 'get_twp_archive_template' ) );

		// Widgets
		wp_register_sidebar_widget( 'twp-show-next-performances', __('Spectacle Next Performances'), array( $this, 'widget_show_next_performances' ) );
		wp_register_sidebar_widget( 'twp-next-performances', __('Global Next Performances'), array( $this, 'widget_next_performances' ) );
	}

	/**
	 * Fired when the plugin is activated.
	 *
	 * @since    0.2
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses "Network Activate" action, false if WPMU is disabled or plugin is activated on an individual blog.
	 */
	public static function activate( $network_wide ) {
		// @TODO: Define activation functionality here
	}

	/**
	 * Fired when the plugin is deactivated.
	 *
	 * @since    0.2
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses "Network Deactivate" action, false if WPMU is disabled or plugin is deactivated on an individual blog.
	 */
	public static function deactivate( $network_wide ) {
		// @TODO: Define deactivation functionality here
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
				'name'          => __('Shows', 'theatrewp'),
				'singular_name' => __('Show', 'theatrewp'),
				'add_new'       => __('Add new', 'theatrewp'),
				'add_new_item'  => __('Add new Show', 'theatrewp'),
				'edit_item'     => __('Edit Show', 'theatrewp'),
				'new_item'      => __('New Show', 'theatrewp'),
				'view'          => __('View Show', 'theatrewp'),
				'view_item'     => __('View Show', 'theatrewp'),
				'search_items'  => __('Search Shows', 'theatrewp')
				),
			'singular_label'  => __('Show', 'theatrewp'),
			'public'          => true,
			'has_archive'     => true,
			'capability_type' => 'post',
			'show_ui'         => true,
			'rewrite'         => true,
			'menu_position'   => 5,
			'supports'        => array( 'title', 'editor', 'thumbnail' )
			);

		register_post_type( 'spectacle', $spectacles_args );
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
				'name'          => __('Performances', 'theatrewp'),
				'singular_name' => __('Performance', 'theatrewp'),
				'add_new'       => __('Add new', 'theatrewp'),
				'add_new_item'  => __('Add new performance', 'theatrewp'),
				'edit_item'     => __('Edit performance', 'theatrewp'),
				'new_item'      => __('New performance', 'theatrewp'),
				'view'          => __('View performances', 'theatrewp'),
				'view_item'     => __('View performance', 'theatrewp'),
				'search_items'  => __('Search performance', 'theatrewp')
				),
			'singular_label'  => __('Performance', 'theatrewp'),
			'public'          => true,
			'has_archive'     => true,
			'capability_type' => 'post',
			'rewrite'         => true,
			'menu_position'   => 6,
			'supports'        => array( 'title' )
			);

		register_post_type( 'performance', $performances_args );

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

		load_plugin_textdomain('theatrewp', false, '/' . self::$plugin_dir . '/languages/' );
	}

	/**
	 * Get the path to the custom posts (spectacle/performance) single templates
	 *
	 * @TODO Avoid ../ in path
	 * @access public
	 * @return string
	 */
	public function get_twp_single_template( $template ) {
		//$this->_render_ToDebugBar( 'main', 'msg', 'get_twp_template', false, __FILE__, __LINE__ );
		//$this->_render_ToDebugBar( 'main','pr','template[single-spectacle]', $this->templates['single-spectacle'], __FILE__, __LINE__ );

		if ( 'spectacle' == get_post_type( get_queried_object_id() ) && ! $this->_check_theme_templates(self::$templates['single-spectacle']) ) {
			$template = plugin_dir_path(__FILE__) . '../templates/single-spectacle.php';
		}

		if ( 'performance' == get_post_type( get_queried_object_id() ) && ! $this->_check_theme_templates(self::$templates['single-performance']) ) {
			$template = plugin_dir_path(__FILE__) . '../templates/single-performance.php';
		}

		// $this->_render_ToDebugBar( 'main','pr','template', $template, __FILE__, __LINE__ );

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

	private function _check_theme_templates( $template)  {
		if ( ! locate_template( $template, false ) ) {
			return false;
		}

		return true;
	}

	/**
	* Adding scripts and styles
	*/
	public static function twp_scripts( $hook ) {
		global $wp_version;

		// only enqueue our scripts/styles on the proper pages
		if ( 'post.php' == $hook || 'post-new.php' == $hook ) {
			$twp_script_array = array( 'jquery-ui-datepicker' );
			$twp_style_array = array( 'thickbox' );

			wp_register_script( 'twp-timepicker', TWP_META_BOX_URL . 'js/jquery.timePicker.min.js' );
			wp_register_script( 'twp-scripts', TWP_META_BOX_URL . 'js/twp.js', $twp_script_array, '0.9.1' );
			wp_localize_script( 'twp-scripts', 'twp_ajax_data', array( 'ajax_nonce' => wp_create_nonce( 'ajax_nonce' ), 'post_id' => get_the_ID() ) );
			wp_enqueue_script( 'twp-timepicker' );
			wp_enqueue_script( 'twp-scripts' );

			wp_register_style( 'twp-styles', TWP_META_BOX_URL . 'style.css', $twp_style_array );
			wp_enqueue_style( 'twp-styles' );

			// $this->_render_ToDebugBar( 'main','pr','TWP_META_BOX_URL', TWP_META_BOX_URL, __FILE__, __LINE__ );

		}

		return true;
	}

	public function widget_next_performances( $args ) {
		if ( ! $performances = $this->performance->get_next_performances() ) {
			return false;
		}

		extract( $args );

		echo $before_widget;
		echo $before_title . __('Upcoming Performances', 'theatrewp') . $after_title;

		echo $performances;

		echo $after_widget;
	}

	public function widget_show_next_performances( $args ) {
		global $post;
		$current_category = get_post_type();

		if ( $current_category != 'spectacle' OR ! is_single() ) {
			return false;
		}

		$title = get_the_title( $post->ID );

		if ( ! $performances = $this->performance->get_show_next_performances() ) {
			return false;
		}

		extract( $args );

		echo $before_widget;
		echo $before_title . sprintf(__('“%s” Next Performances', 'theatrewp'), $title) . $after_title;

		echo $performances;

		echo $after_widget;
	}
}
