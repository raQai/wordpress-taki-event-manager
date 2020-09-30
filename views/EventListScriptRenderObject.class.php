<?php

namespace BIWS\TaKiEventManager\views;

use BIWS\CPTBuilder\views\RenderObject;

class EventListScriptRenderObject extends RenderObject
{
    private array $shortcode_atts;

    public function __construct(array $shortcode_atts, string $template)
    {
        parent::__construct($template);
        $this->shortcode_atts = $shortcode_atts;
    }

    public function getSettingsJSON(): string
    {
        return json_encode($this->deserializeAtts());
    }

    /**
     * Deserializes the provided shortcode attributes as follows
     *
     * [shortcode
     *   <taxonomy_slug>="item,item2,item3" // <default filter>=<filter items>
     *   filters="<type>=<options>;<type2>=<options2>"]
     * 
     * example
     * [shortcode biws__cat_tax="treffpunkt" filters="selectTaxonomy=biws__region_tax"]
     * 
     * @return object the for the script deserialized attributes as object,
     *                ready to be encoded as json
     */
    private function deserializeAtts(): object
    {
        $script_object = (object)(array());

        if (!$this->shortcode_atts) {
            return $script_object;
        }

        foreach ($this->shortcode_atts as $key => $value) {
            if ($key === 'filters') {
                if (!property_exists($script_object, 'filters')) {
                    $script_object->filters = array();
                }
                $filters = explode(";", $value);
                foreach ($filters as $filter) {
                    $filterObject = explode("=", $filter);
                    if (count($filterObject) !== 2) {
                        continue;
                    }
                    $filterType = $filterObject[0];
                    $scriptFilterSettings = array();
                    switch ($filterType) {
                        case "selectTaxonomy":
                            $filterSettings = explode(",", $filterObject[1]);
                            if (!count($filterSettings)) {
                                break;
                            }
                            $scriptFilterSettings['taxonomy'] =
                                $filterSettings[0];
                            $taxonomy = get_taxonomy($filterSettings[0]);
                            if (!$taxonomy) {
                                break;
                            }
                            $labels = get_taxonomy_labels($taxonomy);
                            $scriptFilterSettings['label'] =
                                $labels->singular_name;

                            if (count($filterSettings) === 2) {
                                $scriptFilterSettings['selected'] =
                                    $filterSettings[1];
                            }

                            $script_object->filters['selectTaxonomy'][] =
                                $scriptFilterSettings;
                            break;
                    }
                }
            } else {
                $script_object->taxonomies[$key] = explode(",", $value);
            }
        }

        return $script_object;
    }
}
