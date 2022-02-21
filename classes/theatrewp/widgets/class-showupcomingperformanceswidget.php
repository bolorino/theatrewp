<?php
/**
 * ShowUpcomingPerformancesWidget class.
 *
 * Plugin Show Upcoming Performances Widget Class
 *
 * @package TheatreWP
 * @author  Jose Bolorino <jose.bolorino@gmail.com>
 */

namespace TheatreWP\Widgets;
use WP_Widget;

if ( realpath(__FILE__) === realpath($_SERVER['SCRIPT_FILENAME']) )
    exit('Do not access this file directly.');

class ShowUpcomingPerformancesWidget extends WP_Widget {

    public $id = 'twp-show-next-performances';
    public string $title = 'Production Upcoming Performances';
    public string $description = 'Display a list of upcoming performances for the current production';

    /**
     * Register widget with WordPress.
     */
    function __construct() {

        parent::__construct(
            $this->id, // Base ID
            __( $this->title, 'theatre-wp' ), // Name
            array( 'description' => __( $this->description, 'theatre-wp' ), ) // Args
        );
    }

    /**
     * Front-end display of widget.
     *
     * @see WP_Widget::widget()
     *
     * @param array $args     Widget arguments.
     * @param array $instance Saved values from database.
     *
     * @return void | bool
     */
    public function widget( $args, $instance ) {
        global $post, $theatre_wp;
        $current_category = get_post_type();

        if ( $current_category != 'spectacle' OR ! is_single() ) {
            return false;
        }

        if ( ! $performances = $theatre_wp->get_show_next_performances() ) {
            return false;
        }

        $title = get_the_title( $post->ID );

        echo $args['before_widget'];

        echo $args['before_title'] . sprintf( __( '“%s” Upcoming Performances', 'theatre-wp' ), $title ) . $args['after_title'];

        echo $performances;

        echo $args['after_widget'];
    }


} // class UpcomingPerformancesWidget
