#!/usr/bin/env php
<?php
use Symfony\Component\Yaml\Yaml;

if (is_readable(__DIR__.'/../../../autoload.php')) {
    require_once __DIR__.'/../../../autoload.php';
} elseif (is_readable(__DIR__.'/../vendor/autoload.php')) {
    require_once __DIR__.'/../vendor/autoload.php';
} else {
    die('Autoloader not found');
}

if (empty($argv[1])) {
    die('Argument 1: Fichier requis');
} elseif (!is_readable($argv[1])) {
    die("Argument 1: Fichier {$argv[1]} non accessible ou n'existe pas.");
}

$import = $argv[1];
$export = ((!empty($argv[2])) ? $argv[2] : dirname($import).'/array-export.yml');

try {
    $array = include $import;
    $yaml = Yaml::dump($array);

    if (file_put_contents($export, $yaml)) {
        echo 'done, export on file: '.$export.PHP_EOL.PHP_EOL;
    }

    readfile($export);
} catch (Exception $e) {
    echo 'Exception: ',  $e->getMessage(), "\n";
}
