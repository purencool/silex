<?php
/**
 *
 * The autoload.php file loads the silex framework and all 
 * modules that were required in the composer json file.
 *
 * Posts.php loads the services needed for the project
 * to function. It structurally sets the paths to the 
 * src directory.
 *
 */
require_once __DIR__.'/../vendor/autoload.php';
require_once __DIR__.'/../web/Posts.php';
$app->run();
