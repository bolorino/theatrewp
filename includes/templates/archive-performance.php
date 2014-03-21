<?php
/**
 * The Template for displaying list of performances.
 *
 * @package WordPress
 * @subpackage TheatreWordPress
 * @since TheatreWordPress 0.1
 */

get_header(); ?>
<?php
// Performances archive URL
$action_form = home_url( '/' ) . get_option( 'twp_performances_slug' );

$dates_sent = false;
$calendar_data = $theatre_wp->get_calendar_data();

$total_performances = $theatre_wp->get_total_performances();

$paged = get_query_var( 'paged' ) ? get_query_var('paged') : 1;
$calendar_filter_params = array();

// check for sent date
if ( isset( $_POST['twpm'] ) && isset( $_POST['twpy'] ) ) {
	$calendar_filter_params = array(
		'month'	=> intval( $_POST['twpm'] ),
		'year'	=> intval( $_POST['twpy'] ),
		'page'	=> 0
	);

	$total_performances = $theatre_wp->get_total_filtered_performances( $calendar_filter_params );

	$dates_sent = true;
}

$selected_month = ( isset( $calendar_filter_params['month'] ) ? $calendar_filter_params['month'] : date('m') );
$selected_year = ( isset( $calendar_filter_params['year'] ) ? $calendar_filter_params['year'] : date('Y') );

$calendar = $theatre_wp->get_calendar( $calendar_filter_params );

if ( empty( $calendar ) && $dates_sent ) {
	$msg = __( 'There are no registered performances for dates searched', 'theatrewp' );
} elseif ( empty( $calendar ) ) {
	$msg = __( 'There are no performaces right now', 'theatrewp' );
}
?>

	<div id="primary" class="site-content">
		<div id="content" role="main">
			<?php
			// Date selection form
			include( 'check-dates-form.php' );
			?>
			<div class="entry-content">
				<?php
				$current_loop_month = false;
				$current_loop_year = false;

				if ( ! empty( $calendar ) ) { ?>
					<p>
						<?php
						printf( __( '%s Total performances', 'theatrewp' ), $total_performances );

						if ( $dates_sent ) { ?>
							<a href="<?php $action_form; ?>"><?php echo __( 'View upcoming performances', 'theatrewp' ); ?></a>
					<?php }	?>
					</p>
					<?php
					foreach ( $calendar as $post ) {
						setup_postdata( $post ); ?>
						<div class="performances" id="performance-<?php the_ID(); ?>">
							<?php
							$performance_custom = $theatre_wp->get_performance_custom( get_the_ID() );
							$last_month = $current_loop_month;
							$last_year = $current_loop_year;
							$current_loop_month = date( 'F', $performance_custom['date_first'] );
							$current_loop_year = date( 'Y', $performance_custom['date_first'] );
							$spectacle_data = $theatre_wp->get_spectacle_data( sanitize_title( $performance_custom['performance'] ) );

							if ( ! $last_month OR $last_month != $current_loop_month ) {
							?>
								<div class="month">
									<?php
									if ( ! $last_year OR $last_year != $current_loop_year ) { ?>
										<h2 class="current-year">
											<?php echo $current_loop_year . ' '; ?>
										</h2>
									<?php } ?>
									<h3 class="current-month">
										<?php echo $current_loop_month; ?>
									</h3>
								</div>
							<?php } ?>

							<div class="performance">
								<?php the_title( '<h2 class="entry-title"><a href="' . get_permalink( $post->ID ) . '" title="' . the_title_attribute( 'echo=0' ) . '" rel="bookmark">', '</a></h2>' ); ?>
								<?php
								if ( has_post_thumbnail( $spectacle_data['id'] ) ) {
									echo $spectacle_data['thumbnail'];
								}
								?>
								<h4><a href="<?php echo $performance_custom['spectacle_url']; ?>"><?php echo $performance_custom['spectacle_title']; ?></a> </h4>

								<?php // Get dates
								$performance_first_date = date_i18n( get_option( 'date_format' ), $performance_custom['date_first'] );
								$first_time = strftime( '%H:%M', $performance_custom['date_first'] );
								$last_time = false;

								if ( $performance_custom['date_last'] ) {
									$performance_last_date = date_i18n( get_option( 'date_format' ), $performance_custom['date_last'] );
									$last_time = strftime( '%H:%M', $performance_custom['date_last'] );

									if ( $first_time == $last_time) {
										$performance_time = sprintf( _x( 'From %s to %s (%s)', 'From day one to day two at (hour)', 'theatrewp' ), $performance_first_date, $performance_last_date, $first_time );
									} else {
										$performance_time = sprintf( _x( 'From %s (%s) to %s (%s)', 'From day one at (hour) to day two at (hour)', 'theatrewp' ), $performance_first_date, $first_time, $performance_last_date, $last_time );
									}
								} else {
									$performance_time = sprintf( '%s (%s)',  $performance_first_date, $first_time );
								}
								?>

								<p class="date">
									<?php echo $performance_time; ?>
								</p>

								<?php if ( $performance_custom['place'] ) { ?>
									<p class="location">
										<?php echo $performance_custom['place'] . ' (' . $performance_custom['town'] . ')'; ?>
									</p>
								<?php } ?>
							</div>

						</div>

					<?php } // End foreach loop;
				} // If calendar not empty
				?>

			<?php if ( isset( $msg ) ) { ?> <h3><?php echo $msg;?></h3> <?php } ?>

			<div class="nav-previous alignleft"><?php next_posts_link( __( 'Upcoming Performances', 'theatrewp' ) ); ?></div>
			<div class="nav-next alignright"><?php previous_posts_link( __( 'Previous Performances', 'theatrewp') ); ?></div>

			</div> <?php // entry-content ?>
		</div>
	</div>

<?php get_sidebar(); ?>
<?php get_footer(); ?>
