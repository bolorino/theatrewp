<?php
namespace TheatreWP;

if (realpath(__FILE__) === realpath($_SERVER['SCRIPT_FILENAME']))
	exit('Do not access this file directly.');


class Performance {

	protected Spectacle $spectacle;

	public array $month_names;

	public string $month;

	public string $year;

	public int $total_performances;

	public string $first_available_year;

	public string $last_available_year;

	public string $language;

	public string $polylang_language;

	public function __construct( Spectacle $spectacle ) {
		$this->spectacle = $spectacle;

		$this->_set_month_names( __( 'Select Month', 'theatre-wp' ) );
		$this->month = date('m');
		$this->year = date('Y');

		$this->_set_total_performances();

		$this->_set_first_available_year();

		$this->_set_last_available_year();

	}

	/**
	* Gets performance custom fields
	*
	* @access public
	 * @param int $ID
	* @return array | bool
	*/
	public function get_performance_custom(int $ID) {
		$custom = get_post_custom( $ID );

		if ( ! $custom ) {
			return false;
		}

		$performance_custom = array();

		$performance_custom['spectacle_id'] = $custom[ Setup::$twp_prefix . 'spectacle_id' ][0] ?? false;
		$performance_custom['event']       = $custom[ Setup::$twp_prefix . 'event' ][0] ?? false;
		$performance_custom['place']       = $custom[ Setup::$twp_prefix . 'place' ][0] ?? false;
		$performance_custom['address']     = $custom[ Setup::$twp_prefix . 'address' ][0] ?? false;
		$performance_custom['town']        = $custom[ Setup::$twp_prefix . 'town' ][0] ?? false;
		$performance_custom['region']      = $custom[ Setup::$twp_prefix . 'region' ][0] ?? false;
		$performance_custom['country']     = $custom[ Setup::$twp_prefix . 'country' ][0] ?? false;
		$performance_custom['date_first']  = $custom[ Setup::$twp_prefix . 'date_first' ][0] ?? false;
		$performance_custom['date_last']   = $custom[ Setup::$twp_prefix . 'date_last' ][0] ?? false;
		$performance_custom['display_map'] = $custom[ Setup::$twp_prefix . 'display_map' ][0] ?? false;

		if ( get_option( 'twp_tickets_info' ) == 1 ) {
			$performance_custom['tickets_url'] = $custom[Setup::$twp_prefix . 'tickets_url'][0] ?? false;
			$performance_custom['tickets_price'] = $custom[Setup::$twp_prefix . 'tickets_price'][0] ?? false;
			$performance_custom['free_entrance'] = $custom[Setup::$twp_prefix . 'free_entrance'][0] ?? false;
			$performance_custom['invitation'] = $custom[Setup::$twp_prefix . 'invitation'][0] ?? false;
		}

		$spectacle_data                        = $this->spectacle->get_spectacle_data( intval( $performance_custom['spectacle_id'] ) );
		$performance_custom['spectacle_title'] = $spectacle_data['title'];
		$performance_custom['spectacle_url']   = $spectacle_data['link'];

		return $performance_custom;
	}

