<?php

/**
 * Returns the importmap for this application.
 *
 * - "path" is a path inside the asset mapper system. Use the
 *     "debug:asset-map" command to see the full list of paths.
 *
 * - "entrypoint" (JavaScript only) set to true for any module that will
 *     be used as an "entrypoint" (and passed to the importmap() Twig function).
 *
 * The "importmap:require" command can be used to add new entries to this file.
 */
return [
    'app' => [
        'path' => './assets/app.js',
        'entrypoint' => true,
    ],
    'jquery' => [
        'version' => '3.6.0',
    ],
    'select2' => [
        'version' => '4.0.13',
    ],
    'select2/dist/css/select2.min.css' => [
        'version' => '4.0.13',
        'type' => 'css',
    ],
    'bootstrap' => [
        'version' => '4.6.2',
    ],
    'popper.js' => [
        'version' => '1.16.1',
    ],
    'admin-lte' => [
        'version' => '3.2.0',
    ],
    'admin-lte/dist/css/adminlte.min.css' => [
        'version' => '3.2.0',
        'type' => 'css',
    ],
    '@fortawesome/fontawesome-free' => [
        'version' => '5.15.4',
    ],
    '@fortawesome/fontawesome-free/css/fontawesome.min.css' => [
        'version' => '5.15.4',
        'type' => 'css',
    ],
];
