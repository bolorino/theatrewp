<?php
/**
 * TWP_Widget class.
 *
 * Plugin widgets class
 *
 * @package TheatreWP
 * @author  Jose Bolorino <jose.bolorino@gmail.com>
 */

if ( realpath(__FILE__) === realpath($_SERVER['SCRIPT_FILENAME']) )
    exit('Do not access this file directly.');

class TWP_Widget {

    /**
     * @var TWP_Spectacle
     */
    public $spectacle;

    /**
     * @var TWP_Performance
     */
    public $performance;

    public function __construct( TWP_Spectacle $spectacle, TWP_Performance $performance ) {
        $this->spectacle = $spectacle;
        $this->performance = $performance;

    }

    public function init() {
        wp_register_sidebar_widget(
            'twp-show-spectacles',
            __( 'Productions', 'theatrewp' ),
            array( $this, 'widget_show_spectacles' ),
            array(
                'description' => __('Display a list of your productions', 'theatrewp')
            )
        );
        wp_register_widget_control(
            'twp-show-spectacles',
            __('Productions', 'theatrewp'),
            array ( $this, 'widget_show_spectacles_control' )
        );

        wp_register_sidebar_widget( 'twp-show-next-performances', __( 'Spectacle Next Performances', 'theatrewp' ), array( $this, 'widget_show_next_performances' ) );

        wp_register_sidebar_widget( 'twp-next-performances', __( 'Global Next Performances', 'theatrewp' ), array( $this, 'widget_next_performances' ) );

        wp_register_sidebar_widget( 'twp-production-sponsors', __( 'Production Sponsors', 'theatrewp' ), array( $this, 'widget_production_sponsors') );
    }
    /**
     * Spectacles Widget
     *
     * @access public
     * @return void
     */
    public function widget_show_spectacles( $args ) {

        $widget_title      = get_option( 'twp_widget_spectacles_title' );
        $spectacles_number = get_option( 'twp_widget_spectacles_number' );
        $sort_by           = get_option( 'twp_widget_spectacles_sortby' );
        $sort              = get_option( 'twp_widget_spectacles_sort' );

        if ( ! $spectacles = $this->spectacle->get_spectacles( $spectacles_number, $sort_by, $sort ) ) {
            return false;
        }

        extract( $args );

        echo $before_widget;

        echo $before_title . $widget_title . $after_title;

        echo $spectacles;

        echo $after_widget;

    }

    /**
     * Spectacles Widget Options
     *
     * @access public
     * @return void
     */
    public function widget_show_spectacles_control( $args=array(), $params=array() ) {

        if ( isset( $_POST['submitted'] ) ) {
            update_option( 'twp_widget_spectacles_title', $_POST['widget_title'] );
            update_option( 'twp_widget_spectacles_number', intval( $_POST['number'] ) );
            update_option( 'twp_widget_spectacles_sortby', $_POST['sortby'] );
            update_option( 'twp_widget_spectacles_sort', $_POST['sort'] );
        }

        $widget_title = get_option( 'twp_widget_spectacles_title' );
        $spectacles_number = get_option( 'twp_widget_spectacles_number' );
        $sort_by = get_option( 'twp_widget_spectacles_sortby' );
        $sort = get_option( 'twp_widget_spectacles_sort' );

        $output = '<p>'
        . '<label for="widget-show-spectacles-title">'
        . __( 'Title:' ) . '</label>'
        . '<input type="text" class="widefat" id="widget-show-spectacles-title" name="widget_title" value="' . stripslashes( $widget_title ) .'">'
        . '</p>'
        . '<p>'
        . '<label for="widget-show-spectacles-number">'
        . __( 'Number of spectacles to show (0 for all):', 'theatrewp' )
        . '</label>'
        . '<input type="text" size="3" value="' . $spectacles_number . '" id="widget-show-spectacles-number" name="number">'
        . '</p>'
        . '<p>'
        . '<label for="widget-show-spectacles-sortby"> '
        . __( 'Sort by', 'theatrewp' )
        . '</label>'
        . '<select name="sortby">'
        . '<option value="post_date"';

        if ( $sort_by == 'post_date' ) {
            $output .= ' selected="selected"';
        }

        $output .= '>' . __( 'Date' , 'theatrewp' ) . '</option>'
        . '<option value="title"';

        if ( $sort_by == 'title' ) {
            $output .= ' selected="selected"';
        }

        $output .= '>' . __( 'Title', 'theatrewp' ) . '</option>'
        . '</select>'
        . '<select name="sort">'
        . '<option value="ASC"';

        if ( $sort == 'ASC' ) {
            $output .= ' selected="selected"';
        }

        $output .= '>' . __( 'Asc', 'theatrewp' ) . '</option>';

        $output .= '<option value="DESC"';

        if ( $sort == 'DESC' ) {
            $output .= ' selected="selected"';
        }

        $output .= '>' . __( 'Desc', 'theatrewp' ) . '</option>';

        $output .= '</select>'
        . '</p>'
        . '<p>'
        . '<input type="hidden" name="submitted" value="1">'
        . '</p>';

        echo $output;
    }

