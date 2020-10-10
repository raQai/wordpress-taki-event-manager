<?php

/**
 * Copyright Patrick Bogdan. All rights reserved.
 * See LICENSE.txt for license details.
 */

/**
 * Event list target template
 *
 * Template to render the svelte events list app target.
 *
 * @since      1.0.0
 *
 * @see        ShortcodeRenderObject
 * 
 * @package    BIWS\TaKiEventManager
 * @subpackage templates
 *
 * @author     Patrick Bogdan
 * @copyright  2020 Patrick Bogdan
 * @license    https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License, version 3 or later
 */

namespace BIWS\TaKiEventManager\templates;

use BIWS\CPTBuilder\views\RenderObject;

if (!($render_object instanceof RenderObject)) {
    die("Template {$__FILE__} requires RenderObject");
}
?>
<noscript>
    <div class="entry-content">
        <div class="wp-block-cover has-background-dim has-background-gradient has-luminous-vivid-orange-to-vivid-red-gradient-background did-you-know">
            <div class="wp-block-cover__inner-container">
                <p class="has-small-font-size">Zur Darstellung dieses Moduls wird JavaScript benötigt.</p>
                <p class="has-small-font-size">Bitte fügen Sie diese Seite zu Ihren Ausnahmen hinzu um JavaScript inhalte korrekt anzuzeigen oder verwenden Sie einen modernen Browser.</p>
            </div>
        </div>
    </div>
</noscript>
<div id="biws__events-list" class="alignwide has-small-font-size"></div>