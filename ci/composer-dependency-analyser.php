<?php

use ShipMonk\ComposerDependencyAnalyser\Config\Configuration;

$config = new Configuration();

function getConfigFiles(): array
{
    $files = [];
    $paths = [__DIR__ . '/../config/*'];

    while ($paths !== []) {
        $path = array_shift($paths);
        foreach ((array) glob($path) as $filePathname) {
            if (is_dir($filePathname)) {
                $paths[] = $filePathname . '/*';
            } elseif (str_ends_with($filePathname, '.yaml')) {
                $files[] = $filePathname;
            }
        }
    }

    sort($files);
    return $files;
}

$classNameRegex = '[a-zA-Z_\x80-\xff][a-zA-Z0-9_\x80-\xff]*';
$pattern        = "`$classNameRegex(?:\\\\$classNameRegex)+`";
$classes        = [];
foreach (getConfigFiles() as $file) {
    $file = (string) realpath($file);
    $dicFileContents = (string) \file_get_contents($file);
    \preg_match_all($pattern, $dicFileContents, $matches);
    $classes = array_merge($classes, $matches[0]);
}

return $config
    ->addPathToScan(__DIR__ . '/../bin/console', isDev: false)
    ->addPathToScan(__DIR__ . '/../src', isDev: false)
    ->addPathToScan(__DIR__ . '/../scripts', isDev: false)
    ->addPathToScan(__DIR__ . '/../tests', isDev: true)
    ->addPathToScan(__DIR__ . '/../features/bootstrap', isDev: true)
    ->addForceUsedSymbols($classes)
;
