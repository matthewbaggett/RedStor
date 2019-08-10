<?php
define("__PHPCS_ROOT__", __DIR__);

$additionalDirectories = [
    __DIR__ . "/sdk",
];

return require(__PHPCS_ROOT__ . "/vendor/benzine/benzine-style/php_cs.php");
