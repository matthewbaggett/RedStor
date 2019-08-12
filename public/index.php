<?php
$pathToVendor = __DIR__ . "/../vendor/";
require_once($pathToVendor . "autoload.php");

\RedStor\RedStor::Instance()
    ->loadAllRoutes()
    ->runHttp();