<?php
if (realpath(__FILE__) === realpath($_SERVER['SCRIPT_FILENAME']))
	exit('Do not access this file directly.');


class TWP_Performance {

	protected $spectacle;

	public $month_names;

	public $month;

	public $year;

	public $total_performances;

	public $first_available_year;

	public $last_available_year;

	public function __construct( $spectacle ) {
		$this->spectacle = $spectacle;

		$this->month_names = $this->_set_month_names();
		$this->month = date('m');
		$this->year = date('Y');

		$this->total_performances = $this->_set_total_performances();

		$this->first_available_year = $this->_set_first_available_year();

		$this->last_available_year = $this->_set_last_available_year();
	}

	/**
	* Get performance custom fields
	*
	* @access public
	* @param int $ID
	* @return array
	*/
	public function get_performance_custom( TWP_Spectacle $spectacle, $ID ) {
		$custom = get_post_custom( intval( $ID ) );

		if ( ! $custom ) {
			return false;
		}

		$performance_custom = array();

		$performance_custom['performance'] = isset( $custom[Theatre_WP::$twp_prefix . 'performance'][0] ) ? $custom[Theatre_WP::$twp_prefix . 'performance'][0] : false;
		$performance_custom['event']       = isset( $custom[Theatre_WP::$twp_prefix . 'event'][0] ) ? $custom[Theatre_WP::$twp_prefix . 'event'][0] : false;
		$performance_custom['place']       = isset( $custom[Theatre_WP::$twp_prefix . 'place'][0] ) ? $custom[Theatre_WP::$twp_prefix . 'place'][0] : false;
		$performance_custom['address']     = isset( $custom[Theatre_WP::$twp_prefix . 'address'][0] ) ? $custom[Theatre_WP::$twp_prefix . 'address'][0] : false;
		$performance_custom['town']        = isset( $custom[Theatre_WP::$twp_prefix . 'town'][0] ) ? $custom[Theatre_WP::$twp_prefix . 'town'][0] : false;
		$performance_custom['country']     = isset( $custom[Theatre_WP::$twp_prefix . 'country'][0] ) ? $custom[Theatre_WP::$twp_prefix . 'country'][0] : false;
		$performance_custom['date_first']  = isset( $custom[Theatre_WP::$twp_prefix . 'date_first'][0] ) ? $custom[Theatre_WP::$twp_prefix . 'date_first'][0] : false;
		$performance_custom['date_last']   = isset( $custom[Theatre_WP::$twp_prefix . 'date_last'][0] ) ? $custom[Theatre_WP::$twp_prefix . 'date_last'][0] : false;
		$performance_custom['display_map'] = isset( $custom[Theatre_WP::$twp_prefix . 'display_map'][0] ) ? $custom[Theatre_WP::$twp_prefix . 'display_map'][0] : false;


		$spectacle_name                        = sanitize_title( $performance_custom['performance'] );
		$spectacle_data                        = $this->spectacle->get_spectacle_data( $spectacle_name );
		$performance_custom['spectacle_title'] = $spectacle_data['title'];
		$performance_custom['spectacle_url']   = $spectacle_data['link'];

		return $performance_custom;
	}

	/**
	 * Get an HTML list of upcoming performances.
	 *
	 * @access public
	 * @return string
	 */
	public function get_next_performances() {
		$now = time();

		$args = array(
			'post_type' => 'performance',
			'meta_key' => Theatre_WP::$twp_prefix . 'date_first',
			'orderby' => 'meta_value',
			'meta_compare' => '>=',
			'meta_value' => $now,
			'order' => 'ASC',
			'numberposts' => 5 // @TODO limit by config
		);

		$next = get_posts( $args );

		if ( ! $next ) {
			return false;
		}

		$output = '<ul class="next-performances">';

	    foreach ( $next as $post ) : setup_postdata( $post );
	    	$performance_custom = $this->get_performance_custom( $this->spectacle, $post->ID );

	        $spectacle_link = $this->spectacle->get_spectacle_link( $performance_custom['performance'] );

	        $output .= '<li>';

	        $output .= '<strong><a href="' . get_permalink( $post->ID ) . '">';
	        $output .= get_the_title( $post->ID ) .'</a></strong> <br />';

	        $output .= '<em><a href="' . $spectacle_link . '">';
	        $output .= $performance_custom['performance'] .'</a></em> <br />';

	        if ( $performance_custom['event'] ) {
	        	$output .= $performance_custom['event'] . '<br />';
	        }

	        if ( $performance_custom['date_last'] ) {
	        	$output .= '<span class="twpdate">';
	        	$output .= _x( 'From', '(date) performing from day', 'theatrewp' );
	        	$output .= ' ' . date_i18n( get_option( 'date_format' ), $performance_custom['date_first'] ) . ' '
	        		. _x( 'To', '(date) performing to day', 'theatrewp' ) . ' '
	        		. date_i18n( get_option( 'date_format' ), $performance_custom['date_last'] )
					. '</span>';
	        } else {
	        	$output .= date_i18n( get_option( 'date_format' ), $performance_custom['date_first'] );
	        }

	        $output .= '<br>';

	        $output .= $performance_custom['town'];

	        $output .= '</li>';
	    endforeach;

	    $output .= '</ul>';

	    return $output;
	}

