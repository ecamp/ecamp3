<?php

$env = getenv('env') ?: 'dev';

return [
    // Retrieve the list of modules for this application.
    'modules' => include __DIR__ . '/modules.config.php',
    // This should be an array of paths in which modules reside.
    // If a string key is provided, the listener will consider that a module
    // namespace, the value of that key the specific path to that module's
    // Module class.
    'module_listener_options' => [
        'module_paths' => [
            './module',
            './plugin',
            './vendor'
        ],
        // Using __DIR__ to ensure cross-platform compatibility. Some platforms --
        // e.g., IBM i -- have problems with globs that are not qualified.
        'config_glob_paths' => [
            realpath(__DIR__) . '/autoload/{,*.}global.php',
            realpath(__DIR__) . '/autoload/{,*.}global.' . $env. '.php',
            realpath(__DIR__) . '/autoload/{,*.}local.php',
            realpath(__DIR__) . '/autoload/{,*.}local.' . $env. '.php',
        ],
        'config_cache_key' => 'application.config.cache',
        'config_cache_enabled' => false,
        'module_map_cache_key' => 'application.module.cache',
        'module_map_cache_enabled' => false,
        'cache_dir' => 'data/cache/',
    ],
];
