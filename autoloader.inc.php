<?php

namespace BIWS\TaKiEventManager;

defined('ABSPATH') or die('Nope!');

if (!defined('WPINC')) {
    die;
}

spl_autoload_register(function ($className) {
    if (strpos($className, __NAMESPACE__) !== 0) {
        return;
    }
    $className = str_replace(__NAMESPACE__, "", $className);
    // replace all single and double slashes with !
    // mixed slashes \/ /\ will turn into double !!
    $path = preg_replace(
        '#\\\+|\/+#',
        '!',
        BIWS_TaKiEventManager__PLUGIN_DIR_PATH . $className . '.class.php'
    );
    // replace all !+ with a single directory separator
    require_once preg_replace('#!+#', DIRECTORY_SEPARATOR, $path);
});