	/**
	 * Gets an HTML list of upcoming performances.
	 *
	 * @access public
	 * @return string
	 */
	public function get_next_performances() {
		$now = time();
		$number_to_display = intval( get_option( 'twp_widget_performances_number' ) );

		$args = array(
			'post_status'  => 'publish',
			'post_type'    => 'performance',
			'meta_key'     => Setup::$twp_prefix . 'date_first',
			'orderby'      => 'meta_value',
			'meta_compare' => '>=',
			'meta_value'   => $now,
			'order'        => 'ASC',
			'posts_per_page'  => ( $number_to_display > 0 ? $number_to_display : -1 )
		);

		$next = get_posts( $args );

		if ( ! $next ) {
			return false;
		}

		$output = '<ul class="next-performances">';

		foreach ( $next as $post ) : setup_postdata( $post );
			$performance_custom = $this->get_performance_custom($post->ID);

			$spectacle_link = $this->spectacle->get_spectacle_link( $performance_custom['spectacle_id'] );

			$output .= '<li>';

			$output .= '<strong><a href="' . get_permalink( $post->ID ) . '">';
			$output .= get_the_title( $post->ID ) .'</a></strong> <br />';

			$output .= '<em><a href="' . $spectacle_link . '">';
			$output .= get_post_field( 'post_title', $performance_custom['spectacle_id'] ) .'</a></em> <br />';

			if ( $performance_custom['event'] ) {
				$output .= $performance_custom['event'] . '<br />';
			}

			if ( $performance_custom['date_last'] ) {
				$output .= $this->get_performance_dates( $performance_custom );
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
	 * Returns string of range dates for performance
	 *
	 * @param array $performance_custom
	 * @return string
	 */
	public function get_performance_dates( array $performance_custom ) {

		$output = '<span class="twpdate">';
		$output .= _x( 'From', '(date) performing from day', 'theatre-wp' );
		$output .= ' ' . date_i18n( get_option( 'date_format' ), $performance_custom['date_first'] ) . ' '
			. _x( 'To', '(date) performing to day', 'theatre-wp' ) . ' '
			. date_i18n( get_option( 'date_format' ), $performance_custom['date_last'] )
			. '</span>';

		return $output;
	}

	/**
	 * Gets an HTML list of current show upcoming performances .
	 *
	 * @access public
	 * @return string | bool
	 */
	public function get_show_next_performances( int $spectacle_id = 0 ) {
		global $post;

		$next = $this->get_upcoming_performances( $spectacle_id );

		if ( ! $next ) {
			return false;
		}

		$this_show = false; // There are future performances, but need to check for this show
		$max_count = 5;
		$shown = 0;

		$output = '<ul class="next-performances">';

		foreach ( $next as $performance ) : setup_postdata( $performance );
			$performance_custom = $this->get_performance_custom($performance->ID);

			$spectacle_link = $this->spectacle->get_spectacle_link( intval( $performance_custom['spectacle_id'] ) );

			if ( $post->ID == $performance_custom['spectacle_id'] AND $shown < $max_count ) {
				$this_show = true;
				$shown++;

				$output .= '<li>';

				$output .= '<a href="' . get_permalink( $performance->ID ) . '">';
				$output .= get_the_title( $performance->ID ) .'</a> <br />';

				if ( $performance_custom['event'] ) {
					$output .= $performance_custom['event'] . '<br />';
				}

				$output .= $performance_custom['town'];

				$output .= '<br />';

				if ( $performance_custom['date_last'] ) {
					$output .= $this->get_performance_dates( $performance_custom );
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
	 * Gets an array containing current show upcoming performances .
	 *
	 * @access public
	 * @return array | bool
	 */
	public function get_show_next_performances_array() {
		global $post;

		$next = $this->get_upcoming_performances();

		if ( ! $next ) {
			return false;
		}

		$performances = array();
		$this_show = false; // There are future performances, but need to check for this show
		$max_count = 5;
		$shown = 0;

		foreach ( $next as $performance ) : setup_postdata( $performance );
			$performance_custom = $this->get_performance_custom($performance->ID);

			$spectacle_link = $this->spectacle->get_spectacle_link( $performance_custom['spectacle_id'] );

			if ( $post->ID == $performance_custom['spectacle_id'] AND $shown < $max_count ) {
				$this_show = true;

				$performances[$shown]['title'] = get_the_title( $performance->ID );
				$performances[$shown]['link'] = get_permalink( $performance->ID );

				if ( $performance_custom['event'] ) {
					$performances[$shown]['event'] = $performance_custom['event'];
				}

				$performances[$shown]['town'] = $performance_custom['town'];

				if ( ! empty( $performance_custom['region'] ) ) {
					$performances[$shown]['region'] = $performance_custom['region'];
				}

				if ( ! empty( $performance_custom['place'] ) ) {
					$performances[$shown]['place'] = $performance_custom['place'];
				}

				$performances[$shown]['date_first'] = $performance_custom['date_first'];

				if ( $performance_custom['date_last'] ) {
					$performances[$shown]['date_last'] = $performance_custom['date_last'];
				}

				// Get tickets info
				if ( get_option( 'twp_tickets_info' ) == 1 ) {

					if ( isset( $performance_custom['tickets_url'] ) AND ! empty( $performance_custom['tickets_url'] ) ) {
						$performances[$shown]['tickets_url'] = $performance_custom['tickets_url'];
					}

					if ( isset( $performance_custom['tickets_price'] ) AND ! empty( $performance_custom['tickets_price'] ) ) {
						$performances[$shown]['tickets_price'] = $performance_custom['tickets_price'];
					}

					if ( isset( $performance_custom['free_entrance'] ) AND ! empty( $performance_custom['free_entrance'] ) ) {
						$performances[$shown]['free_entrance'] = $performance_custom['free_entrance'];
					}

					if ( isset( $performance_custom['invitation'] ) AND ! empty( $performance_custom['invitation'] ) ) {
						$performances[$shown]['invitation'] = $performance_custom['invitation'];
					}
				}

				$shown++;
			}
		endforeach;

		if ( ! $this_show ) {
			return false;
		}

		return $performances;
	}

	/**
	* Gets a date filtered list of performances.
	*
	* @access public
	*
	* @param array $calendar_filter_params
	*
	* @return object | bool
	*/
	public function get_filtered_calendar( array $calendar_filter_params ) {
		global $wpdb;
		// $calendar_filter_params:
		// month, year, page

		// Default values
		$page = 1;
		$offset = 0;

		if ( ! empty( $calendar_filter_params ) ) {
			$this->month = ( array_key_exists( 'month', $calendar_filter_params ) ? intval( $calendar_filter_params['month'] ) : 0 );
			$this->year  = ( array_key_exists( 'year', $calendar_filter_params ) ? intval( $calendar_filter_params['year'] ) : 0 );
			$page        = ( array_key_exists( 'page', $calendar_filter_params ) ? intval( $calendar_filter_params['page'] ) : 1 );
		}

		$performances_per_page = get_option( 'twp_performances_number' );

		if ( $page > 1 ) {
			$offset = ($page-1)*$performances_per_page;
		}

		$sql_calendar = "SELECT $wpdb->posts.ID, post_title, post_name, meta_key, meta_value, FROM_UNIXTIME(meta_value, '%M') AS month, FROM_UNIXTIME(meta_value, '%Y') AS year, post_author, post_date, post_content, post_status
			FROM $wpdb->posts, $wpdb->postmeta";

		// Polylang compatibility
		$this->polylang_language = $this->get_polylang_language();

		if ( $this->polylang_language ) {
			$sql_calendar .= " INNER JOIN $wpdb->term_relationships wtr
				ON ($wpdb->postmeta.post_id = wtr.object_id)
				INNER JOIN $wpdb->term_taxonomy wtt
					ON (wtr.term_taxonomy_id = wtt.term_taxonomy_id)
				INNER JOIN $wpdb->terms wt
					ON (wt.term_id = wtt.term_id) ";
		}

		$sql_calendar .= "
			WHERE " . $wpdb->posts . '.ID = ' . $wpdb->postmeta . ".post_id
			AND post_type = 'performance'
			AND post_status = 'publish'
			AND meta_key = '" . Setup::$twp_prefix . "date_first' ";

		$sql_calendar .= $this->_get_calendar_sql_date();

		// Polylang compatibility
		if ( $this->polylang_language ) {
			$sql_calendar .= "AND wtt.taxonomy = 'language'
				AND wt.slug = '$this->polylang_language' ";
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

	/**
	 * Counts the total filtered performances
	 *
	 * @param array $calendar_filter_params
	 * @return int|false
	 */
	public function get_total_filtered_performances( array $calendar_filter_params ) {
		global $wpdb;

		if ( ! empty( $calendar_filter_params ) ) {
			$this->month = ( array_key_exists( 'month', $calendar_filter_params ) ? intval( $calendar_filter_params['month'] ) : 0 );
			$this->year  = ( array_key_exists( 'year', $calendar_filter_params ) ? intval( $calendar_filter_params['year'] ) : 0 );
		}

		$sql_calendar = "SELECT COUNT(ID) AS total
			FROM $wpdb->posts, $wpdb->postmeta ";

		// Polylang compatibility
		$this->polylang_language = $this->get_polylang_language();

		if ( $this->polylang_language ) {
			$sql_calendar .= " INNER JOIN $wpdb->term_relationships wtr
				ON ($wpdb->postmeta.post_id = wtr.object_id)
				INNER JOIN $wpdb->term_taxonomy wtt
					ON (wtr.term_taxonomy_id = wtt.term_taxonomy_id)
				INNER JOIN $wpdb->terms wt
					ON (wt.term_id = wtt.term_id) ";
		}

		$sql_calendar .= "WHERE " . $wpdb->posts . '.ID = ' . $wpdb->postmeta . ".post_id
			AND post_type = 'performance'
			AND post_status = 'publish'
			AND meta_key = '" . Setup::$twp_prefix . "date_first' ";

		$sql_calendar .= $this->_get_calendar_sql_date();

		// Polylang compatibility
		if ( $this->polylang_language ) {
			$sql_calendar .= " AND wtt.taxonomy = 'language'
				AND wt.slug = '$this->polylang_language' ";
		}

		$count_filtered_performances = $wpdb->get_row( $sql_calendar );

		if ( empty( $count_filtered_performances ) ) {
			return false;
		}

		return $count_filtered_performances->total;

	}

	/**
	 * Returns WP_Posts for upcoming performances
	 *
	 * @return false|int[]|\WP_Post[]
	 */
	public function get_upcoming_performances( int $spectacle_id = 0) {

		$current_category = get_post_type();

		if ( 'spectacle' != $current_category  ) {
			return false;
		}

		$now = time();
		$number_to_display = intval( get_option( 'twp_widget_performances_number' ) );

		$args = array(
			'post_status'  => 'publish',
			'post_type' => 'performance',
			'meta_query' => array(
				array(
					'key' => Setup::$twp_prefix . 'date_first',
					'orderby' => 'meta_value',
					'compare' => '>=',
					'value' => $now,
				)
			),
			'order' => 'ASC',
			'numberposts' => ( $number_to_display > 0 ? $number_to_display : -1 )
		);

		// If a spectacle_id is given get only the performances which have this spectacle ID
		if ( $spectacle_id != 0 ) {
			$args['meta_query'][] = array(
				'key' => Setup::$twp_prefix . 'spectacle_id',
				'compare' => '=',
				'value' => $spectacle_id
			);
		}

		return get_posts( $args );

	}

	/**
	* Gets an array of busy dates.
	*
	* @access public
	* @param array $calendar_filter_params
	* @return array|false
	*/
	public function get_busy_dates( array $calendar_filter_params ) {
		global $wpdb, $post;
		// $calendar_filter_params:
		// month, year

		if ( ! empty( $calendar_filter_params ) ) {
			$this->month = ( array_key_exists( 'month', $calendar_filter_params ) ? intval( $calendar_filter_params['month'] ) : 0 );
			$this->year  = ( array_key_exists( 'year', $calendar_filter_params ) ? intval( $calendar_filter_params['year'] ) : 0 );
		}

		$sql_calendar = "SELECT $wpdb->posts.ID, post_title, post_name, meta_key, meta_value, FROM_UNIXTIME(meta_value, '%d') AS day, FROM_UNIXTIME(meta_value, '%m') AS month, FROM_UNIXTIME(meta_value, '%Y') AS year, post_status
			FROM $wpdb->posts, $wpdb->postmeta";

		// Polylang compatibility
		$this->polylang_language = $this->get_polylang_language();

		if ( $this->polylang_language ) {
			$sql_calendar .= " INNER JOIN $wpdb->term_relationships wtr
				ON ($wpdb->postmeta.post_id = wtr.object_id)
				INNER JOIN $wpdb->term_taxonomy wtt
					ON (wtr.term_taxonomy_id = wtt.term_taxonomy_id)
				INNER JOIN $wpdb->terms wt
					ON (wt.term_id = wtt.term_id) ";
		}

		$sql_calendar .= "
			WHERE " . $wpdb->posts . '.ID = ' . $wpdb->postmeta . ".post_id
			AND post_type = 'performance'
			AND post_status = 'publish'
			AND meta_key = '" . Setup::$twp_prefix . "date_first' ";

		$sql_calendar .= $this->_get_calendar_sql_date();

		// Polylang compatibility
		if ( $this->polylang_language ) {
			$sql_calendar .= "AND wtt.taxonomy = 'language'
				AND wt.slug = '$this->polylang_language' ";
		}

		$sql_calendar .= 'ORDER BY meta_value ';

		$filtered_calendar = $wpdb->get_results( $sql_calendar, ARRAY_A );

		if ( empty( $filtered_calendar ) ) {
			return false;
		}

		return $filtered_calendar;
	}

	/**
	* Returns an embedded Google Maps for the given event
	*
	* @param array $custom_meta
	* @param int $width
	* @param int $height
	* @return string - an iframe pulling http://maps.google.com/ for this event
	*/
	function get_event_google_map_embed( $custom_meta, $width ='', $height = '' ) {

		$this->language = substr( get_locale(), 0, 2 );

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
			return '<div id="googlemaps"><iframe width="' . $width . '" height="' . $height . '" src="https://www.google.com/maps/embed/v1/place?key=' . get_option( 'twp_google_maps_api' )
			. '&amp;language=' . $this->language
			. '&amp;q='.$google_address.'?>"></iframe></div>';
		}

		return '';
	}

	private function _set_month_names( $month_selection_text ) {
		$month_names = array();
		$month_names[] = $month_selection_text;

		for ( $n=1; $n <= 12; $n++ ) {
			$month_names[] = date_i18n( 'F', mktime( 0, 0, 0, $n, 1 ) );
		}

		$this->month_names = $month_names;
	}

	/**
	 * Sets the total number of performances on DB
	 *
	 * @return void
	 */
	private function _set_total_performances() {
		global $wpdb;

		$sql_total_performances = $wpdb->get_row( "SELECT COUNT(ID) AS total FROM $wpdb->posts WHERE post_type = 'performance' AND post_status = 'publish' ");

		$this->total_performances = $sql_total_performances->total;
	}

	/**
	 * Sets the first registered performance year
	 *
	 * @return void
	 */
	private function _set_first_available_year() {
		global $wpdb;

		$sql_first_available_year = $wpdb->get_row( "SELECT meta_key, meta_value AS date_selection FROM $wpdb->postmeta WHERE meta_key = 'twp_date_first' ORDER BY meta_value ASC LIMIT 1 ");

		if ( ! $sql_first_available_year OR ! $sql_first_available_year->date_selection ) {
			$this->first_available_year = date( 'Y' );
		} else {
			$this->first_available_year = date( 'Y', $sql_first_available_year->date_selection );
		}
	}

	/**
	 * Sets the last registered performance year
	 *
	 * @return void
	 */
	private function _set_last_available_year() {
		global $wpdb;

		$sql_last_available_year = $wpdb->get_row( "SELECT meta_key, meta_value AS date_selection FROM $wpdb->postmeta WHERE meta_key = 'twp_date_first' ORDER BY meta_value DESC LIMIT 1 ");

		if ( ! $sql_last_available_year OR ! $sql_last_available_year->date_selection ) {
			$this->last_available_year = date( 'Y' );
		} else {
			$this->last_available_year = date( 'Y', $sql_last_available_year->date_selection );
		}
	}

	/**
	* Polylang compatibility for performances custom query
	*
	* @return string - two char Country ISO code set by Polylang
	*/
	public function get_polylang_language() {
		if ( ! function_exists( 'pll_current_language' ) ) {
			return false;
		}

		return pll_current_language();
	}

	/**
	 * Builds SQL to get performances filtered by date
	 *
	 * @return string
	 */
	private function _get_calendar_sql_date(): string
	{
		$sql_calendar = '';

		if (0 == $this->month && 0 == $this->year) {
			// Upcoming performances. Not month nor year passed
			// @TODO $topdate to config
			$topdate = time() - 72800;
			$sql_calendar .= "AND meta_value >= $topdate ";
		} elseif ($this->year == 0) {
			$this->year = date('Y');
		}

		if (0 != $this->year) {
			$sql_calendar .= "AND FROM_UNIXTIME(meta_value, '%Y') = $this->year ";
		}

		if (0 != $this->month) {
			$sql_calendar .= "AND FROM_UNIXTIME(meta_value, '%m') = $this->month ";
		}

		return $sql_calendar;
	}
}
