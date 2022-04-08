<?php

// Setting internal encoding for string functions
mb_internal_encoding('UTF-8');

/**
 * Gets Class name from instances and gets/requires the source files
 * @param string $class_name The URL address to be parsed
 */
function autoload_classes(string $class_name): void
{
    if (file_exists("controllers/$class_name.controller.php")) {
        /** @noinspection PhpIncludeInspection due to dynamic file calling */
        require_once "controllers/$class_name.controller.php";
    } else {
        /** @noinspection PhpIncludeInspection due to dynamic file calling */
        require_once "models/$class_name.model.php";
    }
}

// Registers the callback
spl_autoload_register("autoload_classes");

// Creating the router and processing parameters from the user's URL
$router = new Router();
$router->process([$_SERVER['REQUEST_URI']]);