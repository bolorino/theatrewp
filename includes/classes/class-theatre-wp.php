<?php
/**
 * TheatreWP.
 *
 * @package   TheatreWP
 * @author    Jose Bolorino <jose.bolorino@gmail.com>
 * @license   GPL-2.0+
 * @link      http://www.bolorino.net/TheatreWP
 * @copyright 2013 Jose Bolorino
 */

/**
 * Theatre_WP class.
 *
 * Plugin main class
 *
 * @package TheatreWP
 * @author  Jose Bolorino <jose.bolorino@gmail.com>
 */
if ( realpath(__FILE__) === realpath($_SERVER['SCRIPT_FILENAME']) )
	exit('Do not access this file directly.');


class Theatre_WP {
	/**
	 * Plugin version.
	 *
	 * @since   0.2
	 *
	 * @var     string
	 */
	protected $version = '0.3';

	/**
	 * Unique identifier for your plugin.
	 *
	 * Use this value (not the variable name) as the text domain when internationalizing strings of text. It should
	 * match the Text Domain file header in the main plugin file.
	 *
	 * @since    0.2
	 *
	 * @var      string
	 */
	protected $plugin_slug = 'theatrewp';

	protected static $plugin_dir;

	/**
 	 * @var string
 	 */
	public static $twp_prefix = 'twp_';

	/**
 	 * @var TWP_Spectacle
 	 */
	public $spectacle;

 	/**
 	 * @var TWP_Performance
 	 */
 	public $performance;

 	/**
 	 * @var TWP_Setup
 	 */
 	protected $setup;

 	public function __construct($path) {

		//$this->_render_ToDebugBar('main', 'msg', 'Theatre_WP Constructor', false, __FILE__, __LINE__);

		self::$plugin_dir = $path;

 		// Include required files
		$this->includes();

		$this->spectacle = new TWP_Spectacle;
		$this->performance = new TWP_Performance( $this->spectacle );

 		$this->setup = new TWP_Setup( self::$plugin_dir, $this->spectacle, $this->performance );

 	}

	/**
	 * Include required core files.
	 *
	 * @access public
	 * @return void
	 */
	private function includes() {
		if ( is_admin() ) $this->admin_includes();

		include( 'class-twp-setup.php' );
		include( 'class-twp-spectacles.php' );
		include( 'class-twp-performances.php' );
		include( 'class-twp-metaboxes.php' );
	}

	/**
	 * Include admin required files.
	 *
	 * @access public
	 * @return void
	 */
	private function admin_includes() {
		// Enqueue admin scripts
		add_action( 'admin_enqueue_scripts', array( 'TWP_Setup', 'twp_scripts' ), 10 );
	}

	/* Spectacle public methods */

	public function list_spectacles() {
		return $this->spectacle->get_spectacles_titles();
	}

	public function get_spectacle_custom( $ID ) {
		return $this->spectacle->get_spectacle_custom( $ID );
	}

	public function get_spectacle_data( $post_name ) {
		return $this->spectacle->get_spectacle_data( $post_name );
	}

	public function get_spectacle_link( $spectacle_title ) {
		return $this->spectacle->get_spectacle_link( $spectacle_title );
	}

	/* Performance public methods */
	public function get_performance_custom( $ID ) {
		return $this->performance->get_performance_custom( $this->spectacle, $ID );
	}

	public function get_show_next_performances() {
		return $this->performance->get_show_next_performances();
	}

	public function get_next_performances() {
		return $this->performance->get_next_performances();
	}

}

