<?php
// In this example, the vendor folder is located in "example/"
require_once __DIR__.'/../vendor/autoload.php';

$inifile = realpath(__DIR__.'/../asset/matrice_dossier.ini');
$data = parse_ini_file($inifile, true);

$table = call_user_func_array(
    'Jgauthi\Component\Utils\Arrays::to_html_table_title_filter_col',
    [
        'data' => $data,
        'title_table' => 'Display array with filter col',
        'cols_display' => [
            'field_libelle' => 'Libellé',
            'field_name' => 'Nom du champ',
            'key' => 'Clé du champ',
            'required' => 'Requis',
            'comment' => 'Commentaire',
            'not_exist' => 'Not exists',
        ],
        'encode' => 'UTF-8',
    ]
);

//$GLOBALS['class_main'] = 'container-fluid';
?>
<style>
    tbody th:nth-child(1), tbody td:nth-child(1) 	{ background-color: #F25769; color: white; }
    tbody td.required 								{ text-align: center; }
</style>

<div class="row">
    <div class="col-sm-2"></div>
    <div class="col-sm-8">
        <?=$table?>
        <p>Ini file: <?=basename($inifile)?></p>
        <blockquote><?=nl2br(file_get_contents($inifile))?></blockquote>
    </div>
    <div class="col-sm-2"></div>
</div>
<div class="row">
    <div class="col-sm-2"></div>
    <div class="col-sm-4" style="font-size: 0.8em;"><?php var_dump($data); ?></div>
</div>