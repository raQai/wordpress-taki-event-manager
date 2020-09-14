<?php

/**
 * @package TaKiEventManager
 * @author Patrick Bogdan
 * @copyright 2020 Patrick Bogdan
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2 or later
 *
 * @wordpress-plugin
 * Plugin Name: TaKi Event Manager
 * Description: Erstellen und Darstellen von Events für Tageskinder-Heilbronn. Benötigt BIWS CPT Builder.
 * Author: Patrick Bogdan
 * Version: 1.0.0
 * License: GPL v2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */

namespace BIWS\TaKiEventManager;

use BIWS\CPTBuilder\cpt\CustomPostTypeBuilder;
use BIWS\CPTBuilder\fields\FieldType;
use BIWS\CPTBuilder\metabox\MetaBoxBuilder;
use BIWS\CPTBuilder\taxonomy\TaxonomyBuilder;
use DateTime;
use DateTimeZone;

defined('ABSPATH') or die('Nope!');

if (!defined('WPINC')) {
    die;
}

define('BIWS_TaKiEventManager__PLUGIN_DIR_PATH', plugin_dir_path(__FILE__));
define('BIWS_TaKiEventManager__PLUGIN_DIR_URL', plugin_dir_url(__FILE__));

register_activation_hook(__FILE__, function () {
    if (!function_exists('deactivate_plugins')) {
        include_once ABSPATH . '/wp-admin/includes/plugin.php';
    }

    if (!current_user_can('activate_plugins')) {
        \deactivate_plugins(plugin_basename(__FILE__));

        $error_message = "<p>Nicht genug rechte um diese Plugin zu aktivieren.</p>";
        die($error_message);
    }

    if (!class_exists('BIWS\\CPTBuilder\\cpt\\CustomPostTypeBuilder')) {
        \deactivate_plugins(plugin_basename(__FILE__));

        $error_message = "<p>TaKiEventManager benötigt das <strong>BIWS CPT Builder</strong> Plugin</p>";
        die($error_message);
    }
});

/*
 FIXME cleaner way to deactivate this plugin but it does not trigger for some reason
register_deactivation_hook(ABSPATH . '/wp-content/plugins/CPTBuilder/CPTBuilder.php', function () {
    if (!function_exists('deactivate_plugins')) {
        include_once ABSPATH . '/wp-admin/includes/plugin.php';
    }
    deactivate_plugins(plugin_basename( __FILE__ ));
});
*/

if (
    !class_exists('BIWS\\CPTBuilder\\cpt\\CustomPostTypeBuilder') ||
    !class_exists('BIWS\\CPTBuilder\\taxonomy\\TaxonomyBuilder') ||
    !class_exists('BIWS\\CPTBuilder\\metabox\\MetaBoxBuilder') ||
    !class_exists('BIWS\\CPTBuilder\\fields\\FieldType')
) {
    if (!function_exists('deactivate_plugins')) {
        include_once ABSPATH . '/wp-admin/includes/plugin.php';
    }
    deactivate_plugins(plugin_basename(__FILE__));
    return;
}

include BIWS_TaKiEventManager__PLUGIN_DIR_PATH . 'view/Shortcode.php';

$events_args = array(
    'label' => 'Veranstaltungen',
    'description' => 'Taki Vreanstaltungen',
    'labels' => array(
        'name' => 'Veranstaltungen',
        'singular_name' => 'Veranstaltung',
    ),
    'supports' => array('title', 'editor', 'thumbnail', 'custom-fields'),
    'has_archive' => true,
    'menu_icon' => 'dashicons-calendar-alt',
    'public' => true,
    'show_ui' => true,
    'show_in_menu' => true,
    'show_in_rest' => true,
    'menu_position' => 5,
    'exclude_from_search' => false,
    'rewrite' => array('slug' => "events"),
);

$category_taxonomy = TaxonomyBuilder::create('biws__cat_tax')
    ->args(
        array(
            'hierarchical' => true,
            'labels' => array(
                'name' => 'Kategorien',
                'singular_name' => 'Kategorie',
                'search_items' =>  'Kategorie suchen',
                'all_items' => 'Alle Kategorien',
                'parent_item' => 'Übergeordnete Kategorie',
                'parent_item_colon' => 'Übergeordnete Kategorie:',
                'edit_item' => 'Kategorie bearbeiten',
                'update_item' => 'Kategorie aktualisieren',
                'add_new_item' => 'Neue Kategorie hinzufügen',
                'new_item_name' => 'Neuer Kategoriename',
            ),
            'show_ui' => true,
            'show_admin_column' => true,
            'show_tag_cloud' => false,
            'show_in_rest' => true,
            'query_var' => true,
        )
    )
    ->build();

$region_taxonomy = TaxonomyBuilder::create('biws__region_tax')
    ->args(
        array(
            'hierarchical' => true,
            'labels' => array(
                'name' => 'Regionen',
                'singular_name' => 'Region',
                'add_new_item' => 'Neue Region erstellen',
                'search_items' => 'Region suchen',
                'edit_item' => 'Region bearbeiten',
            ),
            'show_ui' => true,
            'show_admin_column' => true,
            'show_tag_cloud' => false,
            'show_in_rest' => true,
            'query_var' => true,
        )
    )
    ->build();

