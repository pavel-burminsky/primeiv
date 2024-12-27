<?php

namespace WP_Business_Reviews\Includes\Blocks\Collection;

/**
 * WPBR - Render Collection Block
 *
 * @since 1.6.0
 */
class Block
{
    public function register()
    {
        register_block_type(
            'wpbr/collection',
            [
                'render_callback' => function ($args) {
                    $args = wp_parse_args( $args, ['id' => 0] );

                    if ($args[ 'id' ]) {
                        return do_shortcode(
                            sprintf('[wpbr_collection id="%d"]', $args[ 'id' ])
                        );
                    }

                    return null;
                },
                'attributes'      => [
                    'id' => [
                        'type' => 'number'
                    ],
                ],
            ]
        );
    }
}
