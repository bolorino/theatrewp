<?php
/**
 * The Template for displaying Productions Categories.
 *
 * @package WordPress
 * @subpackage TheatreWordPress
 * @since TheatreWordPress 0.40
 */

get_header(); ?>
    <div id="primary" class="site-content">
        <div id="content" role="main">
            <?php
            $productions_per_page = get_option( 'twp_spectacles_number' );
            $term = get_query_var( 'term' );
            $term_properties = get_term_by( 'slug', $term, 'format' );
            $paged = get_query_var('paged') ? intval( get_query_var('paged') ) : 1;

            $args = array(
                'post_type'     => 'spectacle',
                'format'        => $term,
                'paged'         => $paged,
                'posts_per_page'=> $productions_per_page
                );

            query_posts( $args );
            ?>

            <h2 class="taxonomy-title"><?php echo $term_properties->name; ?></h2>

            <?php
            while ( have_posts() ) : the_post(); ?>
                <div class="spectacles" id="spectacle-<?php the_ID(); ?>">
                    <?php
                    $production_custom = $theatre_wp->get_spectacle_custom( get_the_ID() );
                    ?>
                    <div class="spectacle">
                        <?php the_title( '<h2 class="entry-title"><a href="' . get_permalink() . '" title="' . the_title_attribute( 'echo=0' ) . '" rel="bookmark">', '</a></h2>' ); ?>

                            <?php if ( function_exists('has_post_thumbnail') && has_post_thumbnail() ) { ?>
                                <div class="spectacle-image">
                                    <a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
                                        <?php the_post_thumbnail( 'post-thumb', array( "class" => "post_thumbnail" ) ); ?>
                                    </a>
                                </div>
                            <?php } ?>

                            <div class="spectacle-description">
                                <?php
                                if ( isset( $production_custom['synopsis'] ) && ! empty( $production_custom['synopsis'] ) ) { ?>
                                    <p class="synopsis">
                                        <?php echo $production_custom['synopsis']; ?>
                                    </p>
                                <?php
                                }
                                ?>
                            </div>
                    </div>
                </div> <!-- spectacles -->

            <?php endwhile; ?>
        </div><!-- content -->

    </div>
<?php get_sidebar(); ?>
<?php get_footer(); ?>
