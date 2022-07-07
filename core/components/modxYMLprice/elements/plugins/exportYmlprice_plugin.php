<?php
/* @global $modx*/


$eventName = $modx->event->name;
switch($eventName) {
    case 'OnHandleRequest':

        $request = &$_REQUEST;

        if ( $request['q'] == "modxYMLprice.xml"){

            $parents = $request['parents'] ? $request['parents'] : $modx->getOption('shop_catalog_id');
            echo $modx->runSnippet('modxYMLprice_snippet',array('parents'=>$parents));
            exit;
        }


        break;
}
