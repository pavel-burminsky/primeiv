<?php

namespace WP_Business_Reviews\Includes\Routes;

use WP_Business_Reviews\Includes\Deserializer\Collection_Deserializer;
use WP_Business_Reviews\Includes\Deserializer\Review_Deserializer;
use WP_Business_Reviews\Includes\Deserializer\Review_Source_Deserializer;
use WP_REST_Request;
use WP_REST_Response;
use WP_Query;

/**
 * @since 1.6.0
 */
class Get_Collection_Route extends Endpoint
{
    /**
     * @var string
     */
    protected $endpoint = 'get-collection';

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
                            'description'       => esc_html__('Collection ID', 'wp-business-reviews'),
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
        $wpQuery                    = new WP_Query();
        $review_deserializer        = new Review_Deserializer($wpQuery);
        $review_source_deserializer = new Review_Source_Deserializer($wpQuery);
        $collection_deserializer    = new Collection_Deserializer(
            $wpQuery,
            $review_source_deserializer,
            $review_deserializer
        );

        if ( ! $collection = $collection_deserializer->get_collection($request->get_param('id'))) {
            return new WP_REST_Response(null, 404);
        }

        $collection = $collection_deserializer->hydrate_reviews($collection);

        return new WP_REST_Response([
            'data' => [
                'settings' => $collection->get_settings(),
                'reviews'  => $collection->get_reviews(),
            ],
        ]);
    }
}
