<?php
if ( realpath(__FILE__) === realpath($_SERVER['SCRIPT_FILENAME']) )
	exit('Do not access this file directly.');

class TWP_Spectacle {

	public static $audience;

	private $_valid_sort_by;

	public function __construct() {

		// Define the available audiences
		self::$audience = array(
 			__('All Audiences', 'theatrewp'),
 			__('Adults', 'theatrewp'),
 			__('Family', 'theatrewp'),
 			__('Kids', 'theatrewp'),
 			__('Young', 'theatrewp')
 		);

 		$this->_valid_sort_by = array( 'title', 'post_date' );
	}

	/**
	 * Get spectacle title and URL from post name
	 *
	 * @access public
	 * @param string $post_name
	 * @return array
	 */
	public function get_spectacle_data( $post_name ) {
		global $wpdb;

		$spectacle_query = $wpdb->prepare(
			"
			SELECT ID, post_title, post_name FROM $wpdb->posts
			WHERE post_name = %s
			AND post_type = 'spectacle'
			AND post_status = 'publish'
			LIMIT 1
			",
			$post_name
		);

		if ( ! $spectacle = $wpdb->get_row( $spectacle_query ) ) {
			return false;
		}

		$spectacle_data = array();

		$spectacle_data['id'] = $spectacle->ID;
		$spectacle_data['thumbnail'] = get_the_post_thumbnail( $spectacle->ID, 'thumbnail', array( 'class' => 'twp_production_thumbnail' ) );
		$spectacle_data['title'] = __( $spectacle->post_title );
		$spectacle_data['link'] = home_url('/') . get_option( 'twp_spectacle_slug' ) . '/' . $spectacle->post_name . '/';

		return $spectacle_data;
	}

	/**
	 * Get spectacle custom metadata.
	 *
	 * @access public
	 * @param int $ID
	 * @return array
	 */
	public function get_spectacle_custom( $ID ) {

		$custom = get_post_custom( intval($ID) );

		if ( ! $custom ) {
			return false;
		}

		$spectacle_custom = array();

		$spectacle_custom['synopsis'] = ( isset($custom[Theatre_WP::$twp_prefix . 'synopsis'][0]) ? $custom[Theatre_WP::$twp_prefix . 'synopsis'][0] : false );
		$spectacle_custom['audience'] = ( isset($custom[Theatre_WP::$twp_prefix . 'audience'][0]) ? $custom[Theatre_WP::$twp_prefix . 'audience'][0] : false );
		$spectacle_custom['credits']  = ( isset($custom[Theatre_WP::$twp_prefix . 'credits'][0]) ? $custom[Theatre_WP::$twp_prefix . 'credits'][0] : false );
		$spectacle_custom['sheet']    = ( isset($custom[Theatre_WP::$twp_prefix . 'sheet'][0]) ? $custom[Theatre_WP::$twp_prefix . 'sheet'][0] : false );
		$spectacle_custom['video']    = ( isset($custom[Theatre_WP::$twp_prefix . 'video'][0]) ? $custom[Theatre_WP::$twp_prefix . 'video'][0] : false );

		return $spectacle_custom;
	}

	/**
	 * Get an HTML list of spectacles.
	 *
	 * @access public
	 * @return string
	 */
	public function get_spectacles( $limit, $sort_by, $sort ) {
		$limit = intval( $limit );

		if ( $limit == 0 ) {
			$limit = -1;
		}

		if ( ! in_array( $sort_by, $this->_valid_sort_by ) ) {
			return false;
		}

		if ( 'ASC' != $sort && 'DESC' != $sort ) {
			return false;
		}

		$shows_query = get_posts( "post_type=spectacle&post_status=publish&orderby=$sort_by&order=$sort&numberposts=$limit" );

		if ( ! $shows_query ) {
			return false;
		}

		$output = '<ul class="spectacles">';

		foreach ( $shows_query as $post ) : setup_postdata( $post );
			$output .= '<li>';

	        $output .= '<strong><a href="' . get_permalink( $post->ID ) . '">';
	        $output .= get_the_title( $post->ID ) .'</a></strong> <br />';

	        $output .= '</li>';

		endforeach;

	    $output .= '</ul>';

	    return $output;
	}

	/**
	 * Get a list of spectacles titles.
	 *
	 * @access public
	 * @return array
	 */
	public function get_spectacles_titles() {
		$shows_query =  get_posts( 'post_type=spectacle&post_status=publish&orderby=title&order=ASC&numberposts=-1' );

		if ( ! $shows_query ) {
			return false;
		}

		foreach ( $shows_query as $show ) {
			$shows[] =  __($show->post_title);
		}

		return $shows;
	}

	/**
	 * Get spectacle URL from post name.
	 *
	 * @access public
	 * @param string $post_name
	 * @return string
	 */
	public function get_spectacle_link( $post_name ) {
		global $wpdb;

		$spectacle_query = $wpdb->prepare(
			"SELECT post_name
			FROM $wpdb->posts
			WHERE post_title = %s
			AND post_type = 'spectacle'
			AND post_status = 'publish'
			LIMIT 1",
			$post_name
		);

		if ( ! $spectacle = $wpdb->get_row( $spectacle_query ) ) {
			return false;
		}

		$link = home_url( '/' ) . get_option( 'twp_spectacle_slug' ) . '/' . $spectacle->post_name . '/';

		return $link;
	}

}