    /**
     * Upcoming Performances Widget
     *
     * @access public
     * @return void
     */
    public function widget_next_performances( $args ) {
        if ( ! $performances = $this->performance->get_next_performances() ) {
            return false;
        }

        extract( $args );

        echo $before_widget;
        echo $before_title . __( 'Upcoming Performances', 'theatrewp' ) . $after_title;

        echo $performances;

        echo $after_widget;
    }

    /**
     * Current Spectacle Upcoming Performances Widget
     *
     * @access public
     * @return void
     */
    public function widget_show_next_performances( $args ) {
        global $post;
        $current_category = get_post_type();

        if ( $current_category != 'spectacle' OR ! is_single() ) {
            return false;
        }

        $title = get_the_title( $post->ID );

        if ( ! $performances = $this->performance->get_show_next_performances() ) {
            return false;
        }

        extract( $args );

        echo $before_widget;
        echo $before_title . sprintf( __( '“%s” Upcoming Performances', 'theatrewp' ), $title ) . $after_title;

        echo $performances;

        echo $after_widget;
    }

    /**
     * Current Spectacle Sponsors Widget
     *
     * @access public
     * @return void
     */

    public function widget_production_sponsors( $args ) {
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

        $production_sponsors = unserialize( $custom[Theatre_WP::$twp_prefix . 'prod-sponsor'][0] );

        $sponsors_list = '<ul id="sponsors-list">';

        foreach ( $production_sponsors as $production_sponsor ) {
            $production_sponsor_data = get_post( $production_sponsor );
            $production_sponsor_metadata = get_post_custom( $production_sponsor );

            $sponsors2sort[] = array(
                'sponsor_weight' => $production_sponsor_metadata[Theatre_WP::$twp_prefix . 'sponsor-weight'][0],
                'ID'           => $production_sponsor_data->ID,
                'sponsor_logo' => get_the_post_thumbnail( $production_sponsor_data->ID, 'medium' ),
                'sponsor_name' => $production_sponsor_data->post_title,
                'sponsor_url'  => $production_sponsor_metadata[Theatre_WP::$twp_prefix . 'sponsor-url'][0]
            );
        }

        array_multisort( $sponsors2sort, SORT_DESC );

        foreach ( $sponsors2sort as $sponsor2show ) {
            $sponsors_list .= '<li>'
            . '<a href="' . $sponsor2show['sponsor_url'] . '">'
            . $sponsor2show['sponsor_logo']
            . '</a><br>'
            . '<small>' . __( $sponsor2show['sponsor_name'] ) . '</small>'
            . '</li>';
        }

        $sponsors_list .= '</ul>';

        extract( $args );

        echo $before_widget;
        echo $before_title . __( 'Sponsors', 'theatrewp' ) . $after_title;

        echo $sponsors_list;

        echo $after_widget;
    }

}
