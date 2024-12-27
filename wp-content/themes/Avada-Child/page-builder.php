<?php
/**
 * Template Name: Custom Page Builder
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct script access denied.' );
}
get_header(); ?>
    <style>
        #main .fusion-row {
            max-width: none;
        }
    </style>
    <section id="content" class="full-width">
        <?php while ( have_posts() ) : ?>
            <?php the_post(); ?>
            <div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <div class="post-content">
                    <?php
                    if( function_exists('get_field') ) {
                        if( have_rows('page_builder') ):
                            while ( have_rows('page_builder') ) : the_row();
                                $row_layout = get_row_layout();
                                ?>
                                <div class="<?php echo primeiv_get_page_builder_section_class($row_layout); ?>">
                                    <?php
                                    $page_builder_element_slug = str_replace('_', '-', $row_layout);
                                    get_template_part('template-parts/page-builder/' . $page_builder_element_slug, null, ['class_root' => primeiv_get_section_root_class($page_builder_element_slug )] );
                                    ?>
                                </div>
                            <?php
                            endwhile;
                        endif;
                    }
                    ?>
                </div>
                <?php  ?>
            </div>
        <?php endwhile; ?>
    </section>
<?php get_footer(); ?>