$contact_taxonomy = TaxonomyBuilder::create('biws__contact_tax')
    ->args(
        array(
            'hierarchical' => true,
            'labels' => array(
                'name' => 'Ansprechpartner*In',
                'singular_name' => 'Ansprechpartner*In',
                'search_items' =>  'Ansprechpartner*In suchen',
                'all_items' => 'Alle Ansprechpartner*Innen',
                'parent_item' => 'Übergeordnete/r Ansprechpartner*In',
                'parent_item_colon' => 'Übergeordnete/r Ansprechpartner*In:',
                'edit_item' => 'Ansprechpartner*In bearbeiten',
                'update_item' => 'Ansprechpartner*In aktualisieren',
                'add_new_item' => 'Neue/n Ansprechpartner*In hinzufügen',
                'new_item_name' => 'Neuer Ansprechpartner*Innen-Name',
            ),
            'show_ui' => true,
            'show_admin_column' => true,
            'public' => false,
            'show_tag_cloud' => false,
            'show_in_rest' => true,
        )
    )
    ->addField(FieldType::TEXT, 'phone', 'Telefonnummer', true, true, '+49 (0) 111 - 222 333 44')
    ->addField(FieldType::EMAIL, 'email', 'E-Mail', true, true, 'name@domain.de')
    ->build();

$location_taxonomy = TaxonomyBuilder::create('biws__location_tax')
    ->args(
        array(
            'hierarchical' => true,
            'labels' => array(
                'name' => 'Orte',
                'singular_name' => 'Ort',
                'search_items' =>  'Ort suchen',
                'all_items' => 'Alle Orte',
                'parent_item' => 'Übergeordneter Ort',
                'parent_item_colon' => 'Übergeordneter Ort:',
                'edit_item' => 'Ort bearbeiten',
                'update_item' => 'Ort aktualisieren',
                'add_new_item' => 'Neuen Ort hinzufügen',
                'new_item_name' => 'Neuer Ortname',
            ),
            'show_ui' => true,
            'show_admin_column' => true,
            'public' => false,
            'show_tag_cloud' => false,
            'show_in_rest' => true,
        )
    )
    ->addField(FieldType::TEXT, 'building', 'Gebäude', false, true)
    ->addField(FieldType::TEXT, 'street', 'Straße', false, true)
    ->addField(FieldType::TEXT, 'street_nr', 'Hausnummer', false, true)
    ->addField(FieldType::TEXT, 'zip', 'PLZ', false, true)
    ->addField(FieldType::TEXT, 'location', 'Ort', false, true)
    ->build();

$datetime_meta_box = MetaBoxBuilder::create('biws__datetime_meta')
    ->title('Zeitangaben')
    ->addField(FieldType::DATE, 'datetime__start_date', 'Anfangsdatum', true)
    ->addField(FieldType::DATE, 'datetime__end_date', 'Enddatum', true)
    ->addField(FieldType::TIME, 'datetime__start_time', 'Uhrzeit von', true)
    ->addField(FieldType::TIME, 'datetime__end_time', 'Uhrzeit bis', true)
    ->build();

$date = new DateTime('now', new DateTimeZone('Europe/Berlin'));
$today_date = $date->format('Y-m-d');
$now_time = $date->format('H:m');

$events_rest_params = array(
    'meta_query' => array(
        'relation' => 'OR',
        array(
            'key' => 'datetime__start_date',
            'compare' => 'NOT EXISTS',
        ),
        array(
            'key' => 'datetime__start_date',
            'value' => '',
            'compare' => '=',
        ),
        array(
            'key' => 'datetime__start_date',
            'value' => $today_date,
            'type' => 'DATE',
            'compare' => '>',
        ),
        array(
            'key' => 'datetime__end_date',
            'value' => $today_date,
            'type' => 'DATE',
            'compare' => '>',
        ),
        array(
            array(
                'relation' => 'OR',
                array(
                    'key' => 'datetime__start_date',
                    'value' => $today_date,
                    'compare' => '=',
                ),
                array(
                    'key' => 'datetime__end_date',
                    'value' => $today_date,
                    'compare' => '=',
                ),
            ),
            array(
                'relation' => 'OR',
                array(
                    'key' => 'datetime__start_time',
                    'value' => '',
                    'compare' => '=',
                ),
                array(
                    'key' => 'datetime__start_time',
                    'compare' => 'NOT EXISTS',
                ),
                array(
                    'key' => 'datetime__end_time',
                    'value' => '',
                    'compare' => '=',
                ),
                array(
                    'key' => 'datetime__end_time',
                    'compare' => 'NOT EXISTS',
                ),
                array(
                    'key' => 'datetime__start_time',
                    'value' => $now_time,
                    'compare' => '>=',
                ),
                array(
                    'key' => 'datetime__end_time',
                    'value' => $now_time,
                    'compare' => '>=',
                ),
            )
        ),
    ),
);

function events_sort_callback($event, $other)
{
    $event_meta = $event['biws__datetime_meta'];
    $other_meta = $other['biws__datetime_meta'];
    $compare_keys = array(
        'datetime__start_date',
        'datetime__start_time',
        'datetime__end_time',
        'datetime__end_date'
    );

    foreach ($compare_keys as $key) {
        $key_exists = array_key_exists($key, $event_meta);
        $other_exists = array_key_exists($key, $other_meta);
        $compare = 0;
        if (!$key_exists && !$other_exists) {
            continue;
        } else if (!$key_exists) {
            $compare = -1;
        } else if (!$other_exists) {
            $compare = 1;
        } else {
            $compare = $event_meta[$key] <=> $other_meta[$key];
        }
        if ($compare !== 0) {
            return $compare;
        }
    }

    return 0;
};

CustomPostTypeBuilder::create("biws-events")
    ->args($events_args)
    ->addTaxonomy($category_taxonomy)
    ->addTaxonomy($region_taxonomy)
    ->addTaxonomy($location_taxonomy)
    ->addTaxonomy($contact_taxonomy)
    ->addMetaBox($datetime_meta_box)
    ->unsetColumns('date')
    ->setRestRoute(
        'biws/v1',
        'biws-events',
        $events_rest_params,
        '\\' . __NAMESPACE__ . '\\events_sort_callback'
    )
    ->buildAndInit();
