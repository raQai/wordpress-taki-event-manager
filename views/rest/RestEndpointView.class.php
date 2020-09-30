<?php

namespace BIWS\TaKiEventManager\views\rest;

use BIWS\CPTBuilder\views\IView;
use BIWS\CPTBuilder\views\IViewController;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

class RestEndpointView implements IView
{
    private AbstractRestEndpointViewController $controller;

    public function __construct(AbstractRestEndpointViewController $controller)
    {
        $this->controller = $controller;
    }

    public function getController(): IViewController
    {
        return $this->controller;
    }

    public function init(): void
    {
        add_action('rest_api_init', array($this, 'register'));
    }

    public function remove(): void
    {
        remove_action('rest_api_init', array($this, 'register'));
    }

    /**
     * Registers this rest api view
     * 
     * @param WP_REST_Server $wp_rest_server unused
     * 
     * @see https://developer.wordpress.org/rest-api/extending-the-rest-api/adding-custom-endpoints/
     */
    public function register(WP_REST_Server $wp_rest_server): void
    {
        $this->controller->registerWithCallback(array($this, 'restCallback'));
    }

    /**
     * Returns the REST response for the frontend
     *
     * @param WP_REST_Request $request The passed in request data to process
     *                        the callback
     * 
     * @return WP_REST_Response|WP_Error
     * 
     * @see AbstractRestEndpointViewController::getRestResponse()
     */
    public function restCallback(WP_REST_Request $request)
    {
        return $this->controller->getRestResponse($request);
    }
}
