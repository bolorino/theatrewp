<?php
/**
 * TWP_Show_Upcoming_Performances_Widget class.
 *
 * Plugin Production Sponsors Widget Class
 *
 * @package TheatreWP
 * @author  Jose Bolorino <jose.bolorino@gmail.com>
 */

if ( realpath(__FILE__) === realpath($_SERVER['SCRIPT_FILENAME']) )
    exit('Do not access this file directly.');

class TWP_Production_Sponsors_Widget extends WP_Widget {

    public $id = 'twp-production-sponsors';
    public $title = 'Production Sponsors';
    public $description = 'Display a list Sponsors for the current production';

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
        global $post;

        $title = apply_filters( 'widget_title', $instance['title'] );

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

        echo $args['before_widget'];

        if ( ! empty( $title ) )
            echo $args['before_title'] . $title . $args['after_title'];

        echo $output;

        echo $args['after_widget'];
    }

    public function form( $instance ) {
        if ( isset( $instance[ 'title' ] ) ) {
            $title = $instance[ 'title' ];
        }
        else {
            $title = __( 'New title', 'theatre-wp' );
        }
        ?>

        <p>
        <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
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

        return $instance;
    }

} // class TWP_Production_Sponsors_Widget
