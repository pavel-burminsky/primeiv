<?php
$button_title = get_sub_field('button_title');
$button_url = get_sub_field('button_url');
?>
<div class="<?php echo $args['class_root']; ?>">
    
    <?php
    if( $title = get_sub_field('title') ) {
        get_template_part('template-parts/page-builder/section' , 'title', ['title' => $title] );
    }
    if( $sub_title = get_sub_field('sub_title') ) {
        get_template_part('template-parts/page-builder/section' , 'subtitle', ['sub_title' => $sub_title] );
    }

    ?>
    <div class="<?php printf('%s__items', $args['class_root']) ; ?>">
        <?php
        if( have_rows( 'items' ) ){
            while (have_rows('items')) {
                the_row();
                ?>
                <div class="<?php printf( '%s__item', $args['class_root'] ); ?>">
                    <?php
                    if ( $title = get_sub_field( 'title' ) ) {
                        ?>
                        <h3 class="<?php printf( '%s__item__title', $args['class_root'] ); ?>"><?php echo $title; ?></h3>
                        <?php
                    }
                    if ( $body = get_sub_field( 'body' ) ) {
                        echo $body;
                    }
                    ?>
                </div>
                <?php
            }
        }
        ?>
    </div>
    <?php
    if (
        $button_title
        && $button_url ) {
        primeiv_print_button( $button_title, $button_url );
    }
    ?>
</div>
