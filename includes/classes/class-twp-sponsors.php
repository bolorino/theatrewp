<?php
if ( realpath(__FILE__) === realpath($_SERVER['SCRIPT_FILENAME']) )
    exit('Do not access this file directly.');

class TWP_Sponsor {

    protected static $default_single_sponsor = 0;

    public function __construct() {

        self::$default_single_sponsor  = ( get_option( 'twp_single_sponsor' ) == 1 ? 1 : 0 );
    }

    /**
     * Get a list of available sponsors.
     *
     * @access public
     * @return array
     */
    public function get_sponsors_titles() {
        $sponsors_query =  get_posts( 'post_type=sponsor&post_status=publish&orderby=title&order=ASC&numberposts=-1' );


        if ( ! $sponsors_query ) {
            $sponsors[] = array(
                'id' => 0,
                'title' => __( 'There are no sponsors yet. Add some in the Sponsors menu.', 'theatre-wp' )
            );

            return $sponsors;
        }

        if ( self::$default_single_sponsor == 1 ) {
            $sponsors[] = array(
                'id' => 0,
                'title' => __( 'No sponsors', 'theatre-wp' )
            );
        }

        foreach ( $sponsors_query as $sponsor ) {
            $sponsors[] =  array(
                'id' => $sponsor->ID,
                'title' => __( $sponsor->post_title )
            );
        }

        return $sponsors;
    }

    public function get_sponsors() {
      global $post;

      $current_category = get_post_type();

      if ( $current_category != 'spectacle' OR ! is_single() ) {
        return false;
      }

      $custom = get_post_custom( $post->ID );

      if ( ! array_key_exists( Theatre_WP::$twp_prefix . 'prod-sponsor', $custom ) )                                                 
      {
        return false;
      }

      $output = '<ul id="sponsors-list">';

      $sponsors_ids = $custom[Theatre_WP::$twp_prefix . 'prod-sponsor'][0];

      $production_sponsors = unserialize( $sponsors_ids );

      if ( $production_sponsors[0] == '0' ) {
        return false;
      }

      foreach ( $production_sponsors as $production_sponsor ) {
        $production_sponsor_data = get_post( $production_sponsor );
        $production_sponsor_metadata = get_post_custom( $production_sponsor );

        $sponsors2sort[] = array(
          'sponsor_weight' => ( array_key_exists( Theatre_WP::$twp_prefix . 'sponsor-weight', $production_sponsor_metadata ) ? intval( $production_sponsor_metadata[Theatre_WP::$twp_prefix . 'sponsor-weight'][0] ) : 0 ),
          'ID'           => $production_sponsor_data->ID,
          'sponsor_logo' => get_the_post_thumbnail( $production_sponsor_data->ID, 'medium' ),
          'sponsor_name' => $production_sponsor_data->post_title,
          'sponsor_url'  => $production_sponsor_metadata[Theatre_WP::$twp_prefix . 'sponsor-url'][0]
        );
      }

      array_multisort( $sponsors2sort, SORT_DESC );

      $sponsors_list = '';

      foreach ( $sponsors2sort as $sponsor2show ) {
        $sponsors_list .= '<li>'
        . '<a href="' . $sponsor2show['sponsor_url'] . '">'
        . $sponsor2show['sponsor_logo']
        . '</a><br>'
        . '<small>' . __( $sponsor2show['sponsor_name'] ) . '</small>'
        . '</li>';
      }

      $output .= $sponsors_list;

      $output .= '</ul>';

      return $output;
    }

}
