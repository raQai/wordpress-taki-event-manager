<?php

namespace BIWS\TaKiEventManager\templates\scripts;

use BIWS\TaKiEventManager\views\EventListScriptRenderObject;

if (!($render_object instanceof EventListScriptRenderObject)) {
    die("Template {$__FILE__} requires EventListScriptRenderObject");
}
?>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const settings = JSON.parse('<?= $render_object->getSettingsJSON() ?>'),
            params = {
                perPage: 5
            },
            props = Object.assign({
                params: params
            }, settings);

        new events_list({
            target: document.querySelector("#biws__events-list"),
            props
        });
    });
</script>