<?php
/**
 * The Template for displaying list of spectacles.
 *
 * @package WordPress
 * @subpackage TheatreWordPress
 * @since TheatreWordPress 0.1
 */

get_header(); ?>

	<div id="primary" class="site-content">
		<div id="content" role="main">
			<?php
			$spectacles_per_page = get_option( 'twp_spectacles_number' );

			$paged = get_query_var('paged') ? intval( get_query_var('paged') ) : 1;

			$args = array(
				'post_type'      => 'spectacle',
				'paged'          => $paged,
				'posts_per_page' => $spectacles_per_page
			    );

			query_posts( $args );
			?>
			<div class="entry-content">
			<?php
			while ( have_posts() ) : the_post(); ?>
				<article class="spectacles" id="spectacle-<?php the_ID(); ?>">
					<?php
					$spectacle_custom = $theatre_wp->get_spectacle_custom( get_the_ID() );
					?>
					<div class="spectacle">
						<?php the_title( '<h2 class="entry-title"><a href="' . get_permalink() . '" title="' . the_title_attribute( 'echo=0' ) . '" rel="bookmark">', '</a></h2>' ); ?>

							<?php if ( function_exists( 'has_post_thumbnail' ) && has_post_thumbnail() ) { ?>
								<div class="spectacle-image">
									<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
										<?php the_post_thumbnail( 'medium', array( "class" => "post_thumbnail" ) ); ?>
									</a>
								</div>
							<?php } ?>
							<?php if ( isset( $spectacle_custom['audience'] ) ) { ?>
								<p class="audience">
									<em><?php _e( 'Audience:', 'theatrewp' ); ?></em>
									<?php echo $spectacle_custom['audience']; ?>
								</p>
							<?php } ?>
							<div class="spectacle-description">
								<?php
								if ( isset( $spectacle_custom['synopsis'] ) ) { ?>
									<p class="synopsis">
										<?php echo $spectacle_custom['synopsis']; ?>
									</p>
								<?php
								}
								?>
							</div>
						<!--</div>-->
					</div>
				</article> <!-- spectacles -->

			<?php endwhile; ?>

			<div class="nav-previous alignleft"><?php next_posts_link( __( 'More Spectacles', 'theatrewp' ) ); ?></div>
			<div class="nav-next alignright"><?php previous_posts_link( __( 'Previous Spectacles', 'theatrewp') ); ?></div>

			</div> <!-- entry-content -->
		</div>
	</div>

<?php get_sidebar(); ?>
<?php get_footer(); ?>
