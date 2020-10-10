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

use BIWS\CPTBuilder\views\IViewController;
use BIWS\CPTBuilder\views\RenderObject;

/**
 * Shortcode view controller implementation
 *
 * @since      1.0.0
 *
 * @package    BIWS\TaKiEventManager
 * @subpackage views
 */
class ShortcodeViewController implements IViewController
{
    /**
     * The current event list script version.
     * 
     * @since 1.0.0
     * @access private
     *
     * @var string JS_EVENT_LIST_VERSION The script version.
     */
    private const JS_EVENT_LIST_VERSION = '1.0.1';

    /**
     * The event list script url to register and enqueue.
     *
     * @since 1.0.0
     * @access private
     * 
     * @global string BIWS_TaKiEventManager__PLUGIN_DIR_URL
     *
     * @var string JS_EVENT_LIST The event list script url.
     */
    private const JS_EVENT_LIST = BIWS_TaKiEventManager__PLUGIN_DIR_URL
        . 'public/build/bundle.js';

    /**
     * The event list style url to register and enqueue.
     *
     * @since 1.0.0
     * @access private
     * 
     * @global string BIWS_TaKiEventManager__PLUGIN_DIR_URL
     *
     * @var string CSS_EVENT_LIST The event list style url.
     */
    private const CSS_EVENT_LIST = BIWS_TaKiEventManager__PLUGIN_DIR_URL
        . 'public/build/bundle.css';

    /**
     * The event list target template path.
     *
     * @since 1.0.0
     * @access private
     * 
     * @global string BIWS_TaKiEventManager__PLUGIN_DIR_PATH
     *
     * @var string TEMPLATE_TARGET The event list target template path.
     */
    private const TEMPLATE_TARGET = BIWS_TaKiEventManager__PLUGIN_DIR_PATH
        . 'templates/Target.php';

    /**
     * The event list script template path.
     *
     * @since 1.0.0
     * @access private
     * 
     * @global string BIWS_TaKiEventManager__PLUGIN_DIR_PATH
     *
     * @var string TEMPLATE_SCRIPT The event list script template path.
     */
    private const TEMPLATE_SCRIPT = BIWS_TaKiEventManager__PLUGIN_DIR_PATH
        . 'templates/scripts/EventListScript.php';

    /**
     * @since 1.0.0
     *
     * @see IViewController::init()
     * @see add_action()
     * @see self::registerAssets()
     */
    public function init(): void
    {
        add_action('init', array($this, 'registerAssets'));
    }

    /**
     * @since 1.0.0
     *
     * @see IViewController::remove()
     * @see remove_action()
     */
    public function remove(): void
    {
        remove_action('init', array($this, 'registerAssets'));
    }

    /**
     * Registers the script and style assets necessarry to render the frontend
     * event list.
     * 
     * @since 1.0.0
     * 
     * @see wp_script_is()
     * @see wp_register_script()
     * @see wp_register_style()
     */
    public function registerAssets()
    {
        if (wp_script_is('biws-event-list-js')) {
            return;
        }
        wp_register_script(
            'biws-event-list-js',
            self::JS_EVENT_LIST,
            array(),
            self::JS_EVENT_LIST_VERSION,
            true
        );
        wp_register_style('biws-event-list-css', self::CSS_EVENT_LIST);
    }

    /**
     * Enqueues the script and style assets to render the event list in the
     * frontend.
     * 
     * @since 1.0.0
     * 
     * @see wp_enqueue_script()
     * @see wp_enqueue_style()
     */
    public function enqueueAssets(): void
    {
        wp_enqueue_script('biws-event-list-js');
        wp_enqueue_style('biws-event-list-css');
    }

    /**
     * Returns the render object to render the event script in the frontend.
     * 
     * @since 1.0.0
     * 
     * @see self::deserializeAtts()
     * @see self::TEMPLATE_SCRIPT
     * 
     * @param string|array $atts The shortcode attributes to be deserialized for
     *                           the script as params and filters.
     * 
     * @return EventListScriptRenderObject The script render object.
     */
    public function getScriptRenderObject($atts): EventListScriptRenderObject
    {
        $deserialized_atts = $this->deserializeAtts($atts);
        return new EventListScriptRenderObject(
            $deserialized_atts['params'],
            $deserialized_atts['filters'],
            self::TEMPLATE_SCRIPT
        );
    }

