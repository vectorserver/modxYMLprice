<?php
/* @var modX $modx */
require_once MODX_CORE_PATH.'components/modxYMLprice/model/modxYMLprice/modxYMLprice.php';

/** @var TYPE_NAME $scriptProperties */
$params = array();
$params['snippet'] = $scriptProperties;
if($params['snippet']["parents"]){
    $params['shop']['modxYMLprice_shop_catalog_id'] = $params['snippet']["parents"];
}

$init = new modxYMLprice($modx,$params);
return $init->exportYmlprice();