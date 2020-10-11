<?php
use Jgauthi\Component\Utils\Json;

// In this example, the vendor folder is located in "example/"
require_once __DIR__.'/../vendor/autoload.php';

// Return exception if error
$data = Json::decode(file_get_contents(__DIR__.'/../asset/clients.json'), true);

// Output a response like an API (json format)
Json::response($data);
