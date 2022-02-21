<?php
/**
 * UpcomingPerformancesWidget class.
 *
 * Plugin Upcoming Performances Widget Class
 *
 * @package TheatreWP
 * @author  Jose Bolorino <jose.bolorino@gmail.com>
 */

namespace TheatreWP\Widgets;
use WP_Widget;

if ( realpath(__FILE__) === realpath($_SERVER['SCRIPT_FILENAME']) )
    exit('Do not access this file directly.');

class UpcomingPerformancesWidget extends WP_Widget {

    public $id          = 'twp-next-performances';
    public string $title       = 'Upcoming Performances';
    public string $description = 'Display a list of upcoming performances';

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
     */
    public function widget( $args, $instance ) {
        global $theatre_wp;

        // @ ToDo add param to exclude current spectacle
        // in Performance get_next_performances()
        if ( ! $performances = $theatre_wp->get_next_performances() ) {
            return false;
        }

        $title = apply_filters( 'widget_title', $instance['title'] );

        echo $args['before_widget'];
        if ( ! empty( $title ) )
            echo $args['before_title'] . $title . $args['after_title'];

        echo $performances;

        echo $args['after_widget'];
    }

    /**
     * Back-end widget form.
     *
     * @see WP_Widget::form()
     *
     * @param array $instance Previously saved values from database.
     */
    public function form( $instance ) {
	    $title = $instance['title'] ?? __( 'New title', 'theatre-wp' );

        $number = ( isset( $instance['number'] ) && intval( $instance['number'] ) >= 0 ? intval( $instance['number'] ) : get_option( 'twp_widget_performances_number' ) );
        ?>

        <p>
        <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        </p>

        <p>
        <label for="widget-performances-number"><?php _e( 'Number of performances to display (0 for all):', 'theatre-wp' ); ?></label>
        <input id="widget-performances-number" name="<?php echo $this->get_field_name( 'number' ); ?>" type="text" size="3" value="<?php echo $number; ?>">
        </p>

        <?php
    }

    /**
     * Sanitize widget form values as they are saved.
     *
     * @see WP_Widget::update()
     *
     * @param array $new_instance Values just sent to be saved.
     * @param array $old_instance Previously saved values from database.
     *
     * @return array Updated safe values to be saved.
     */
    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
        $instance['number'] = ( ! empty( $new_instance['number'] ) ) ? intval( $new_instance['number'] ) : 0;

        update_option( 'twp_widget_performances_number', $instance['number'] );

        return $instance;
    }

} // class UpcomingPerformancesWidget
