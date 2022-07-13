<?php
define('MODX_API_MODE', true);
require '../../../index.php';

$action = $_REQUEST['action'];
$dataValue = $_REQUEST['data'];

//TODO убрать ненужные элементы из запроса

/** @var modX $modx */
/** @var modProcessorResponse $result */

$param = array(
    'key'=>'modxYMLprice_'.$action,//modxYMLprice_offers_key_mapping
    'namespace'=>'modxYMLprice',
    //'area'=>'shop',
    'value'=>$dataValue,
);


$result = $modx->runProcessor('system/settings/updatefromgrid',
    array('data'=>json_encode($param)));

header('Content-type: text/json');

$data = $result->response;
$data['data'] = json_decode($dataValue,true);
echo json_encode($data);
exit;