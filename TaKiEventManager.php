<?php

/**
 * TaKi Event Manager plugin
 *
 * Copyright Patrick Bogdan. All rights reserved.
 * See LICENSE.txt for license details.
 *
 * Requirements not tested below specified versions.
 * 
 * @package   BIWS\TaKiEventManager
 * @author    Patrick Bogdan
 * @copyright 2020 Patrick Bogdan
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License, version 3 or later
 * @since     1.0.0
 *
 * @wordpress-plugin
 * Plugin Name:       TaKi Event Manager
 * Plugin URI:        https://github.com/raQai/wordpress-taki-event-manager
 * Description:       Erstellen und Darstellen von Events für Tageskinder-Heilbronn. Benötigt BIWS CPT Builder.
 * Requires at least: 5.5
 * Requires PHP:      7.4
 * Author:            Patrick Bogdan
 * Author URI:        https://github.com/raQai
 * Version:           1.0.0
 * License:           GPL v3 or later
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 */

namespace BIWS\TaKiEventManager;

defined('ABSPATH') or die('Nope!');

if (!defined('WPINC')) {
    die;
}

use BIWS\CPTBuilder\models\CustomPostType;
use BIWS\CPTBuilder\models\fields\FieldType;
use BIWS\CPTBuilder\models\fields\PlaceholderField;
use BIWS\CPTBuilder\models\fields\SimpleField;
use BIWS\CPTBuilder\models\MetaBox;
use BIWS\CPTBuilder\models\Taxonomy;
use BIWS\CPTBuilder\services\CPTService;
use BIWS\CPTBuilder\services\PostDuplicatorService;
use BIWS\CPTBuilder\views\admin\cpt\CPTColumnView;
use BIWS\CPTBuilder\views\admin\cpt\CPTColumnViewController;
use BIWS\CPTBuilder\views\RenderService;
use BIWS\CPTBuilder\views\RenderType;
use BIWS\TaKiEventManager\views\rest\CPTRestEndpointViewController;
use BIWS\TaKiEventManager\views\rest\RestEndpointView;
use BIWS\TaKiEventManager\views\rest\RestProps;
use BIWS\TaKiEventManager\views\ShortcodeView;
use BIWS\TaKiEventManager\views\ShortcodeViewController;
use BIWS\TaKiEventManager\views\rest\TaxonomyRestEndpointViewController;

define('BIWS_TaKiEventManager__PLUGIN_DIR_PATH', plugin_dir_path(__FILE__));
define('BIWS_TaKiEventManager__PLUGIN_DIR_URL', plugin_dir_url(__FILE__));

register_activation_hook(__FILE__, function () {
    if (!function_exists('deactivate_plugins')) {
        include_once ABSPATH . '/wp-admin/includes/plugin.php';
    }

    if (!current_user_can('activate_plugins')) {
        deactivate_plugins(plugin_basename(__FILE__));

        $error_message = "<p>Nicht genug rechte um diese Plugin zu aktivieren.</p>";
        die($error_message);
    }

    if (!class_exists('BIWS\\CPTBuilder\\services\\CPTService')) {
        deactivate_plugins(plugin_basename(__FILE__));

        $error_message = "<p>TaKiEventManager benötigt das <strong>BIWS CPT Builder</strong> Plugin</p>";
        die($error_message);
    }
});

/* FIXME cleaner way to deactivate this plugin but needs more research *
register_deactivation_hook(
    ABSPATH . '/wp-content/plugins/CPTBuilder/CPTBuilder.php',
    function () {
        if (!function_exists('deactivate_plugins')) {
            include_once ABSPATH . '/wp-admin/includes/plugin.php';
        }
        deactivate_plugins(plugin_basename(__FILE__));
    }
);
/**/

if (!class_exists('BIWS\\CPTBuilder\\services\\CPTService')) {
    if (!function_exists('deactivate_plugins')) {
        include_once ABSPATH . '/wp-admin/includes/plugin.php';
    }
    deactivate_plugins(plugin_basename(__FILE__));
    return;
}

require_once BIWS_TaKiEventManager__PLUGIN_DIR_PATH . 'autoloader.inc.php';

