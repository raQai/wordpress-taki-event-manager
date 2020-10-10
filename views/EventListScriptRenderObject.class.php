<?php

/**
 * Copyright Patrick Bogdan. All rights reserved.
 * See LICENSE.txt for license details.
 * 
 * @author     Patrick Bogdan
 * @copyright  2020 Patrick Bogdan
 * @license    https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License, version 3 or later
 */

namespace BIWS\TaKiEventManager\views;

use BIWS\CPTBuilder\views\RenderObject;

/**
 * Event list script render object implementation
 *
 * Extends the RenderObject by adding the necessary values
 *
 * @since      1.0.0
 *
 * @see RenderObject
 *
 * @package    BIWS\TaKiEventManager
 * @subpackage views
 */
class EventListScriptRenderObject extends RenderObject
{
    /**
     * The rest query parameters.
     * 
     * @since 1.0.0
     * @access private
     * 
     * @var string[] Parameters as key => value pairs.
     */
    private array $params = array();

    /**
     * The filters.
     * 
     * @since 1.0.0
     * @access private
     * 
     * @var string[] Filters as encoded json strings.
     */
    private array $filters = array();

    /**
     * Constructs the EventListScriptRenderObject and deserializes the given
     * shortcode $atts to obtain the corresponding values for the script.
     * 
     * @since 1.0.0
     * 
     * @param string[] $params   The params for the script.
     * @param string[] $filters  The filters for the script.
     * @param string   $template The script template path.
     * 
     * @link https://developer.wordpress.org/reference/functions/add_shortcode/
     */
    public function __construct(array $params, array $filters, string $template)
    {
        parent::__construct($template);
        $this->params = $params;
        $this->filters = $filters;
    }

    /**
     * @since 1.0.0
     * 
     * @see self::$params
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * @since 1.0.0
     * 
     * @see self::$filters
     */
    public function getFilters(): array
    {
        return $this->filters;
    }
}
