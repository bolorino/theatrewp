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
}
