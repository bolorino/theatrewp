<?php
if ( realpath(__FILE__) === realpath($_SERVER['SCRIPT_FILENAME']) )
    exit('Do not access this file directly.');

class TWP_Sponsor {

    public function __construct() {

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
                'title' => __('There are no sponsors yet. This checkbox will disappear after you add some in the Sponsors menu.')
            );
            return $sponsors;
        }

        foreach ( $sponsors_query as $sponsor ) {
            $sponsors[] =  array(
                'id' => $sponsor->ID,
                'title' => __($sponsor->post_title)
            );
        }

        return $sponsors;
    }
}
