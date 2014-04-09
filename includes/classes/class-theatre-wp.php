<?php
/**
 * TheatreWP.
 *
 * @package   TheatreWP
 * @author    Jose Bolorino <jose.bolorino@gmail.com>
 * @license   GPL-2.0+
 * @link      http://www.bolorino.net/TheatreWP
 * @copyright 2013-2014 Jose Bolorino
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
	protected $version = '0.40';

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
 	 * @var TWP_Sponsor
 	 */
 	public $sponsor;

 	/**
 	 * @var TWP_Setup
 	 */
 	protected $setup;

 	public function __construct($path) {
		self::$plugin_dir = $path;

 		// Include required files
		$this->includes();

		$this->spectacle = new TWP_Spectacle;
		$this->performance = new TWP_Performance( $this->spectacle );
		$this->sponsor = new TWP_Sponsor;

		$this->setup = new TWP_Setup( self::$plugin_dir, $this->spectacle, $this->performance, $this->sponsor );

		if ( is_admin() ) $this->admin_includes();
 	}

	/**
	 * Include required core files.
	 *
	 * @access public
	 * @return void
	 */
	private function includes() {
		include( 'class-twp-setup.php' );
		include( 'class-twp-spectacles.php' );
		include( 'class-twp-performances.php' );
		include( 'class-twp-sponsors.php' );
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
		add_action( 'admin_enqueue_scripts', array( $this->setup, 'twp_scripts' ), 10 );
	}

	/* Spectacle public methods */

	/**
	 * Returns an HTML list of available spectacles with links
	 *
	 * @access public
	 * @param int $limit
	 * @return string
	 */
	public function list_spectacles( $limit = 0, $sort_by, $sort ) {
		return $this->spectacle->get_spectacles( $limit, $sort_by, $sort );
	}

	/**
	 * Returns an array containig spectacles titles
	 *
	 * @access public
	 * @return array
	 */
	public function get_spectacles_titles() {
		return $this->spectacle->get_spectacles_titles();
	}

	/**
	 * Get spectacle custom metadata.
	 *
	 * @access public
	 * @param int $ID
	 * @return array
	 */
	public function get_spectacle_custom( $ID ) {
		return $this->spectacle->get_spectacle_custom( $ID );
	}

	/**
	 * Get spectacle title and URL from Spectacle title.
	 *
	 * @access public
	 * @param string $spectacle_title
	 * @return array
	 */
	public function get_spectacle_data( $spectacle_title ) {
		return $this->spectacle->get_spectacle_data( $spectacle_title );
	}

	/**
	 * Get spectacle URL from Spectacle title.
	 *
	 * @access public
	 * @param string $spectacle_title
	 * @return string
	 */
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

	public function display_performance_map( $custom_meta, $width = '', $height = '' ) {
		return $this->performance->get_event_google_map_embed( $custom_meta, $width, $height );
	}

	public function get_month_names( ) {
		return $this->performance->month_names;
	}

	public function get_total_performances() {
		return $this->performance->total_performances;
	}

	public function get_total_filtered_performances( $performances_filter_params ) {
		return $this->performance->get_total_filtered_performances( $performances_filter_params );
	}

	public function get_first_available_year() {
		return $this->performance->first_available_year;
	}

	public function get_last_available_year() {
		return $this->performance->last_available_year;
	}

	/**
	 * Get neccesary data to display calendar filter
	 *
	 * @access public
	 * @return array
	 */
	public function get_calendar_data() {
		$calendar_data = array(
			'month_names'          => $this->get_month_names(),
			'current_year'         => date('Y'),
			'first_available_year' => $this->get_first_available_year(),
			'last_available_year'  => $this->get_last_available_year()
		);

		return $calendar_data;
	}

	public function get_calendar( $calendar_filter_params ) {
		return $this->performance->get_filtered_calendar( $calendar_filter_params );
	}
}

