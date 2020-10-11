<?php
use Jgauthi\Component\Utils\Http;

// In this example, the vendor folder is located in "example/"
require_once __DIR__.'/../vendor/autoload.php';

$file = realpath(__DIR__.'/../asset/clients.json');
if (isset($_GET['dl'])) {
    // Symfony Alternative: https://symfony.com/doc/current/components/http_foundation.html#serving-files
    Http::download_file($file);
}

?><p>Download <a href="?dl">file</a>.</p>