	/**
	 * Get an HTML list of current show upcoming performances .
	 *
	 * @access public
	 * @return string
	 */
	public function get_show_next_performances () {
		global $wpdb, $post;

		$current_category = get_post_type();

		if ( 'spectacle' != $current_category  ) {
			return false;
		}

		$now = time();
		$title = get_the_title( $post->ID );

		$args = array(
			'post_type' => 'performance',
			'meta_key' => Theatre_WP::$twp_prefix . 'date_first',
			'orderby' => 'meta_value',
			'meta_compare' => '>=',
			'meta_value' => $now,
			'order' => 'ASC',
			'numberposts' => 5 // @TODO limit by config
		);

		$next = get_posts( $args );

		if ( ! $next ) {
			return false;
		}

		// @TODO It would be possible to get the show related performances directly?
	    $this_show = false; // There are future performances, but need to check for this show

	    $output = '<ul class="next-performances">';

	    foreach ( $next as $performance ) : setup_postdata( $performance );
	    	$performance_custom = $this->get_performance_custom( $this->spectacle, $performance->ID );

	        $spectacle_title = sanitize_title( $performance_custom['performance'] );
	        $spectacle_link = $this->spectacle->get_spectacle_link( $spectacle_title );

	        if ( $title == $performance_custom['performance'] ) {
	        	$this_show = true;

	        	$output .= '<li>';

	        	$output .= '<a href="' . get_permalink( $performance->ID ) . '">';
	        	$output .= get_the_title( $performance->ID ) .'</a> <br />';

	        	if ( $performance_custom['event'] ) {
	        		$output .= $performance_custom['event'] . '<br />';
	        	}

	        	$output .= $performance_custom['town'];

	        	$output .= '<br />';

	        	if ( $performance_custom['date_last'] ) {
		        	$output .= '<span class="twpdate">';
		        	$output .= _x( 'From', '(date) performing from day', 'theatrewp' );
		        	$output .= ' ' . date_i18n( get_option( 'date_format' ), $performance_custom['date_first'] ) . ' '
		        		. _x( 'To', '(date) performing to day', 'theatrewp' ) . ' '
		        		. date_i18n( get_option( 'date_format' ), $performance_custom['date_last'] )
						. '</span>';
		        } else {
		        	$output .= date_i18n( get_option( 'date_format' ), $performance_custom['date_first'] );
		        }

	        	$output .= '</li>';
	        }
	    endforeach;

	    $output .= '</ul>';

	    if ( ! $this_show ) {
	        $output = false;
	    }

	    return $output;
	}

	/**
	* Get a date filtered list of performances.
	*
	* @access public
	* @param array $calendar_filter_params
	* @return object
	*/
	public function get_filtered_calendar( $calendar_filter_params ) {
		global $wpdb, $post;
		// $calendar_filter_params:
		// month, year, page

		// Default values
		$page = 1;
		$offset = 0;

		if ( ! empty( $calendar_filter_params ) ) {
			$this->month = intval( $calendar_filter_params['month'] );
			$this->year = intval( $calendar_filter_params['year'] );
			$page = intval( $calendar_filter_params['page'] );
		}

		$performances_per_page = get_option( 'twp_performances_number' );

		if ( $page > 1 ) {
			$offset = ($page-1)*$performances_per_page;
		}

		$sql_calendar = "SELECT ID, post_title, post_name, meta_key, meta_value, FROM_UNIXTIME(meta_value, '%M') AS month, FROM_UNIXTIME(meta_value, '%Y') AS year, post_author, post_date, post_content
			FROM $wpdb->posts, $wpdb->postmeta
			WHERE " . $wpdb->posts . '.ID = ' . $wpdb->postmeta . ".post_id
			AND post_type = 'performance'
			AND meta_key = '" . Theatre_WP::$twp_prefix . "date_first' ";

		if ( 0 == $this->month && 0 == $this->year ) {
			// Upcoming performances. Not month nor year passed
			// @TODO $topdate to config
			$topdate = time()-72800;
			$sql_calendar .= "AND meta_value >= $topdate ";
		} elseif ( $this->year == 0 ) {
			$this->year = date('Y');
		}

		if ( 0 != $this->year ) {
			$sql_calendar .= "AND FROM_UNIXTIME(meta_value, '%Y') = $this->year ";
		}

		if ( 0 != $this->month ) {
			$sql_calendar .= "AND FROM_UNIXTIME(meta_value, '%m') = $this->month ";
		}

		$sql_calendar .= 'ORDER BY meta_value ';
		$sql_calendar .= " LIMIT $performances_per_page ";

		if ( $page > 1 ) {
			$sql_calendar .= " OFFSET $offset";
		}

		$filtered_calendar = $wpdb->get_results( $sql_calendar, OBJECT );

		if ( empty( $filtered_calendar ) ) {
			return false;
		}

		return $filtered_calendar;
	}

