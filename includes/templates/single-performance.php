<?php
/**
 * The Template for displaying single Performance.
 *
 * @package WordPress
 * @subpackage TheatreWordPress
 * @since TheatreWordPress 0.1
 */

global $twp_prefix;
get_header(); ?>

	<div id="primary" class="site-content">
		<div id="content" role="main">

			<?php while ( have_posts() ) : the_post(); ?>
				<?php
				$performance_custom = $theatre_wp->get_performance_custom( get_the_ID() );

				if ( $performance_custom['date_first'] ) {
					$performance_first_date = date_i18n( get_option( 'date_format' ), $performance_custom['date_first'] );
					$performance_first_time = strftime( '%H:%M', $performance_custom['date_first'] );
				}

				if ( $performance_custom['date_last'] ) {
					$performance_last_date = date_i18n( get_option( 'date_format' ), $performance_custom['date_last'] );
					$performance_last_time = strftime( '%H:%M', $performance_custom['date_last'] );
				}
				?>

				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

					<header class="entry-header">
						<h1 class="entry-title"><?php the_title(); ?></h1>
					</header>

					<div class="entry-content">
						<?php
						if ( isset( $performance_first_date ) ) { ?>
							<h3><?php echo __( 'When', 'theatre-wp' ); ?></h3>
							<p class="single-performance-dates">
								<?php
								if ( isset( $performance_last_date ) ) {
									echo _x( 'From', '(date) performing from day', 'theatre-wp' ) . ' ';
								}
								?>
								<span class="performance-date"><?php echo $performance_first_date; ?></span>
								(<span class="performance-time"><?php echo $performance_first_time;?></span>)
								<?php
								if ( isset( $performance_last_date ) ) {
									echo _x( 'To', '(date) performing to day', 'theatre-wp' ) . ' ';
								?>
									<span class="performance-date"><?php echo $performance_last_date; ?></span>
									(<span class="performance-time"><?php echo $performance_last_time;?></span>)<br>
								<?php
								}
								?>
							</p>
						<?php } ?>

						<?php if ( $performance_custom['spectacle_title'] ) { ?>
							<h3><?php echo __( 'Show', 'theatre-wp' ); ?></h3>
							<div class="show">
								<?php
								$production_custom = $theatre_wp->get_spectacle_data( $performance_custom['spectacle_id'] );

								if ( has_post_thumbnail( $production_custom['id'] ) ) { ?>
									<a href="<?php echo $performance_custom['spectacle_url']; ?>"><?php echo $production_custom['thumbnail']; ?></a>
								<?php
								}
								?>

								<p>
									<strong><a href="<?php echo $performance_custom['spectacle_url']; ?>"><?php echo $performance_custom['spectacle_title']; ?></a> </strong>
								</p>
							</div>
						<?php } ?>

						<?php if ( $performance_custom['event'] ) { ?>
							<h3><?php echo __( 'Event', 'theatre-wp' ); ?></h3>
							<div class="event">
								<?php echo $performance_custom['event']; ?>
							</div>
						<?php } ?>

						<?php if ( $performance_custom['display_map'] ) { ?>
							<div id="performance-map">
								<?php $map = $theatre_wp->display_performance_map( $performance_custom );
								echo $map;
								?>
							</div>
						<?php }	?>

						<?php wp_link_pages( array( 'before' => '<div class="page-links">' . __( 'Pages:', 'twentytwelve' ), 'after' => '</div>' ) ); ?>
					</div><!-- .entry-content -->

				</article>

			<?php endwhile; // end of the loop. ?>

		</div><!-- #content -->
	</div><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
