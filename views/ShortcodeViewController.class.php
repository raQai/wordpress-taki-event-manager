<?php

namespace BIWS\TaKiEventManager\views;

use BIWS\CPTBuilder\views\IViewController;

class ShortcodeViewController implements IViewController
{
    private const JS_EVENT_LIST_VERSION = '1.0.1';

    private const JS_EVENT_LIST = BIWS_TaKiEventManager__PLUGIN_DIR_URL
        . 'public/build/bundle.js';

    private const CSS_EVENT_LIST = BIWS_TaKiEventManager__PLUGIN_DIR_URL
        . 'public/build/bundle.css';

    private const TEMPLATE_TARGET = BIWS_TaKiEventManager__PLUGIN_DIR_PATH
        . 'templates/Target.php';

    private const TEMPLATE_SCRIPT = BIWS_TaKiEventManager__PLUGIN_DIR_PATH
        . 'templates/scripts/EventListScript.php';

    public function init(): void
    {
        add_action('init', array($this, 'registerAssets'));
    }

    public function remove(): void
    {
        remove_action('init', array($this, 'registerAssets'));
    }

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

    public function enqueueAssets()
    {
        wp_enqueue_script('biws-event-list-js');
        wp_enqueue_style('biws-event-list-css');
    }

    public function getScriptRenderObject($atts): EventListScriptRenderObject
    {
        return new EventListScriptRenderObject($atts, self::TEMPLATE_SCRIPT);
    }

    public function getShortcodeRenderObject(): ShortcodeRenderObject
    {
        return new ShortcodeRenderObject(self::TEMPLATE_TARGET);
    }
}
