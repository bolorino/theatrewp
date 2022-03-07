<?php
/**
 * ShowUpcomingPerformancesWidget class.
 *
 * Plugin Production Sponsors Widget Class
 *
 * @package TheatreWP
 * @author  Jose Bolorino <jose.bolorino@gmail.com>
 */

namespace TheatreWP\Widgets;

use TheatreWP\Setup;
use TheatreWP\Sponsor;
use WP_Widget;

if ( realpath(__FILE__) === realpath($_SERVER['SCRIPT_FILENAME']) )
	exit('Do not access this file directly.');

class ProductionSponsorsWidget extends WP_Widget {

	public $id = 'twp-production-sponsors';

	public string $title = 'Production Sponsors';

	public string $description = 'Display a list Sponsors for the current production';

    private Sponsor $sponsor;

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {

		parent::__construct(
			$this->id, // Base ID
			__( $this->title, 'theatre-wp' ), // Name
			array( 'description' => __( $this->description, 'theatre-wp' ), ) // Args
		);

        $this->sponsor = Setup::$sponsor;
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

		$output = $this->sponsor->get_sponsors();

		if ( ! $output ) {
			return false;
		}

		$title = apply_filters( 'widget_title', $instance['title'] );

		echo $args['before_widget'];

		if ( ! empty( $title ) )
			echo $args['before_title'] . $title . $args['after_title'];

		echo $output;

		echo $args['after_widget'];
	}

	public function form( $instance ) {
		$title = $instance['title'] ?? __( 'New title', 'theatre-wp' );
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

} // class ProductionSponsorsWidget
