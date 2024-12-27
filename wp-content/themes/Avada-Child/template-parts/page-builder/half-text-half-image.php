<?php

$body = get_sub_field('body');

$image_id = get_sub_field('image');
$img_html = wp_get_attachment_image($image_id);

$content = [];

$button_title = get_sub_field('button_title');
$button_url = get_sub_field('button_url');

?>
<div class="<?php echo $args['class_root']; ?>">
    <div class="container">
        <?php
        if ( $title = get_sub_field( 'title' ) ) {
            get_template_part( 'template-parts/page-builder/section', 'title', [ 'title' => $title ] );
        }
        if ( $sub_title = get_sub_field( 'sub_title' ) ) {
            get_template_part( 'template-parts/page-builder/section', 'subtitle', [ 'sub_title' => $sub_title ] );
        }
        ?>

        <?php
        if( have_rows( 'items' ) ) {
            ?>
            <div class="<?php printf( '%s__image-text-rows', $args['class_root'] ); ?>">
                <?php

                $key = 0;
                while ( have_rows( 'items' ) ) {
                    the_row();
                    $title = get_sub_field( 'title' );
                    $body = get_sub_field( 'body' );
                    $image_id = get_sub_field( 'image' );
                    $img_html = wp_get_attachment_image( $image_id, 'full' );

                    $is_odd = $key % 2 === 0;
                    $class = !$is_odd
                        ? 'reversed'
                        : '';

                    $image_id = $item['image'];


                    switch ( $key ) {
                        case 0:
                            $class_image_bg = 'with-border-right-dark';

                            break;
                        case 2:
                            $class_image_bg = 'with-border-left';
                            break;
                        default:
                            $class_image_bg = '';

                    }

                    $key++;
                    ?>
                    <div class="<?php printf( '%s__image-text-row %s', $args['class_root'], $class ); ?>">
                        <div class="<?php printf( '%s__image-text-row__text', $args['class_root'] ); ?>">
                            <?php if ( $title ) { ?>
                                <h3 class="<?php printf( '%s__image-text-row__text__title ', $args['class_root'] ); ?>"><?php echo $title; ?></h3>
                            <?php } ?>
                            <?php
                            if ( $body ) {
                                echo $body;
                            }
                            ?>

                        </div>
                        <div class="<?php printf( '%s__image-text-row__image image-with-border %s', $args['class_root'], $class_image_bg ); ?>">
                            <div class="col-inner">
                                <?php echo $img_html; ?>

                            </div>
                        </div>
                    </div>
                    <?php
                }
                ?>
            </div>
            <?php
        }

        if (
            $button_title
            && $button_url 
        ) {
            primeiv_print_button( $button_title, $button_url );
        }
        ?>
    </div>
</div>
