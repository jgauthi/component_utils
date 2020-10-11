<?php
use Jgauthi\Component\Utils\Folder;

// In this example, the vendor folder is located in "example/"
require_once __DIR__.'/../vendor/autoload.php';

$dir = realpath(__DIR__.'/../'); // Example/
define('DESCRIPTION', "Folder::get_architecture VS scandir VS glob on folder `{$dir}`.");

$arch = Folder::getArchitecture($dir);
$scandir = scandir($dir);
$glob = glob("$dir/*");

var_dump(
    $arch,
    $scandir,
    $glob
);
