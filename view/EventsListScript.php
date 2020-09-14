<?php

namespace BIWS\TaKiEventManager\shortcode;

defined('ABSPATH') or die('Nope!');

?>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const settings = JSON.parse('<?php echo json_encode($script_object); ?>'),
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