<?php
/**
 * The Template for displaying single Performance.
 *
 * @package WordPress
 * @subpackage TheatreWordPress
 * @since TheatreWordPress 0.1
 */

get_header(); ?>

	<div id="primary" class="site-content">
		<div id="content" role="main">

			<?php while ( have_posts() ) : the_post(); ?>
				<?php
				$performance_custom = $theatre_wp->get_performance_custom( get_the_ID() );

                $dates = $theatre_wp->get_performance_dates( $performance_custom );
				?>

				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

					<header class="entry-header">
						<h1 class="entry-title"><?php the_title(); ?></h1>
					</header>

					<div class="entry-content">
						<?php echo $dates; ?>

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
