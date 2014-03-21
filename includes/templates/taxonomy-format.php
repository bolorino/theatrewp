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
            $term = get_query_var( 'term' );
            $term_properties = get_term_by( 'slug', $term, 'format' );
            $loop = new WP_Query( array( 'format' => $term, 'posts_per_page' => 10 ) ); ?>

            <h2 class="taxonomy-title"><?php printf( _x( '%s Spectacles', 'spectacles that belong to %s category', 'theatrewp' ), $term_properties->name ); ?></h2>

            <?php
            while ( $loop->have_posts() ) : $loop->the_post(); ?>
                <div class="spectacles" id="spectacle-<?php the_ID(); ?>">
                    <?php
                    $spectacle_custom = $theatre_wp->get_spectacle_custom( get_the_ID() );
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
                                if ( isset( $spectacle_custom['synopsis'] ) ) { ?>
                                    <p class="synopsis">
                                        <?php echo $spectacle_custom['synopsis']; ?>
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
