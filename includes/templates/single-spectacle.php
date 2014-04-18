<?php
/**
 * The Template for displaying single Spectacles.
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
				// Get the Production formats
				$production_format = get_the_terms( $post->ID, 'format' );

				foreach ( $production_format as $format ) {
					$formats[] = array(
						'name' => $format->name,
						'slug' => $format->slug
					);
				}

				// Production custom metadata
				$production_custom = $theatre_wp->get_spectacle_custom( get_the_ID() );
				?>

				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

					<header class="entry-header">
						<h2 class="entry-title"><?php the_title(); ?></h2>

						<p class="production-formats">
							<?php
							// Display and link the Production formats
							foreach ( $formats as $format ) {
							?>
								<a href="<?php echo $theatre_wp->get_production_cat_url( $format['slug'] ); ?>"><?php echo $format['name']; ?></a>
							<?php
							}
							?>
						</p>

						<?php the_post_thumbnail(); ?>

						<?php if ( $production_custom['audience'] ) { ?>
							<p>
								<span class="audience">
									<?php echo $production_custom['audience']; ?>
								</span>
							</p>
						<?php } ?>

						<?php if ( $production_custom['synopsis'] ) { ?>
							<p>
								<span class="synopsis">
									<?php echo $production_custom['synopsis']; ?>
								</span>
							</p>
						<?php } ?>
					</header>

					<div class="entry-content">
						<?php the_content(); ?>

						<?php if ( $production_custom['credits'] ) { ?>
							<h2><?php echo __('Credits', 'theatrewp'); ?></h2>
							<div class="credits">
								<?php echo nl2br( $production_custom['credits'] ); ?>
							</div>
						<?php } ?>

						<?php if ( $production_custom['sheet'] ) { ?>
							<h2><?php echo __('Sheet', 'theatrewp'); ?></h2>
							<div class="sheet">
								<?php echo nl2br( $production_custom['sheet'] ); ?>
							</div>
						<?php } ?>

						<?php wp_link_pages( array( 'before' => '<div class="page-links">' . __( 'Pages:', 'twentytwelve' ), 'after' => '</div>' ) ); ?>
					</div><!-- .entry-content -->

				</article>

				<nav class="nav-single">
					<h3 class="assistive-text"><?php _e( 'Post navigation', 'twentytwelve' ); ?></h3>
					<span class="nav-previous"><?php previous_post_link( '%link', '<span class="meta-nav">' . _x( '&larr;', 'Previous post link', 'twentytwelve' ) . '</span> %title' ); ?></span>
					<span class="nav-next"><?php next_post_link( '%link', '%title <span class="meta-nav">' . _x( '&rarr;', 'Next post link', 'twentytwelve' ) . '</span>' ); ?></span>
				</nav><!-- .nav-single -->

			<?php endwhile; // end of the loop. ?>

		</div><!-- #content -->
	</div><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
