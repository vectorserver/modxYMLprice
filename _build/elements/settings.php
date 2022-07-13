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
                'value' => '{"pagetitle":{"field":"name","info":"Название (обязательный) ","default":""},"parent":{"field":"categoryId","info":"Id категории","default":""},"uri":{"field":"url","info":"Ссылка на страницу товара","default":""},"description":{"field":"description","info":"Описание (обязательный) || Подробное описание товара: например, его преимущества и особенности.","default":""},"tv_images":{"field":"picture","info":"Изображение (обязательный)","default":""},"ms_vendor.name":{"field":"vendor","info":"Бренд (обязательный)","default":""},"ms_size":{"field":"dimensions","info":"Габариты с упаковкой (обязательный для FBY и FBS) || Длина, ширина, высота в упаковке.","default":""},"ms_weight":{"field":"weight","info":"Вес с упаковкой (обязательный для FBY и FBS) || Вес товара с упаковкой.","default":""},"ms_vendor.country":{"field":"country_of_origin","info":"Страна производства || Страна, где был произведен товар.","default":""},"ms_article":{"field":"vendorCode","info":"Артикул производителя || Код товара, который ему присвоил производитель.","default":""},"ms_price":{"field":"price","info":"Цена (обязательный)","default":"1"},"ms_old_price":{"field":"oldprice","info":"Цена до скидки || Если товар продается со скидкой, укажите в этом поле цену без учета скидки. Маркет покажет ее зачеркнутой, чтобы покупатели видели выгоду.","default":"0"},"tv_currencyId":{"field":"currencyId","info":"Валюта (DBS) || RUR, BYN, EUR, USD, UAN, KZT","default":"RUR"},"tv_available":{"field":"available","info":"Статус товара (DBS) || true — товар в наличии, false — под заказ.","default":""},"tv_store":{"field":"store","info":"Купить в магазине без заказа (DBS) || Пометка о том, что товар можно купить офлайн, просто придя в магазин.","default":"true"},"tv_delivery":{"field":"delivery","info":"Доставка (DBS) || Есть ли курьерская доставка.","default":"true"},"tv_pickup":{"field":"pickup","info":"Самовывоз (DBS) || Доступен ли самовывоз.","default":"true"},"tv_customparam":{"field":"param","info":"Характеристики || Кроме общих свойств, у товара есть характеристики, присущие конкретной категории, к которой он относится. Например, у велосипеда есть размер рамы, а детское пюре бывает овощное, мясное или фруктовое.","default":""}}',
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