<?php

namespace BIWS\TaKiEventManager\shortcode;

defined('ABSPATH') or die('Nope!');

add_action('init', function () {
    if (!wp_script_is('biws-event-list-js')) {
        wp_register_script(
            'biws-event-list-js',
            BIWS_TaKiEventManager__PLUGIN_DIR_URL . 'public/build/bundle.js',
            array(),
            '1.0.0',
            true
        );
        wp_register_style(
            'biws-event-list-css',
            BIWS_TaKiEventManager__PLUGIN_DIR_URL . 'public/build/bundle.css'
        );
    }
});

add_shortcode('biws-events', function ($atts) {
    wp_enqueue_style('biws-event-list-css');
    wp_enqueue_script('biws-event-list-js');

    add_action('wp_footer', function () use ($atts) {
        $script_object = deserializeAttributes($atts);
        ob_start();
        include BIWS_TaKiEventManager__PLUGIN_DIR_PATH . 'view/EventsListScript.php';
        ob_end_flush();
    });

    ob_start();
    include BIWS_TaKiEventManager__PLUGIN_DIR_PATH . 'view/Target.php';
    return ob_get_clean();
});

/**
 * [shortcode
 *   <taxonomy_slug>="item,item2,item3" // <default filter>=<filter items>
 *   filters="<type>=<options>;<type2>=<options2>"]
 * 
 * example
 * [shortcode biws__cat_tax="treffpunkt" filters="selectTaxonomy=biws__region_tax"]
 */
function deserializeAttributes($atts)
{
    $script_object = (object)(array());

    if (!$atts) {
        return $script_object;
    }

    foreach ($atts as $key => $value) {
        if ($key === 'filters') {
            if (!property_exists($script_object, 'filters')) {
                $script_object->filters = [];
            }
            $filters = explode(";", $value);
            foreach ($filters as $filter) {
                $filterObject = explode("=", $filter);
                if (count($filterObject) !== 2) {
                    continue;
                }
                $filterType = $filterObject[0];
                $scriptFilterSettings = [];
                switch ($filterType) {
                    case "selectTaxonomy":
                        $filterSettings = explode(",", $filterObject[1]);
                        if (!count($filterSettings)) {
                            break;
                        }
                        $scriptFilterSettings['taxonomy'] = $filterSettings[0];
                        $taxonomy = get_taxonomy($filterSettings[0]);
                        if (!$taxonomy) {
                            break;
                        }
                        $labels = get_taxonomy_labels($taxonomy);
                        $scriptFilterSettings['label'] = $labels->singular_name;

                        if (count($filterSettings) === 2) {
                            $scriptFilterSettings['selected'] = $filterSettings[1];
                        }

                        $script_object->filters['selectTaxonomy'][] = $scriptFilterSettings;
                        break;
                }
            }
        } else {
            $script_object->taxonomies[$key] = explode(",", $value);
        }
    }

    return $script_object;
}
