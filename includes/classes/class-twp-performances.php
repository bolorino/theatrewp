<?php
if (realpath(__FILE__) === realpath($_SERVER['SCRIPT_FILENAME']))
	exit('Do not access this file directly.');


class TWP_Performance {

	protected $spectacle;

	public function __construct( $spectacle ) {
		$this->spectacle = $spectacle;
	}

	/**
	* Get performance custom fields
	*
	* @access public
	* @param int $ID
	* @return array
	*/
	public function get_performance_custom( TWP_Spectacle $spectacle, $ID ) {
		$custom = get_post_custom( intval($ID) );

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


		$spectacle_name                    = sanitize_title( $performance_custom['performance'] );
		$spectacle_data                    = $this->spectacle->get_spectacle_data( $spectacle_name );
		$performance_custom['title']       = $spectacle_data['title'];
		$performance_custom['link']        = $spectacle_data['link'];

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
	        	$output .= '<span class="fecha">Del ' . strftime('%A %e de %B de %Y', $performance_custom['date_first'] )
	        	. '<br />' . ' al ' . strftime( '%A %e de %B de %Y', $performance_custom['date_last'] ) . '</span>';
	        } else {
	        	$performance_date = strftime( '%A %e de %B de %Y', $performance_custom['date_first'] );
	        	$output .= ucfirst( $performance_date );
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

	    foreach ( $next as $post ) : setup_postdata( $post );
	    	$performance_custom = $this->get_performance_custom( $this->spectacle, $post->ID );

	        $spectacle_title = sanitize_title( $performance_custom['performance'] );
	        $spectacle_link = $this->spectacle->get_spectacle_link( $spectacle_title );

	        if ( $title == $performance_custom['performance'] ) {
	        	$this_show = true;

	        	$output .= '<li>';

	        	$output .= '<a href="' . get_permalink() . '">';
	        	$output .= get_the_title( $post->ID ) .'</a> <br />';

	        	if ( $performance_custom['event'] ) {
	        		$output .= $performance_custom['event'] . '<br />';
	        	}

	        	$output .= $performance_custom['town'];

	        	$output .= '<br />';

	        	if ( $performance_custom['date_last'] ) {
	        		$output .= '<span class="fecha">Del ' . strftime('%A %e de %B de %Y', $performance_custom['date_first'] )
	        		. '<br />' . ' al ' . strftime( '%A %e de %B de %Y', $performance_custom['date_last'] ) . '</span>';
	        	} else {
	        		$performance_date = strftime( '%A %e de %B de %Y', $performance_custom['date_first'] );
	        		$output .= ucfirst( $performance_date );
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
}
