<?php

namespace BIWS\TaKiEventManager\templates;

use BIWS\TaKiEventManager\views\ShortcodeRenderObject;

if (!($render_object instanceof ShortcodeRenderObject)) {
    die("Template {$__FILE__} requires ShortcodeRenderObject");
}
?>
<div id="biws__events-list" class="alignwide has-small-font-size"></div>