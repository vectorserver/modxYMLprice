<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
if (!defined('MODX_CORE_PATH')) {
    $path = dirname(__FILE__);
    while (!file_exists($path . '/core/config/config.inc.php') && (strlen($path) > 1)) {
        $path = dirname($path);
    }
    define('MODX_CORE_PATH', $path . '/core/');
}

return [
    'name' => 'modxYMLprice',
    'name_lower' => 'modxYMLprice',
    'version' => '2.2.126',
    'release' => 'pl',
    // Install package to site right after build
    'install' => true,
    // Which elements should be updated on package upgrade
    'update' => [
        'chunks' => true,
        'menus' => true,
        'permission' => false,
        'plugins' => true,
        'policies' => false,
        'policy_templates' => false,
        'resources' => false,
        'settings' => true,
        'snippets' => true,
        'templates' => false,
        'widgets' => false,
    ],
    // Which elements should be static by default
    'static' => [
        'plugins' => true,
        'snippets' => true,
        'chunks' => true,
    ],
    // Log settings
    'log_level' => !empty($_REQUEST['download']) ? 0 : 3,
    'log_target' => php_sapi_name() == 'cli' ? 'ECHO' : 'HTML',
    // Download transport.zip after build
    'download' => !empty($_REQUEST['download']),
];