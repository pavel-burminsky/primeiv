<?php

namespace WP_Business_Reviews\Includes\Routes;

use WP_Error;

/**
 * @since 1.6.0
 */
abstract class Endpoint
{
    /**
     * Route namespace
     */
    const ROUTE_NAMESPACE = 'wpbr/v1';

    /**
     * @var string
     */
    protected $endpoint;


    abstract public function registerRoute();

    /**
     * Check user permissions
     * @return bool|WP_Error
     */
    public function permissionsCheck()
    {
        if ( ! current_user_can('edit_posts')) {
            return new WP_Error(
                'rest_forbidden',
                esc_html__('You dont have the right permissions', 'wp-business-reviews'),
                ['status' => $this->authorizationStatusCode()]
            );
        }

        return true;
    }

    // Sets up the proper HTTP status code for authorization.
    public function authorizationStatusCode()
    {
        if (is_user_logged_in()) {
            return 403;
        }

        return 401;
    }
}
