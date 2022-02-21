<?php
/**
 * TheatreWP.
 *
 * @package   TheatreWP
 * @author    Jose Bolorino <jose.bolorino@gmail.com>
 * @license   GPL-2.0+
 * @link      https://www.bolorino.net/pages/theatre-wp-wordpress-plugin-performing-arts.html
 * @copyright 2013-2019 Jose Bolorino
 */

namespace TheatreWP;

/**
 * TheatreWP class.
 *
 * Plugin main class
 *
 * @package TheatreWP
 * @author  Jose Bolorino <jose.bolorino@gmail.com>
 */
if ( realpath(__FILE__) === realpath($_SERVER['SCRIPT_FILENAME']) )
	exit('Do not access this file directly.');


class TheatreWP {

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
	public static string $plugin_slug = 'theatre-wp';

	public static string $twp_text_domain = 'theatre-wp';

	/**
 	 * @var Spectacle
 	 */
	public Spectacle $spectacle;

 	/**
 	 * @var Performance
 	 */
 	public Performance $performance;

 	/**
 	 * @var Sponsor
 	 */
 	public Sponsor $sponsor;

 	/**
 	 * @var Setup
 	 */
 	protected Setup $setup;

 	public function __construct() {

        $this->spectacle   = new Spectacle;
        $this->performance = new Performance( $this->spectacle );
        $this->sponsor     = new Sponsor;
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
	 *
	 * @param int $ID
	 *
	 * @return array
	 */
	public function get_spectacle_custom( int $ID ) {
		return $this->spectacle->get_spectacle_custom( $ID );
	}

	/**
	 * Get spectacle title and URL from Spectacle title.
	 *
	 * @access public
	 *
	 * @param int $ID
	 *
	 * @return array
	 */
	public function get_spectacle_data( int $ID, string $thumbnail_size='thumbnail' ) {
		return $this->spectacle->get_spectacle_data( intval( $ID ), sanitize_text_field( $thumbnail_size ) );
	}

	/**
	 * Get spectacle URL from Spectacle title.
	 *
	 * @access public
	 *
	 * @param int $ID
	 *
	 * @return string
	 */
	public function get_spectacle_link( int $ID ) {
		return $this->spectacle->get_spectacle_link( intval( $ID ) );
	}

	/**
	 * Get full URL for a given production category slug
	 *
	 * @access public
	 *
	 * @param string $slug
	 *
	 * @return string
	 */
	public function get_production_cat_url( string $slug ) {
		return esc_url( home_url() ) . '/' . Spectacle::$production_category_slug . '/' . sanitize_title( $slug );
	}

	/**
	 * Get spectacle main image in available sizes
	 *
	 * @access public
	 *
	 * @param int $ID
	 * @param array $additional_sizes
	 *
	 * @return array
	 */
	public function get_spectacle_thumbnail( int $ID, $additional_sizes=array() ) {
		return $this->spectacle->get_spectacle_thumbnail( intval( $ID ), $additional_sizes );
	}

	/* Performance public methods */

	/**
	 * Checks if there are any performances
	 *
	 * @access public
	 * @return bool
	 */
	public function are_there_performances() {
		$args = array(
			'post_type' => 'performance',
			'numberposts' => 1
		);

		$check_performances = get_posts( $args );

		if ( ! $check_performances ) {
			return false;
		}

		return true;
	}

	/**
	 * Get performance custom metadata.
	 *
	 * @access public
	 *
	 * @param int $ID
	 *
	 * @return array
	 */
	public function get_performance_custom( int $ID ) {
		return $this->performance->get_performance_custom( $this->spectacle, $ID );
	}

	public function get_show_next_performances() {
		return $this->performance->get_show_next_performances();
	}

    public function get_show_next_performances_array() {
        return $this->performance->get_show_next_performances_array();
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
     * @param int $first_available_year
     * @param int $last_available_year
	 * @return array
	 */
	public function get_calendar_data( $first_available_year = null, $last_available_year = null ) {

        if ( empty( $first_available_year ) ) {
            $first_available_year = $this->get_first_available_year();
        }

        if ( empty( $last_available_year ) ) {
            $last_available_year = $this->get_last_available_year();
        }

        $month_names = $this->get_month_names();

		$calendar_data = array(
			'month_names'          => $month_names,
			'current_year'         => date('Y'),
			'first_available_year' => intval( $first_available_year ),
			'last_available_year'  => intval( $last_available_year )
		);

		return $calendar_data;
	}

	public function get_calendar( $calendar_filter_params ) {
		return $this->performance->get_filtered_calendar( $calendar_filter_params );
	}

    public function get_busy_dates( $calendar_filter_params ) {
        return $this->performance->get_busy_dates( $calendar_filter_params );
    }

    /* Sponsors */
    public function get_sponsors() {
        global $post;
        return $this->sponsor->get_sponsors();
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

        if ( $production_format ) {
            foreach ( $production_format as $format ) {
                $formats[] = array(
                    'name' => $format->name,
                    'slug' => $format->slug
                );
            }
        }

		if ( isset( $formats ) ) {
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
			$twp_content .= __( $production_custom['audience'], 'theatre-wp' );
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
			. __('Credits', 'theatre-wp')
			. '</h2>'
			. nl2br( $production_custom['credits'] )
			. '</div>';
		}

		if ( $production_custom['sheet'] ) {
			$twp_content .= '<div class="sheet">'
			. '<h2 class="twp-subtitle">'
			. __('Sheet', 'theatre-wp')
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
        	. __( 'When', 'theatre-wp' )
        	. '</h3>'
        	. '<p class="single-performance-dates">';

            if ( isset( $performance_last_date ) ) {
                $twp_content .= _x( 'From', '(date) performing from day', 'theatre-wp' );
            }

            $twp_content .= '<span class="performance-date">'
            . $performance_first_date
            . '</span> '
            . '(<span class="performance-time">'
            . $performance_first_time
            . '</span>)';

            if ( isset( $performance_last_date ) ) {
            	$twp_content .= _x( 'To', '(date) performing to day', 'theatre-wp' ) . ' '
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
        	. __( 'Event', 'theatre-wp' )
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