    /**
     * Returns the render object to render the short code template in the
     * frontend.
     * 
     * @since 1.0.0
     * 
     * @see self::TEMPLATE_TARGET
     * 
     * @return RenderObject The simple render object holding the template.
     */
    public function getShortcodeRenderObject(): RenderObject
    {
        return new RenderObject(self::TEMPLATE_TARGET);
    }

    /**
     * Deserializes the provided shortcode attributes and stores them in
     * $this->params and $this->filters.
     * Filters are determined by the parameter key starting with 'filter' and
     * will be deserialized using the corresponding method.
     *
     * Format:
     * <code>
     * [shortcode
     *   <key>=<value>
     *   filter=<filter_string>
     * ]
     * </code>
     * 
     * Example short code:
     * <code>
     * [taki-events
     *     posts_per_page=5 // param
     *     biws__cat_tax=treffpunkt // param
     *     filter=biws__region_tax // filter
     * ]
     * </code>
     * 
     * @since 1.0.0
     * @access private
     * 
     * @see self::deserializeFilter()
     * 
     * @param string|array $atts The attributes provided by the shortcode.
     * 
     * @return array The deserialized attributes as array containing params
     *               and filters.
     */
    private function deserializeAtts($atts): array
    {
        $result = array();
        $result['params'] = array();
        $result['filters'] = array();

        if (!is_array($atts)) {
            return $result;
        }

        foreach ($atts as $key => $value) {
            if (strncasecmp($key, 'filter', 6) === 0) {
                $result['filters'][] = $this->deserializeFilter($value);
            } else {
                $result['params'][$key] = $value;
            }
        }

        return $result;
    }

    /**
     * Deserializes filters obtained by the shortcode attributes.
     * 
     * Filters can be simple strings which will then be handled by the defaults
     * provided by the modules.
     * Filters can also be provided as relaxed json. Lists within the json have
     * to be wrapped in curved brackets and the items have to be separated using
     * commas.
     * 
     * Example filter codes
     * <code>
     * // default filter - in this case taxonomy selector
     * "biws__region_tax"
     * // default filter - taxonomy = biws__cat_tax, options = [fortbildung, treffpunkt]
     * "{taxonomy:biws__cat_tax,options:(fortbildung,treffpunkt)}"
     * // filter type <X>, id = <id>
     * // currently only one filter type is supported.
     * "{type:<X>,id:<id>}"
     * </code>
     * 
     * @since 1.0.0
     * @access private
     * 
     * @param string $filter The filter string.
     *
     * @return string The deserialized filter as encoded json.
     */
    private function deserializeFilter(string $filter): string
    {
        if (!preg_match('/^{.*}$/', $filter)) {
            return $filter;
        }

        $list_replacement = '__list__';

        $bracket_free = substr($filter, 1, -1);

        $lists = array();
        // temporarily store lists
        preg_match('/\(([^]]+)\)/', $bracket_free, $lists);

        // replace lists
        $list_free = preg_replace(
            '/\(([^]]+)\)/',
            $list_replacement,
            $bracket_free
        );

        $json = array();
        $list_replacement_index = 0;
        foreach (explode(',', $list_free) as $combined_pair) {
            $pair = explode(':', trim($combined_pair));
            $value = $pair[1];
            if ($value === $list_replacement) {
                // get list from temporary store
                $list = $lists[$list_replacement_index++];
                // remove curved brackets
                $list = substr($list, 1, -1);
                // create array
                $list = explode(',', $list);
                // trim all values
                $list = array_map(function ($val) {
                    return trim($val);
                }, $list);
                // set as value
                $value = $list;
            }
            $json[trim($pair[0])] = $value;
        }

        return json_encode($json);
    }
}
