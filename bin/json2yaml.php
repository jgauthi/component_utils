#!/usr/bin/env php
<?php
use Jgauthi\Component\Utils\Json;
use Symfony\Component\Yaml\Yaml;

if (is_readable(__DIR__.'/../../../autoload.php')) {
    require_once __DIR__.'/../../../autoload.php';
} elseif (is_readable(__DIR__.'/../vendor/autoload.php')) {
    require_once __DIR__.'/../vendor/autoload.php';
} else {
    die('Autoloader not found');
}

$import = $argv[1];
$export = ((!empty($argv[2])) ? $argv[2] : dirname($import));

try {
    if (is_dir($export)) {
        if (!is_writable($export)) {
            throw new Exception("Le dossier d'export {$export} n'a pas les droits en écriture.");
        }

        $export .= DIRECTORY_SEPARATOR.str_replace('.json', '.yaml', basename($import));
    } elseif (file_exists($export)) {
        unlink($export);
    }

    if (!is_readable($import)) {
        throw new Exception("Le fichier {$import} n'est pas accessible ou n'existe pas.");
    }

    $jsonContent = Json::decode( file_get_contents($import) );
    $yamlContent = Yaml::dump($jsonContent, 4, 2, Yaml::DUMP_OBJECT);
    if (file_put_contents($export, $yamlContent)) {
        echo 'Fichier généré: '.$export.PHP_EOL;
    }

} catch (Exception $exception) {
    die($exception->getMessage()."\n");
}
