<?php
$button_title = get_sub_field('button_title');
$button_url = get_sub_field('button_url');

$last_item_title = get_sub_field('last_item_title');
$last_item_body = get_sub_field('last_item_body');
$last_item_button_title = get_sub_field('last_item_button_title');
$last_item_button_url = get_sub_field('last_item_button_url');

$has_any_last_item_content = (
        $last_item_title
        || $last_item_body
    ) || (
        $last_item_button_title
        && $last_item_button_url
    );
?>
<div class="<?php echo $args['class_root']; ?>">
    
    <?php
    if( $title = get_sub_field('title') ) {
        get_template_part('template-parts/page-builder/section' , 'title', ['title' => $title] );
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
                    if ( $body = get_sub_field( 'body' ) ) {
                        ?>
                        <div class="<?php printf( '%s__item__body', $args['class_root'] ); ?>">
                            <?php echo $body; ?>
                        </div>
                        <?php
                    }
                    if ( $author = get_sub_field( 'author' ) ) {
                        ?>
                        <p class="<?php printf( '%s__item__author', $args['class_root'] ); ?>"><?php echo $author; ?></p>
                        <?php
                    }
                    ?>
                </div>
                <?php
            }
        }
        if ($has_any_last_item_content) {
            ?>
            <div class="<?php printf( '%s__item last-blue', $args['class_root'] ); ?>">
                <?php if( $last_item_title ) {
                    ?>
                    <h3 class="last-blue__title"><?php echo $last_item_title; ?></h3>
                    <?php
                }?>
                <?php if( $last_item_body ){
                    ?>
                    <p class="last-blue__body"><?php echo $last_item_body; ?></p>
                    <?php
                }?>
                <?php 
                if( $last_item_button_title
                    && $last_item_button_url ) {
                    primeiv_print_button( $last_item_button_title, $last_item_button_url, false );
                }
                ?>
            </div>
        <?php
        }
        ?>
    </div>
    
    
</div>
