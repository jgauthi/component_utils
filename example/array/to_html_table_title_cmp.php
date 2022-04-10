<?php
use Jgauthi\Component\Utils\Arrays;

// In this example, the vendor folder is located in "example/"
require_once __DIR__.'/../vendor/autoload.php';

$array1 = ['blue' => 1, 'red' => 2, 'green' => 3, 'purple' => 4];
$array2 = ['green' => 5, 'blue' => 6, 'yellow' => 7, 'cyan' => 8];
$array3 = ['green' => 5, 'blue' => 6, 'red' => 7, 'purple' => 8];

$data = Arrays::mergeTree(
    ['product 3' => $array3, 'product 1' => $array1],
    ['product 2' => $array2]
);

$table = Arrays::to_html_table_title_cmp($data, 'Compare multiple array');

// Format possible également (le nom du produit n'apparaitra pas, à la place: 0, 1, 2...)
// $data = array_cmp($array1, $array2, $array3);

?>
<style>
    tbody th:nth-child(1), tbody td:nth-child(1) 	{ background-color: #F25769; color: white; }
</style>

<div class="row">
    <div class="col-sm-6"><?=$table?></div>
    <div class="col-sm-6" style="font-size: 0.8em;"><?php var_dump($data); ?></div>
</div>