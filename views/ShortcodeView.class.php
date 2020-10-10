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

use BIWS\CPTBuilder\views\IView;
use BIWS\CPTBuilder\views\IViewController;

/**
 * Shortcode view implementation
 *
 * @since      1.0.0
 *
 * @package    BIWS\TaKiEventManager
 * @subpackage views
 */
class ShortcodeView implements IView
{
    /**
     * The controller managing this view. 
     *
     * @since 1.0.0
     * @access private
     *
     * @var ShortcodeViewController $controller The controller for this view.
     */
    private ShortcodeViewController $controller;

    /**
     * @since 1.0.0
     *
     * @param ShortcodeViewController $controller The controller managing
     *                                            this view.
     */
    public function __construct(ShortcodeViewController $controller)
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
     * @see add_shortcode()
     * 
     * @link https://developer.wordpress.org/reference/functions/add_shortcode/
     */
    public function init(): void
    {
        add_shortcode('taki-events', array($this, 'shortcodeCallback'));
    }

    /**
     * @since 1.0.0
     *
     * @see IView::remove()
     * @see remove_shortcode()
     * 
     * @link https://developer.wordpress.org/reference/functions/remove_shortcode/
     */
    public function remove(): void
    {
        remove_shortcode('taki-events');
    }

    /**
     * Renders the shortcode in the frontend by displaying the corresponding
     * template and adding the necessary script to the footer.
     * 
     * @since 1.0.0
     * 
     * @see ShortcodeViewController::getScriptRenderObject()
     * @see ShortcodeViewController::getShortcodeRenderObject()
     *
     * @param string|array $atts The shortcode attributes provided to the shortcode
     *                     components.
     * 
     * @return string|bool The contents of the output buffer, false if buffering
     *                     is not active.
     */
    public function shortcodeCallback($atts)
    {
        $this->controller->enqueueAssets();

        add_action('wp_footer', function () use ($atts) {
            ob_start();
            $this->controller->getScriptRenderObject($atts)->render();
            ob_end_flush();
        });

        ob_start();
        $this->controller->getShortcodeRenderObject()->render();
        return ob_get_clean();
    }
}
