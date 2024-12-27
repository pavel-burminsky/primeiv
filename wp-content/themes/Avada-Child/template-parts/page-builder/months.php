<?php
$button_title = get_sub_field( 'button_title' );
$button_url = get_sub_field( 'button_url' );
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
        <div class="<?php printf( '%s__items', $args['class_root'] ); ?>">
            <?php
            $i = 1;
            if ( have_rows( 'items' ) ) {
                while ( have_rows( 'items' ) ) {
                    the_row();
                    ?>
                    <div class="<?php printf( '%s__item', $args['class_root'] ); ?>">

                        <div class="<?php printf( '%s__item__decorative', $args['class_root'] ) ?>">
                            <span class="<?php printf( '%s__item__decorative__month', $args['class_root'] ) ?>"><?php _e( 'month', 'primeiv' ) ?></span>
                            <span class="<?php printf( '%s__item__decorative__big-number', $args['class_root'] ) ?>"><?php echo $i; ?></span>
                        </div>
                        <div class="<?php printf( '%s__item__body', $args['class_root'] ) ?>">
                            <div>
                                <?php
                                if ( $body = get_sub_field( 'body' ) ) {
                                    echo $body;
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                    <?php
                    $i++;
                }
            }
            ?>
        </div>
        <?php
        if (
            $button_title
            && $button_url ) {
            primeiv_print_button( $button_title, $button_url );
        } ?>
    </div>
</div>
