<?php
/**
 * The Template for displaying list of performances.
 *
 * @package WordPress
 * @subpackage TheatreWordPress
 * @since TheatreWordPress 0.1
 */

get_header(); ?>

	<div id="primary" class="site-content">
		<div id="content" role="main">
			<?php
			// @TODO $topdate to config
			$topdate = time()-72800;
			$paged = get_query_var('paged') ? intval( get_query_var('paged') ) : 1;

			$loop = new WP_Query( array(
				'post_type' => 'performance', 'meta_key' => Theatre_WP::$twp_prefix . 'date_first', 'orderby' => 'meta_value', 'meta_compare' => '>=',
				'meta_value' => $topdate, 'order' => 'ASC', 'paged' => $paged, 'posts_per_page' => 10
				)
			);

			if ( ! $loop->have_posts() ) {
				$msg = __('There are no performaces right now', 'theatrewp');
			}
			?>
			<div class="entry-content">
			<?php
			$current_loop_month = false;
			$current_loop_year = false;
			while ( $loop->have_posts() ) : $loop->the_post(); ?>
				<div class="performances" id="performance-<?php the_ID(); ?>">
					<?php
					$performance_custom = $theatre_wp->get_performance_custom( get_the_ID() );
					$last_month = $current_loop_month;
					$last_year = $current_loop_year;
					$current_loop_month = date('F', $performance_custom['date_first']);
					$current_loop_year = date('Y', $performance_custom['date_first']);

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
						<?php the_title( '<h2 class="entry-title"><a href="' . get_permalink() . '" title="' . the_title_attribute( 'echo=0' ) . '" rel="bookmark">', '</a></h2>' ); ?>
						<h4><a href="<?php echo $performance_custom['link']; ?>"><?php echo $performance_custom['title']; ?></a> </h4>

						<?php // Get dates
						$first_time = strftime('%H:%M', $performance_custom['date_first']);
						$last_time = false;

						if ( $performance_custom['date_last'] ) {
							$last_time = strftime('%H:%M', $performance_custom['date_last']);

							$performance_time = 'From ' . strftime("%A %B %d, %Y", $performance_custom['date_first'])
							. ' to ' . strftime("%A %B %d, %Y", $performance_custom['date_last']);

							if ( $first_time == $last_time) {
								$performance_time .= '<br>' . strftime("%H:%M h.", $performance_custom['date_first']);
							}
						} else {
							$performance_time = strftime("%A %B %d, %Y %H:%M h.", $performance_custom['date_first']);
							$performance_time = ucfirst($performance_time);
						}
						?>
						<p class="date">
							<?php echo $performance_time; ?>
						</p>
						<p class="location">
							<?php echo $performance_custom['place'] . ' (' . $performance_custom['town'] . ')'; ?>
					</div>

				</div>

			<?php endwhile; ?>
			</div> <?php // entry-content ?>
			<?php if ( isset( $msg ) ) { echo $msg; } ?>
		</div>
	</div>

<?php get_sidebar(); ?>
<?php get_footer(); ?>
