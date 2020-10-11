<?php
use Jgauthi\Component\Utils\Zip;

// In this example, the vendor folder is located in "example/"
require_once __DIR__.'/../vendor/autoload.php';

// http://php.net/manual/fr/book.zip.php
if (!class_exists('ZipArchive')) {
    die('La class ZipArchive n\'est pas actif dans votre configuration de php');
}

// Settings
$filename = sys_get_temp_dir().'/ZipArchive.zip';
$dirToAdd = realpath(__DIR__.'/../'); // example/

if (file_exists($filename) && !unlink($filename)) {
    die("Impossible de supprimer l'ancien fichier {$filename}");
}

// Création de l'archive
$zip = new ZipArchive;
if (true !== $zip->open($filename, ZipArchive::CREATE)) {
    exit("Impossible d'ouvrir le fichier <$filename>\n");
}

$zip->addFromString('testfilephp.txt', "#1 Ceci est une chaîne texte, ajoutée comme testfilephp.txt.\n");
Zip::addDir($zip, $dirToAdd, 'example');

$zip->close();
