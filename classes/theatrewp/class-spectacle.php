<?php
namespace TheatreWP;

if ( realpath(__FILE__) === realpath($_SERVER['SCRIPT_FILENAME']) )
	exit('Do not access this file directly.');

class Spectacle {

	public static array $audience;

	public static string $production_category_slug = 'format';

	private array $_valid_sort_by;

	public function __construct() {

		// Define the available audiences
		$this->set_audiences();

		$this->_valid_sort_by = array( 'title', 'post_date' );
	}

	public function set_audiences() {
		$audience = array(
			array(
				'label'	=> __('All Audiences', 'theatre-wp'),
				'value' => 'All Audiences'
			),
			array(
				'label'	=> __('Adults', 'theatre-wp'),
				'value' => 'Adults'
			),
			array(
				'label'	=> __('Family', 'theatre-wp'),
				'value' => 'Family'
			),
			array(
				'label'	=> __('Kids', 'theatre-wp'),
				'value' => 'Kids'
			),
			array(
				'label'	=> __('Young', 'theatre-wp'),
				'value' => 'Young'
			)
		);

		self::$audience = $audience;
	}

	/**
	 * Get production title thumbnail and link
	 *
	 * @access public
	 *
	 * @param int $ID
	 * @param string $thumbnail_size
	 *
	 * @return array | bool
	 */
	public function get_spectacle_data( int $ID, string $thumbnail_size='thumbnail' ) {
		global $wpdb;

		if ( ! $spectacle = get_post( intval( $ID) ) ) {
			return false;
		}

		$thumbnail_id = get_post_thumbnail_id( intval( $ID) );

		// @todo has_term( false, 'spectacle', $ID )

		$spectacle_data = array();

		$spectacle_data['id'] = $spectacle->ID;
		$spectacle_data['thumbnail'] = get_the_post_thumbnail( $spectacle->ID, $thumbnail_size, array( 'class' => 'twp_production_thumbnail' ) );
		$spectacle_data['thumbnail_url'] = wp_get_attachment_image_src( $thumbnail_id, $thumbnail_size, true );
		$spectacle_data['title'] = $spectacle->post_title;
		$spectacle_data['link'] = home_url('/') . get_option( 'twp_spectacle_slug' ) . '/' . $spectacle->post_name . '/';

		return $spectacle_data;
	}

	/**
	 * Get production custom metadata.
	 *
	 * @access public
	 *
	 * @param int $ID
	 *
	 * @return array | bool
	 */
	public function get_spectacle_custom( int $ID ) {

		$custom = get_post_custom( intval( $ID ) );

		if ( ! $custom ) {
			return false;
		}

		$spectacle_custom = array();

		$spectacle_custom['synopsis'] = ( $custom[ Setup::$twp_prefix . 'synopsis' ][0] ?? false );
		$spectacle_custom['audience'] = ( $custom[ Setup::$twp_prefix . 'audience' ][0] ?? false );
		$spectacle_custom['duration'] = ( $custom[ Setup::$twp_prefix . 'duration' ][0] ?? false );
		$spectacle_custom['credits']  = ( $custom[ Setup::$twp_prefix . 'credits' ][0] ?? false );
		$spectacle_custom['sheet']    = ( $custom[ Setup::$twp_prefix . 'sheet' ][0] ?? false );
		$spectacle_custom['video']    = ( $custom[ Setup::$twp_prefix . 'video' ][0] ?? false );

		return $spectacle_custom;
	}

	/**
	 * Get production main image in available sizes
	 *
	 * @access public
	 *
	 * @param int $ID
	 * @param array $additional_sizes
	 *
	 * @return array | bool
	 */
	public function get_spectacle_thumbnail( int $ID, $additional_sizes=array() ) {
		global $wpdb;

		if ( ! $spectacle = get_post( intval( $ID) ) ) {
			return false;
		}

		$default_thumbnail_sizes = array(
			'thumbnail',
			'medium',
			'large',
			'full'
		);

		$thumbnail_id = get_post_thumbnail_id( intval( $ID) );

		$spectacle_data = array();

		foreach ( $default_thumbnail_sizes as $default_size ) {
			$spectacle_data["thumbnail-$default_size"] = wp_get_attachment_image_src( $thumbnail_id, $default_size, true );
		}

		if ( ! empty( $additional_sizes ) ) {
			foreach( $additional_sizes as $additional_size ) {
				$spectacle_data["thumbnail-$additional_size"] = wp_get_attachment_image_src( $thumbnail_id, $additional_size, true );
			}
		}

		return $spectacle_data;
	}

	/**
	 * Get an HTML list of productions.
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
			$output .= get_the_title( $post->ID ) .'</a></strong> ';

			$output .= '</li>';

		endforeach;

		$output .= '</ul>';

		return $output;
	}

	/**
	 * Get a list of productions titles.
	 *
	 * @access public
	 * @return array | bool
	 */
	public function get_spectacles_titles() {
		$shows_query =  get_posts( 'post_type=spectacle&post_status=publish&orderby=title&order=ASC&numberposts=-1' );

		if ( ! $shows_query ) {
			return false;
		}

		$shows = array();

		foreach ( $shows_query as $show ) {
			$shows[] =  $show->post_title;
		}

		return $shows;
	}

	/**
	 * Get an array of productions [titles] -> [ID]
	 *
	 * @access public
	 * @return array | bool
	 */
	public static function get_spectacles_array() {

		$args = array(
			'post_type'		=> 'spectacle',
			'post_status'	=> 'publish',
			'orderby'		=> 'title',
			'order'			=> 'ASC',
			'numberposts'	=> -1
		);

		/* Polylang compatibility.
		 * If editing a performance get the list of spectacles
		 * in the appropriate language
		 */
		if ( is_admin() ) {
			$lang = false;
			$editing_post = ( isset( $_GET['post'] ) ? intval( $_GET['post'] ) : false );

			// Adding a new translation. No post ID yet but new_lang param in URL
			if ( ! $editing_post ) {
				$lang = ( isset( $_GET['new_lang'] ) ? substr( $_GET['new_lang'], 0, 2 ) : false );
			} elseif ( function_exists( 'pll_get_post_language' ) && current_user_can( 'edit_posts' ) ) {
				$lang = pll_get_post_language( $editing_post );
			}

			// If lang is not set default back
			if ( ! $lang OR empty( $lang ) ) {
				$lang = substr( get_locale(), 0, 2 );

				if ( function_exists( 'pll_default_language' ) ) {
					$lang = pll_default_language();
				}
			}

			// Add the $lang arg to get the translated posts
			if ( $lang ) {
				$args['lang'] = $lang;
			}
		}

		$shows_query =  get_posts( $args );

		if ( ! $shows_query ) {
			return false;
		}

		$shows = array();

		foreach ( $shows_query as $show ) {
			$shows[] = array(
				'label'	=> $show->post_title,
				'value' => $show->ID
			);
		}

		return $shows;
	}

	/**
	 * Get production URL from post name.
	 *
	 * @access public
	 *
	 * @param int $ID
	 *
	 * @return string
	 */
	public function get_spectacle_link( int $ID ) {

		if ( ! $spectacle = get_post( intval( $ID) ) ) {
			return false;
		}

		return home_url( '/' ) . get_option( 'twp_spectacle_slug' ) . '/' . $spectacle->post_name . '/';
	}

}
