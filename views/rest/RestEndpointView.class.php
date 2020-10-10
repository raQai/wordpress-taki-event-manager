<?php

/**
 * Copyright Patrick Bogdan. All rights reserved.
 * See LICENSE.txt for license details.
 *
 * @author     Patrick Bogdan
 * @copyright  2020 Patrick Bogdan
 * @license    https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License, version 3 or later
 */

namespace BIWS\TaKiEventManager\views\rest;

use BIWS\CPTBuilder\views\IView;
use BIWS\CPTBuilder\views\IViewController;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

/**
 * Rest endpoint view implementation
 *
 * @since      1.0.0
 *
 * @package    BIWS\TaKiEventManager\views
 * @subpackage rest
 */
class RestEndpointView implements IView
{
    /**
     * The controller managing this view. 
     *
     * @since 1.0.0
     * @access private
     *
     * @var AbstractRestEndpointViewController $controller The controller for
     *                                                     this view.
     */
    private AbstractRestEndpointViewController $controller;

    /**
     * @since 1.0.0
     *
     * @param AbstractRestEndpointViewController $controller The controller for
     *                                                       this view.
     */
    public function __construct(AbstractRestEndpointViewController $controller)
    {
        $this->controller = $controller;
    }

    /**
     * @since 1.0.0
     * 
     * @see IView::getController()
     */
    public function getController(): IViewController
    {
        return $this->controller;
    }

    /**
     * @since 1.0.0
     * 
     * @see IView::init()
     * @see add_action()
     * 
     * @link https://developer.wordpress.org/reference/hooks/rest_api_init/
     */
    public function init(): void
    {
        add_action('rest_api_init', array($this, 'register'));
    }

    /**
     * @since 1.0.0
     * 
     * @see IView::remove()
     * @see remove_action()
     */
    public function remove(): void
    {
        remove_action('rest_api_init', array($this, 'register'));
    }

    /**
     * Registers this rest api view
     * 
     * @since 1.0.0
     * 
     * @see AbstractRestEndpointViewController::registerWithCallback()
     * 
     * @param WP_REST_Server $wp_rest_server unused
     * 
     * @link https://developer.wordpress.org/rest-api/extending-the-rest-api/adding-custom-endpoints/
     */
    public function register(WP_REST_Server $wp_rest_server): void
    {
        $this->controller->registerWithCallback(array($this, 'restCallback'));
    }

    /**
     * Returns the REST response for the frontend
     * 
     * @since 1.0.0
     * 
     * @see AbstractRestEndpointViewController::getRestResponse()
     *
     * @param WP_REST_Request $request The passed in request data to process
     *                        the callback
     * 
     * @return WP_REST_Response|WP_Error The rest response to render or
     *                                   WP_Error if an error occurred.
     */
    public function restCallback(WP_REST_Request $request)
    {
        return $this->controller->getRestResponse($request);
    }
}
