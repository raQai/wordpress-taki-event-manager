<?php

namespace BIWS\TaKiEventManager\views;

use BIWS\CPTBuilder\views\IView;
use BIWS\CPTBuilder\views\IViewController;

class ShortcodeView implements IView
{
    private ShortcodeViewController $controller;
    public function __construct(ShortcodeViewController $controller)
    {
        $this->controller = $controller;
    }
    public function getController(): IViewController
    {
        return $this->controller;
    }

    public function init(): void
    {
        add_shortcode('taki-events', array($this, 'shortcodeCallback'));
    }

    public function remove(): void
    {
        remove_shortcode('taki-events');
    }

    public function shortcodeCallback(array $atts)
    {
        $this->controller->enqueueAssets();

        add_action('wp_footer', function () use ($atts) {
            $render_object = $this->controller->getScriptRenderObject($atts);
            ob_start();
            $render_object->render();
            ob_end_flush();
        });

        ob_start();
        $this->controller->getShortcodeRenderObject()->render();
        return ob_get_clean();
    }
}
