<?php

namespace WP_Business_Reviews\Includes\Routes;

use WP_Business_Reviews\Includes\Deserializer\Review_Deserializer;
use WP_REST_Request;
use WP_REST_Response;
use WP_Query;

/**
 * @since 1.6.0
 */
class Get_Review_Route extends Endpoint
{
    /**
     * @var string
     */
    protected $endpoint = 'get-review';

    public function registerRoute()
    {
        register_rest_route(
            parent::ROUTE_NAMESPACE,
            $this->endpoint,
            [
                [
                    'methods'             => 'GET',
                    'callback'            => [$this, 'handleRequest'],
                    'permission_callback' => [$this, 'permissionsCheck'],
                    'args'                => [
                        'id' => [
                            'required'          => true,
                            'type'              => 'integer',
                            'description'       => esc_html__('Review ID', 'wp-business-reviews'),
                            'validate_callback' => function ($param) {
                                return filter_var($param, FILTER_VALIDATE_INT);
                            },
                        ],
                    ],
                ]
            ]
        );
    }

    public function handleRequest(WP_REST_Request $request)
    {
        $review_deserializer = new Review_Deserializer(new WP_Query());

        if ( ! $review = $review_deserializer->get_review($request->get_param('id'))) {
            return new WP_REST_Response(null, 404);
        }

        return new WP_REST_Response([
            'data' => [
                'review'   => $review,
                'settings' => [
                    'post_parent'       => 0,
                    'style'             => 'light',
                    'format'            => 'review_gallery',
                    'max_columns'       => 1,
                    'max_characters'    => 280,
                    'line_breaks'       => 'disabled',
                    'review_components' => [
                        'reviewer_image' => 'enabled',
                        'reviewer_name'  => 'enabled',
                        'rating'         => 'enabled',
                        'recommendation' => 'enabled',
                        'timestamp'      => 'enabled',
                        'content'        => 'enabled',
                        'platform_icon'  => 'enabled',
                    ],
                ],
            ],
        ]);
    }
}