$cpt = new CustomPostType(
    'taki_events',
    array(
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
    ),
    array(
        new Taxonomy(
            'biws__cat_tax',
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
        ),
        new Taxonomy(
            'biws__region_tax',
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
        ),
        new Taxonomy(
            'biws__contact_tax',
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
            ),
            array(
                new PlaceholderField(
                    FieldType::TEXT,
                    'phone',
                    'Telefonnummer',
                    '+49 (0) 111 - 222 333 44',
                    true
                ),
                new PlaceholderField(
                    FieldType::EMAIL,
                    'email',
                    'E-Mail',
                    'name@domain.de',
                    true
                ),
            )
        ),
        new Taxonomy(
            'biws__location_tax',
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
            ),
            array(
                new SimpleField(FieldType::TEXT, 'building', 'Gebäude'),
                new SimpleField(FieldType::TEXT, 'street', 'Straße'),
                new SimpleField(FieldType::TEXT, 'street_nr', 'Hausnummer'),
                new SimpleField(FieldType::TEXT, 'zip', 'PLZ'),
                new SimpleField(FieldType::TEXT, 'town', 'Ort'),
            )
        ),
        new Taxonomy(
            'biws__tags_tax',
            array(
                'hierarchical' => true,
                'labels' => array(
                    'name' => 'Tags',
                    'singular_name' => 'Tag',
                    'search_items' =>  'Tag suchen',
                    'all_items' => 'Alle Tags',
                    'parent_item' => 'Übergeordneter Tag',
                    'parent_item_colon' => 'Übergeordneter Tag:',
                    'edit_item' => 'Tag bearbeiten',
                    'update_item' => 'Tag aktualisieren',
                    'add_new_item' => 'Neuen Tag hinzufügen',
                    'new_item_name' => 'Neuer Tagname',
                ),
                'show_ui' => true,
                'show_admin_column' => true,
                'public' => false,
                'show_tag_cloud' => false,
                'show_in_rest' => true,
            ),
            array(new SimpleField(FieldType::COLOR, 'color', 'Farbe'),)
        ),
    ),
    array(
        new MetaBox(
            'biws__datetime_meta',
            'Zeitangaben',
            array( // fields
                new PlaceholderField(
                    FieldType::DATE,
                    'datetime__start_date',
                    'Anfangsdatum',
                    'yyyy-mm-dd'
                ),
                new PlaceholderField(
                    FieldType::DATE,
                    'datetime__end_date',
                    'Enddatum',
                    'yyyy-mm-dd'
                ),
                new PlaceholderField(
                    FieldType::TIME,
                    'datetime__start_time',
                    'Uhrzeit von',
                    'HH:MM'
                ),
                new PlaceholderField(
                    FieldType::TIME,
                    'datetime__end_time',
                    'Uhrzeit bis',
                    'HH:MM'
                ),
            )
        ),
        new MetaBox(
            'biws__fee_meta',
            'Kosten',
            array( // fields
                new SimpleField(
                    FieldType::NUMBER,
                    'fee__entry_fee',
                    'Teilnahmegebühr (in €)'
                ),
                new PlaceholderField(
                    FieldType::TEXT,
                    'fee__entry_fee_info',
                    'Zusätzliche Informationen',
                    'z.B. Mitglieder frei oder nur Eltern'
                ),
            )
        ),
    )
);

$render_service = RenderService::getInstance();

// register all default views for the event cpt
$render_service->registerDefaults($cpt);

if ($render_service instanceof RenderService) {
    // Overwrite column renderer to remove date column
    $render_service->registerPost(
        $cpt->getSlug(),
        RenderType::CPT_COLUMN,
        new CPTColumnView((new CPTColumnViewController($cpt))
            ->removeColumn("date"))
    );

    // register shortcode
    $render_service->registerPost(
        $cpt->getSlug(),
        'taki_shortcode',
        new ShortcodeView(new ShortcodeViewController($cpt))
    );

    $cpt_props = new RestProps(
        'biws/v1',
        $cpt->getSlug(),
        array(
            'methods' => 'GET',
            'permission_callback' => '__return_true'
        )
    );

    // register cpt rest endpoint
    $render_service->registerPost(
        $cpt->getSlug(),
        'taki_rest_endpoint',
        new RestEndpointView(
            new CPTRestEndpointViewController($cpt, $cpt_props)
        )
    );

    foreach ($cpt->getTaxonomies() as $taxonomy) {
        $taxonomy_props = new RestProps(
            $cpt_props->getNamespace(),
            $taxonomy->getId(),
            array(
                'methods' => 'GET',
                'permission_callback' => '__return_true',
            )
        );
        $render_service->registerTaxonomy(
            $cpt->getSlug(),
            $taxonomy->getId(),
            'taki_rest_endpoint',
            new RestEndpointView(
                new TaxonomyRestEndpointViewController(
                    $taxonomy,
                    $taxonomy_props
                )
            )
        );
    }
}

/**
 * register the created events $cpt
 */
$service = CPTService::getInstance();

if ($service instanceof CPTService) {
    $service->registerAndInit($cpt);
}

$post_duplicator = PostDuplicatorService::getInstance();

if ($post_duplicator instanceof PostDuplicatorService) {
    $post_duplicator->register($cpt);
}