	public function get_total_filtered_performances( $performances_filter_params ) {
		global $wpdb;

		if ( ! empty( $performances_filter_params ) ) {
			$this->month = intval( $performances_filter_params['month'] );
			$this->year = intval( $performances_filter_params['year'] );
		}

		$sql_calendar = "SELECT COUNT(ID) AS total
			FROM $wpdb->posts, $wpdb->postmeta
			WHERE " . $wpdb->posts . '.ID = ' . $wpdb->postmeta . ".post_id
			AND post_type = 'performance'
			AND meta_key = '" . Theatre_WP::$twp_prefix . "date_first' ";

		if ( 0 == $this->month && 0 == $this->year ) {
			// Upcoming performances. Not month nor year passed
			// @TODO $topdate to config
			$topdate = time()-72800;
			$sql_calendar .= "AND meta_value >= $topdate ";
		} elseif ( $this->year == 0 ) {
			$this->year = date('Y');
		}

		if ( 0 != $this->year ) {
			$sql_calendar .= "AND FROM_UNIXTIME(meta_value, '%Y') = $this->year ";
		}

		if ( 0 != $this->month ) {
			$sql_calendar .= "AND FROM_UNIXTIME(meta_value, '%m') = $this->month ";
		}

		$count_filtered_performances = $wpdb->get_row( $sql_calendar );

		if ( empty( $count_filtered_performances ) ) {
			return false;
		}

		return $count_filtered_performances->total;

	}

	/**
 	* Returns an embedded google maps for the given event
  	*
  	* @param string $ID
  	* @param int $width
  	* @param int $height
  	* @return string - an iframe pulling http://maps.google.com/ for this event
  	*/
	function get_event_google_map_embed( $custom_meta, $width ='', $height = '' ) {

		$location_meta_fields = array( 'address', 'town', 'region', 'postal_code', 'country' );
		$to_url_encode = '';

		foreach ( $custom_meta as $key => $value ) {
			if ( in_array( $key, $location_meta_fields ) && ! empty( $value ) ) {
				$to_url_encode .= $value . ' ';
			}
		}

		if ( ! $height ) $height = '350';
		if ( ! $width ) $width = '100%';

		if( $to_url_encode ) $google_address = urlencode( trim( $to_url_encode ) );

		if ( $google_address ) {
			$google_iframe = '<div id="googlemaps"><iframe width="' . $width . '" height="' . $height . '" src="http://www.google.com/maps?f=q&amp;source=s_q&amp;hl=es&amp;geocode=&amp;q='.$google_address.'?>&amp;output=embed"></iframe></div>';
			return $google_iframe;
		}

		return '';
	}

	private function _set_month_names() {
		$month_names = array();
		$month_names[] = __( 'Select one' );

		for ( $n=1; $n <= 12; $n++ ) {
			$month_names[] = date_i18n( 'F', mktime( 0, 0, 0, $n, 1 ) );
		}

		return $month_names;
	}

	private function _set_total_performances() {
		global $wpdb;

		$sql_total_performances = $wpdb->get_row( "SELECT COUNT(ID) AS total FROM $wpdb->posts WHERE post_type = 'performance' AND post_status = 'publish' ");

		return $sql_total_performances->total;
	}

	private function _set_first_available_year() {
		global $wpdb;

		$sql_first_available_year = $wpdb->get_row( "SELECT meta_key, meta_value AS date_selection FROM $wpdb->postmeta WHERE meta_key = 'twp_date_first' ORDER BY meta_value ASC LIMIT 1 ");

		if ( ! $sql_first_available_year OR ! $sql_first_available_year->date_selection ) {
			return false;
		}

		return date( 'Y', $sql_first_available_year->date_selection );
	}

	private function _set_last_available_year() {
		global $wpdb;

		$sql_last_available_year = $wpdb->get_row( "SELECT meta_key, meta_value AS date_selection FROM $wpdb->postmeta WHERE meta_key = 'twp_date_first' ORDER BY meta_value DESC LIMIT 1 ");

		if ( ! $sql_last_available_year OR ! $sql_last_available_year->date_selection ) {
			return false;
		}

		return date( 'Y', $sql_last_available_year->date_selection );
	}
}
