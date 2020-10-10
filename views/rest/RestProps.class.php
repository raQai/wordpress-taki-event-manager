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

/**
 * Defines a class holding all necessary properties to register a rest route.
 *
 * The render callback should be handled by the corresponding views.
 *
 * @since      1.0.0
 *
 * @package    BIWS\TaKiEventManager\views
 * @subpackage rest
 * 
 * @link https://developer.wordpress.org/reference/functions/register_rest_route/
 */
class RestProps
{
    /**
     * @since 1.0.0
     * @access private
     * 
     * @var string $namespace The first URL segment after core prefix.
     */
    private string $namespace;

    /**
     * @since 1.0.0
     * @access private
     * 
     * @var string $route The base URL for the route.
     */
    private string $route;

    /**
     * @since 1.0.0
     * @access private
     * 
     * @var string $args The options for the rest route.
     */
    private array $args;

    /**
     * @since 1.0.0
     * @access private
     * 
     * @var string $override Determines whether to override existing routes.
     */
    private bool $override;

    /**
     * Constructs this object with the given paramaters as properties
     * 
     * @since 1.0.0
     * 
     * @param string $namespace The first URL segment after core prefix.
     * @param string $route The base URL for the route.
     * @param array $args The options for the rest route.
     * @param bool $override Determines whether to override existing routes.
     */
    public function __construct(
        string $namespace,
        string $route,
        array $args = array(),
        bool $override = false
    ) {
        $this->namespace = $this->sanitize($namespace);
        $this->route = $this->sanitize($route);
        $this->args = $args;
        $this->override = $override;
    }

    /**
     * @since 1.0.0
     * 
     * @see self::$namespace
     */
    public function getNamespace(): string
    {
        return $this->namespace;
    }

    /**
     * @since 1.0.0
     * 
     * @see self::$route
     */
    public function getRoute(): string
    {
        return $this->route;
    }

    /**
     * Helps generating the endpoint url part.
     * 
     * @since 1.0.0
     * 
     * @return string The rest endpoint url path as namespace/route
     */
    public function getEndpoint(): string
    {
        return "{$this->namespace}/{$this->route}";
    }

    /**
     * @since 1.0.0
     * 
     * @see self::$args
     */
    public function getArgs(): array
    {
        return $this->args;
    }

    /**
     * @since 1.0.0
     * 
     * @see self::$override
     */
    public function isOverride(): bool
    {
        return $this->override;
    }

    /**
     * Sanitizes the given value by trimming it and replacing back slashes with
     * forward slashes.
     * 
     * @since 1.0.0
     * @access private
     * 
     * @param string $value The value to be sanitized.
     * 
     * @return string The sanitized value.
     */
    private function sanitize(string $value): string
    {
        return trim(str_replace('\\', '/', $value), '/');
    }
}
