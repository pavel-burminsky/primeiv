<?php
$above_title = get_sub_field('above_title');
$title = get_sub_field('title');
$sub_title = get_sub_field('sub_title');

$body = get_sub_field('body');

$button_title = get_sub_field('button_title');
$button_url = get_sub_field('button_url');


$image_id = get_sub_field('image');
$img_html = wp_get_attachment_image($image_id, 'full');


?>
<div class="<?php echo $args['class_root']; ?>">
    <div class="container">
        <div class="cols">
            <div class="col col__text">
                <div class="col-inner">
                    <?php if( $above_title ){ ?>
                        <p class="above-header"><?php echo $above_title ?></p>
                    <?php } ?>
                    <h2 class="section-header"><?php echo $title ?></h2>
                    <?php if( $sub_title ){ ?>
                        <p class="above-header"><?php echo $sub_title ?></p>
                    <?php }
                    ?>
                    <div class="body">
                        <?php echo $body; ?>
                    </div>
                    <?php
                    if (
                        $button_title
                        && $button_url ) {
                        primeiv_print_button( $button_title, $button_url );
                    } ?>
                </div>
            </div>
            <div class="col col__image image-with-border with-border-right">
                <div class="col-inner">
                    <?php if ( $img_html ) {
                        echo $img_html;
                    } ?>
                </div>
            </div>
        </div>
    </div>
</div>
