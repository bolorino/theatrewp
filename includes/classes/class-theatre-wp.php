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
	static $version = '0.51';

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

	public static $twp_text_domain = 'theatrewp';

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

 	public function __construct( $path ) {
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

		// Widgets
		include( 'class-twp-spectacles-widget.php' );
		include( 'class-twp-upcoming-performances-widget.php' );
		include( 'class-twp-show-upcoming-performances-widget.php' );
		include( 'class-twp-production-sponsors-widget.php');

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
	 * @param int $ID
	 * @return array
	 */
	public function get_spectacle_data( $ID ) {
		return $this->spectacle->get_spectacle_data( intval( $ID ) );
	}

	/**
	 * Get spectacle URL from Spectacle title.
	 *
	 * @access public
	 * @param int $ID
	 * @return string
	 */
	public function get_spectacle_link( $ID ) {
		return $this->spectacle->get_spectacle_link( intval( $ID ) );
	}

	/**
	 * Get full URL for a given production category slug
	 *
	 * @access public
	 * @param string $slug
	 * @return string
	 */
	public function get_production_cat_url( $slug ) {
		return get_bloginfo('url') . '/' . TWP_Spectacle::$production_category_slug . '/' . sanitize_title( $slug );
	}

	/* Performance public methods */

	/**
	 * Get performance custom metadata.
	 *
	 * @access public
	 * @param int $ID
	 * @return array
	 */
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

	/**
	 * Add TWP custom data to single spectacle content
	 *
	 * @access public
	 * @param string $content
	 * @return string
	 */
	public function get_single_spectacle_content( $content ) {
		global $post;

		$twp_content = '<div id="twp-pre_content">';
		$pre_content = false;

		// Production custom metadata
		$production_custom = $this->get_spectacle_custom( $post->ID );

		// Get the Production formats
		$production_format = get_the_terms( $post->ID, 'format' );

		foreach ( $production_format as $format ) {
			$formats[] = array(
				'name' => $format->name,
				'slug' => $format->slug
			);
		}

		if ( ! empty( $formats ) ) {
			$twp_content .= '<div class="twp-formats">';

			foreach ( $formats as $format ) {
				$twp_content .= '<a href="'
				. $this->get_production_cat_url( $format['slug'] )
				. '">'
				. $format['name']
				. '</a> ';
			}
			$twp_content .= '</div>';

			$pre_content = true;
		}

		if ( $production_custom['audience'] ) {
			$twp_content .= '<div class="twp-audience">';
			$twp_content .= __( $production_custom['audience'], 'theatrewp' );
			$twp_content .= '</div>';
			$pre_content = true;
		}

		if ( $production_custom['synopsis'] ) {
			$twp_content .= '<p>';
			$twp_content .= $production_custom['synopsis'];
			$twp_content .= '</p>';
			$pre_content = true;
		}

		$twp_content .= '</div>';

		if ( $pre_content ) {
			$twp_content .= $content;
		}

		$twp_content .= '<div id="twp-content">';

		if ( $production_custom['video'] ) {
			$twp_content .= '<div id="twp-video" class="video">'
			. $production_custom['video']
			. '</div>';
		}

		if ( $production_custom['credits'] ) {
			$twp_content .= '<div id="twp-credits">'
			. '<h2 class="twp-subtitle">'
			. __('Credits', 'theatrewp')
			. '</h2>'
			. $production_custom['credits']
			. '</div>';
		}

		if ( $production_custom['sheet'] ) {
			$twp_content .= '<div class="sheet">'
			. '<h2 class="twp-subtitle">'
			. __('Sheet', 'theatrewp')
			. '</h2>'
			. nl2br( $production_custom['sheet'] )
			. '</div>';
		}

		$twp_content .= '</div>';

		if ( ! $pre_content ) {
			$twp_content .= $content;
		}

		return $twp_content;
	}

	/**
	 * Add TWP custom data to single performance content
	 *
	 * @access public
	 * @param string $content
	 * @return string
	 */
	public function get_single_performance_content( $content ) {
		global $post;

		$twp_content = '<div id="twp-pre_content">';

		// Get performance custom metadata
		$performance_custom = $this->get_performance_custom( get_the_ID() );

        if ( $performance_custom['date_first'] ) {
            $performance_first_date = date_i18n( get_option( 'date_format' ), $performance_custom['date_first'] );
            $performance_first_time = strftime( '%H:%M', $performance_custom['date_first'] );
        }

        if ( $performance_custom['date_last'] ) {
            $performance_last_date = date_i18n( get_option( 'date_format' ), $performance_custom['date_last'] );
            $performance_last_time = strftime( '%H:%M', $performance_custom['date_last'] );
        }

        // Get Spectacle data
        $spectacle_data = $this->get_spectacle_data( $performance_custom['spectacle_id'] );

        if ( isset( $performance_first_date ) ) {
        	$twp_content .= '<h3>'
        	. __( 'When', 'theatrewp' )
        	. '</h3>'
        	. '<p class="single-performance-dates">';

            if ( isset( $performance_last_date ) ) {
                $twp_content .= _x( 'From', '(date) performing from day', 'theatrewp' );
            }

            $twp_content .= '<span class="performance-date">'
            . $performance_first_date
            . '</span> '
            . '(<span class="performance-time">'
            . $performance_first_time
            . '</span>)';

            if ( isset( $performance_last_date ) ) {
            	$twp_content .= _x( 'To', '(date) performing to day', 'theatrewp' ) . ' '
            	. '<span class="performance-date">'
            	. $performance_last_date
            	. '</span>'
                . '(<span class="performance-time">'
                . $performance_last_time
                . '</span>)<br>';
            }

            $twp_content .= '</p>';
        }

        $twp_content .= '<h3>'
        . '<a href="' . $performance_custom['spectacle_url'] . '">'
        . $performance_custom['spectacle_title']
        . '</a>'
        . '</h3>';

        if ( $performance_custom['event'] ) {
        	$twp_content .= '<h3>'
        	. __( 'Event', 'theatrewp' )
        	. '</h3>'
        	. '<div class="event">'
        	. '<p>'
            . '<em>'
            . $performance_custom['event']
            . '</em>'
            . '</p>'
            . '</div>';
        }

        $twp_content .= '<p class="location">';

        if ( $performance_custom['place'] ) {
            $twp_content .= $performance_custom['place'] . '<br>';
        }

        if ( $performance_custom['address'] ) {
            $twp_content .= $performance_custom['address'] . '<br>';
        }

        if ( $performance_custom['town'] ) {
            $twp_content .= $performance_custom['town'] . ' ';
        }

        if ( $performance_custom['country'] ) {
            $twp_content .= '(' . $performance_custom['country'] . ') ';
        }

        $twp_content .= '</p>';

        if ( $performance_custom['display_map'] ) {
        	$twp_content .= '<div id="performance-map">';
            $map = $this->display_performance_map( $performance_custom );
            $twp_content .= $map
            . '</div>';
        }

        return $twp_content . $content;

	}

}

