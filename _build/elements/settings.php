<?php


return
    array (
        'shop_name' =>
            array (
                ////'key' => 'shop_name',
                'value' => '[[++site_name]]',
                'xtype' => 'textfield',
                'namespace' => 'modxYMLprice',
                'area' => 'shop',
                'editedon' => '2022-07-05 21:42:09',
            ),
        'shop_company' =>
            array (
               // //'key' => 'shop_company',
                'value' => '!Компания - [[++site_name]]',
                'xtype' => 'textfield',
                'namespace' => 'modxYMLprice',
                'area' => 'shop',
                'editedon' => '2022-07-05 21:43:03',
            ),
        'shop_url' =>
            array (
                //'key' => 'shop_url',
                'value' => '[[++site_url]]',
                'xtype' => 'textfield',
                'namespace' => 'modxYMLprice',
                'area' => 'shop',
                'editedon' => '2022-07-05 21:43:26',
            ),
        'shop_platform' =>
            array (
                //'key' => 'shop_platform',
                'value' => 'MODX Revolution [[++settings_version]] ([[++settings_distro]])',
                'xtype' => 'textfield',
                'namespace' => 'modxYMLprice',
                'area' => 'shop',
                'editedon' => '2022-07-05 21:45:30',
            ),
        'shop_catalog_id' =>
            array (
                //'key' => 'shop_catalog_id',
                'value' => '2',
                'xtype' => 'numberfield',
                'namespace' => 'modxYMLprice',
                'area' => 'shop',
                'editedon' => '2022-07-05 14:34:39',
            ),
        'shop_currencyId' =>
            array (
                //'key' => 'shop_currencyId',
                'value' => 'RUR',
                'xtype' => 'textfield',
                'namespace' => 'modxYMLprice',
                'area' => 'shop',
                'editedon' => '-1-11-30 00:00:00',
            ),
        'offers_key_mapping' =>
            array (
                //'key' => 'offers_key_mapping',
                'value' => 'name==pagetitle||description==description||price==tv_price||delivery==true||picture==tv_images||param==tv_options||pickup==true',
                'xtype' => 'textarea',
                'namespace' => 'modxYMLprice',
                'area' => 'offers',
                'editedon' => '2022-07-06 15:30:54',
            ),
        'snippet_handler' =>
            array (
                //'key' => 'snippet_handler',
                'value' => 'pdoResources',
                'xtype' => 'textfield',
                'namespace' => 'modxYMLprice',
                'area' => 'shop_handler',
                'editedon' => '2022-07-05 14:30:43',
            ),
    );