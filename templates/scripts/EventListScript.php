<?php

/**
 * Copyright Patrick Bogdan. All rights reserved.
 * See LICENSE.txt for license details.
 */

/**
 * Event list script template
 *
 * Template to render the svelte events list app.
 *
 * @since      1.0.0
 *
 * @see        EventListScriptRenderObject
 * 
 * @package    BIWS\TaKiEventManager\templates
 * @subpackage scripts
 *
 * @author     Patrick Bogdan
 * @copyright  2020 Patrick Bogdan
 * @license    https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License, version 3 or later
 */

namespace BIWS\TaKiEventManager\templates\scripts;

use BIWS\TaKiEventManager\views\EventListScriptRenderObject;

if (!($render_object instanceof EventListScriptRenderObject)) {
    die("Template {$__FILE__} requires EventListScriptRenderObject");
}

$filters = $render_object->getFilters()
    ? '\'' . implode('\', \'', $render_object->getFilters()) . '\''
    : '';

// prepare params string for app
$params = json_encode($render_object->getParams());

?>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const props = {
            params: JSON.parse('<?= $params ?>'),
            filters: [<?= $filters ?>]
        }

        new events_list({
            target: document.querySelector("#biws__events-list"),
            props
        });
    });
</script>