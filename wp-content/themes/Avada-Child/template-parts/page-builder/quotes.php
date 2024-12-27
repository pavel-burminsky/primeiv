<div class="<?php echo $args['class_root']; ?>">

    <div class="<?php printf('%s__items', $args['class_root']) ; ?>">
        <?php
        if( have_rows( 'items' ) ){
            while (have_rows('items')) {
                the_row();

                $title = get_sub_field( 'title' );

                $image_id = get_sub_field('image');
                $img_html = $image_id 
                    ? wp_get_attachment_image( $image_id, 'full', false, [ 'class' => sprintf( '%s__item__image', $args['class_root'] ) ] )
                    : '';
                ?>
                <div class="<?php printf( '%s__item', $args['class_root'] ); ?>">
                    <span class="<?php printf( '%s__big-quote', $args['class_root'] ); ?>">“</span>
                    <?php
                    ?>
                    <h3 class="<?php printf( '%s__item__title', $args['class_root'] ); ?>"><?php echo $title; ?>“</h3>
                    <?php
                    echo $img_html;
                    ?>
                </div>

                <?php
            }
        }

        ?>
    </div>
</div>
