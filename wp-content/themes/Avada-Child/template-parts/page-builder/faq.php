<div class="<?php echo $args['class_root']; ?>">
    
    <?php
    if( $title = get_sub_field('title') ) {
        get_template_part('template-parts/page-builder/section' , 'title', ['title' => $title] );
    }
    if( $sub_title = get_sub_field('sub_title') ) {
        get_template_part('template-parts/page-builder/section' , 'subtitle', ['sub_title' => $sub_title] );
    }
    // todo - old code is used - it requires refactor
    ?>
    <div class="<?php printf('%s__items', $args['class_root']) ; ?>">
        <div class="faq-wrapper faq-section">
            <div class="faq-content">
                <div class="questions">
                    <?php
                    if ( have_rows( 'items' ) ) {
                        while ( have_rows( 'items' ) ) {
                            the_row();
                            $title = get_sub_field( 'title' );
                            $body = get_sub_field( 'body' );
                            if (
                                $title
                                && $body
                            ) { ?>
                                <div class="question-item">
                                    <div class="question-header">
                                        <div class="question-title"><?php echo $title ?></div>
                                        <div class="question-icon"><i class="fa prime-faq"></i></div>
                                    </div>
                                    <div class="question-content">
                                        <?php echo $body ?>
                                    </div>
                                </div>
                                <?php
                            }
                            ?>
                            <?php
                        }
                    }
                    ?>
                </div>
            </div>
            </div>
        </div>
</div>
