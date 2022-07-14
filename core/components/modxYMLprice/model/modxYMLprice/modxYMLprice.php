<?php
/*docs fields: https://yandex.ru/support/marketplace/assortment/fields/index.html*/

//build 0.2.123

class modxYMLprice
{
    /* @var modX $modx */
    public $modx;
    public $config = array();

    /**
     * @param $modx
     * @param $config
     */
    public function __construct(&$modx, $config)
    {
        $this->modx = $modx;

        $this->testmode = 0;

        $cfg_settings = array();
        $settings = $this->modx->getCollection('modSystemSetting', array('namespace' => 'modxYMLprice'));
        $tmp = array();
        foreach ($settings as $setting) {
            $setting->key = str_replace('modxYMLprice_', '', $setting->key);
            $tmp[$setting->key] = $setting->toArray();
            $cfg_settings[$setting->area][$setting->key] = $setting->value;
        }

        $this->config = array_replace_recursive($cfg_settings, $config);

        //replaceNamespace modxYMLprice_


        if (!$this->config['snippet']['parents']) {
            $this->config['snippet']['parents'] = $this->config['shop']['shop_catalog_id'];
        }


        $offersConfig = str_replace(array("\r", "\n", "\t"), "", trim($this->config['offers']['offers_key_mapping']));
        //Default params

        if (!$offersConfig) {
            $offersConfig = json_encode($this->prepareFields(), JSON_UNESCAPED_UNICODE);
            $this->setSetting('modxYMLprice_offers_key_mapping', $offersConfig);
        }

        $this->config['offers']['offers_key_mapping'] = json_decode($offersConfig);
        $this->config['tags'] = $this->prepareFields();

        //tv and fields


    }


    /**
     * @return string
     */
    public function exportYmlprice()
    {


        //Render
        $this->config['shop']['lastmod'] = date('Y-m-d') . "T01:00:00-01:00";
        $this->config['categories'] = "\n" . implode("\n", $this->getYmlcategories()) . "\n";
        $this->config['offers'] = "\n" . implode("\n", $this->getYmloffers()) . "\n";

        $chunk = $this->modx->getChunk('exportYmlprice_chunk', $this->config);
        @header('Content-Type: text/xml');
        return $chunk;
    }


    /**
     * @return array
     */
    public function getAllfieldsResource()
    {
        $params_elem = array(
            'fastMode' => '1',
            'sdhowParentroot' => '1',
            'select' => 'id,class_key',
            'hideContainers' => 1,
        );

        $offers_data = $this->getData($params_elem);
        $prepare = array();

        foreach ($offers_data as $elem) {
            /* @var msProduct $product */
            ///* @var modDocument $product*/
            $product = $this->modx->getObject($elem->class_key, $elem->id);
            $prepare['pagetitle'] = array('name' => 'Resource', 'value' => 'pagetitle');
            $prepare['description'] = array('name' => 'Resource', 'value' => 'description');
            $prepare['introtext'] = array('name' => 'Resource', 'value' => 'introtext');
            $prepare['uri'] = array('name' => 'Resource', 'value' => 'uri');
            $prepare['alias'] = array('name' => 'Resource', 'value' => 'alias');
            if ($elem->class_key == "msProduct") {
                $optionsData = $product->getOne('Data');
                foreach ($optionsData->toArray() as $k => $opt) {
                    $prepare[$k] = array('name' => 'Ms2 Option', 'value' => 'ms_' . $k);
                }
            }

            /* @var modTemplateVarResource $tvs */
            $tvs = $this->modx->getCollection('modTemplateVarResource', array(
                'contentid' => $product->id
            ));
            foreach ($tvs as $k => $tvItem) {
                $tv = $tvItem->getOne('TemplateVar');
                $prepare[$tv->name] = array('name' => "TV " . $tv->caption . " - " . $tv->name, 'value' => 'tv_' . $tv->name);
            }


        }


        return $prepare;


    }

    /**
     * @return array
     */
    private function getYmlcategories()
    {
        //Вывод категорий
        /* @var modResource $rootCat */
        $categories = array();
        $category_data = $this->getData(array(
            'fastMode' => '1',
            'sdhowParentroot' => '1',
            'select' => 'id,parent,pagetitle,class_key',
            'where' => array('isfolder' => 1),
        ));

        $rootCat = $this->modx->getObject('modResource', $this->config["shop"]["shop_catalog_id"]);

        if ($rootCat) $categories[] = "\t\t\t<category id=\"{$rootCat->id}\">{$rootCat->pagetitle}</category>";

        foreach ($category_data as $item_category) {
            $categories[] = "\t\t\t<category id=\"{$item_category->id}\" parentId=\"{$item_category->parent}\">{$item_category->pagetitle}</category>";
        }

        return $categories;
    }

    /**
     * @return array
     */

    private function getYmloffers()
    {

        //
        $params_offers = array(
            'fastMode' => '1',
            'sdhowParentroot' => '1',
            'select' => 'id,class_key',
            'hideContainers' => 1,
        );

        $offers_data = $this->getData($params_offers);

        //input tipes
        $Inputtypes = array("tv_", "ms_");
        $offers = array();
        $offersXmlData = array();
        $options_product = array();
        foreach ($offers_data as $offer_item) {
            /* @var msProduct $product */
            /* @var modResource $product */
            $product = $this->modx->getObject($offer_item->class_key, $offer_item->id);
            $productData = $product->toArray();

            //Field reconciliation
            foreach ($this->config['offers']['offers_key_mapping'] as $res_key => $field_data) {
                $itemXmlData = array(
                    'xmlTag' => $field_data->field,
                    'value' => $field_data->default,
                    'fieldDist' => 'resource',
                    'res_key' => $res_key,
                );
                $typeCheck = mb_substr($res_key, 0, 3);
                $field_resource = mb_substr($res_key, 3);

                //Tv data
                if ($typeCheck == "tv_") {


                    /* @var  modTemplateVar $tv */
                    $tv = $this->modx->getObject('modTemplateVar', array('name' => $field_resource));

                    if (!$tv) {
                        $tv_output = $field_data->default;
                    } else {

                        $tv_input = $tv->getValue($product->id);

                        if ($tv->get('display') == "delim") {

                            $tvOpt = explode($tv->get('output_properties')["delimiter"], $tv_input);

                            $tmpOpt = array();
                            foreach ($tvOpt as $iparam) {
                                $options_product[] = array("name" => $tv->get('caption'), "#text" => $iparam);
                            }

                            $tv_output = $tmpOpt;

                        } else {
                            $tv_output = $tv_input;
                        }
                    }

                    $itemXmlData['value'] = $tv_output;
                    $itemXmlData['fieldDist'] = 'tv';
                    $itemXmlData['res_key'] = $field_resource;

                } elseif ($typeCheck == "ms_") {


                    $ms = $product->get($field_resource);
                    if (!$ms) {
                        $ms = $field_data->default;
                    }

                    $itemXmlData['value'] = $ms;
                    $itemXmlData['fieldDist'] = 'ms';
                    $itemXmlData['res_key'] = $field_resource;

                } else {
                    $itemXmlData['value'] = $product->$res_key;
                }


                //add Cfg
                if ($itemXmlData['xmlTag'] == "url") {
                    $itemXmlData['value'] = $this->config["shop"]["shop_url"] . $itemXmlData['value'];
                }

                if ($itemXmlData['xmlTag'] == "name" || $itemXmlData['xmlTag'] == "description") {
                    $value = trim($itemXmlData['value']);
                    $value = strip_tags($value);
                    $value = htmlspecialchars($value);
                    $value = "<![CDATA[{$value}]]>";
                    $itemXmlData['value'] = $value;
                }


                if ($itemXmlData['xmlTag'] == "picture") {

                    $im_tmp = $itemXmlData['value'];

                    $result = json_decode($im_tmp);

                    if (json_last_error() === JSON_ERROR_NONE) {

                        $images = array();
                        foreach ($result as $str) {
                            foreach ($str as $item) {
                                if (preg_match('/(.*?\.(?:png|jpg|jpeg|gif|svg))/', $item)) {
                                    $images[] = $item;
                                }

                            }
                        }

                        $im_tmp = array_shift($images);
                    }


                    $itemXmlData['value'] = $this->config["shop"]["shop_url"] . $im_tmp;
                }


                $offersXmlData[$field_data->field] = $itemXmlData;


            }


            //options ms
            if ($offer_item->class_key == "msProduct") {
                $optionsData = $product->getOne('Data');
                $options = $optionsData->get('options');
                if ($options) {
                    foreach ($options as $optKey => $iptem) {
                        $options_product[] = array("name" => $product->get($optKey . '.caption'), "#text" => $iptem[0]);
                    }
                }
            }

            $offersXmlData["param"]["value"] = $options_product;


            //render
            $offerTags = "";
            foreach ($offersXmlData as $tag => $item) {

                if ($item["value"]) {


                    if ($tag == "param" && is_array($item["value"])) {

                        foreach ($item["value"] as $param_item) {
                            //<param name="Мощность">750 Вт</param>
                            $offerTags .= "\n\t\t\t\t<param name='{$param_item["name"]}'>{$param_item["#text"]}</param>";
                        }
                        //unset($item_data['param']);
                    } else {
                        $offerTags .= "\n\t\t\t\t<$tag>{$item["value"]}</$tag>";
                    }


                }
            }
            $offers[] = "\t\t\t<offer id=\"{$product->id}\">{$offerTags}\n\t\t\t</offer>";
        }


        return $offers;
    }


    /**
     * @param $params
     * @return mixed
     */
    private function getData($params)
    {

        $params['limit'] = 0;
        $params['return'] = 'json';
        $params_goto = array_merge($params, $this->config['snippet']);

        $snippet_handler = $this->config["shop_handler"]["snippet_handler"] ?? "pdoResources";
        $getData = $this->modx->runSnippet($snippet_handler, $params_goto);
        return json_decode($getData);
    }


    /**
     * @return false|array
     */
    public function prepareFields()
    {
        $fields = array(
            'pagetitle' => array('field' => 'name', 'info' => 'Название (обязательный) ', 'default' => ''),
            'parent' => array('field' => 'categoryId', 'info' => 'Id категории', 'default' => ''),
            'uri' => array('field' => 'url', 'info' => 'Ссылка на страницу товара', 'default' => ''),
            'description' => array('field' => 'description', 'info' => 'Описание (обязательный) || Подробное описание товара: например, его преимущества и особенности.', 'default' => ''),

            //'ms_image' => array('field' => 'picture', 'info' => 'Изображение (обязательный)', 'default' => ''),
            'tv_images' => array('field' => 'picture', 'info' => 'Изображение (обязательный)', 'default' => ''),
            'ms_vendor.name' => array('field' => 'vendor', 'info' => 'Бренд (обязательный)', 'default' => ''),
            'ms_size' => array('field' => 'dimensions', 'info' => 'Габариты с упаковкой (обязательный для FBY и FBS) || Длина, ширина, высота в упаковке.', 'default' => ''),
            'ms_weight' => array('field' => 'weight', 'info' => 'Вес с упаковкой (обязательный для FBY и FBS) || Вес товара с упаковкой.', 'default' => ''),
            'ms_vendor.country' => array('field' => 'country_of_origin', 'info' => 'Страна производства || Страна, где был произведен товар.', 'default' => ''),
            'ms_article' => array('field' => 'vendorCode', 'info' => 'Артикул производителя || Код товара, который ему присвоил производитель.', 'default' => ''),
            'ms_price' => array('field' => 'price', 'info' => 'Цена (обязательный)', 'default' => '1'),
            'ms_old_price' => array('field' => 'oldprice', 'info' => 'Цена до скидки || Если товар продается со скидкой, укажите в этом поле цену без учета скидки. Маркет покажет ее зачеркнутой, чтобы покупатели видели выгоду.', 'default' => '0'),

            'tv_currencyId' => array('field' => 'currencyId', 'info' => 'Валюта (DBS) || RUR, BYN, EUR, USD, UAN, KZT', 'default' => 'RUR'),
            //'tv_vat' => array('field' => 'vat', 'info' => 'Ставка НДС', 'default' => 'VAT_20'),
            //'tv_count' => array('field' => 'count', 'info' => 'Доступное количество товара', 'default' => '1'),
            'tv_available' => array('field' => 'available', 'info' => 'Статус товара (DBS) || true — товар в наличии, false — под заказ.', 'default' => ''),
            'tv_store' => array('field' => 'store', 'info' => 'Купить в магазине без заказа (DBS) || Пометка о том, что товар можно купить офлайн, просто придя в магазин.', 'default' => 'true'),
            'tv_delivery' => array('field' => 'delivery', 'info' => 'Доставка (DBS) || Есть ли курьерская доставка.', 'default' => 'true'),
            'tv_pickup' => array('field' => 'pickup', 'info' => 'Самовывоз (DBS) || Доступен ли самовывоз.', 'default' => 'true'),
            'tv_options' => array('field' => 'param', 'info' => 'Характеристики || Кроме общих свойств, у товара есть характеристики, присущие конкретной категории, к которой он относится. Например, у велосипеда есть размер рамы, а детское пюре бывает овощное, мясное или фруктовое.', 'default' => ''),
        );

        return $fields;
    }


    /**
     * @param $key
     * @param $value
     * @return bool
     */
    private function setSetting($key, $value): bool
    {
        /* @var modSystemSetting $Setting */
        $Setting = $this->modx->getObject('modSystemSetting', $key);
        $Setting->set('value', $value);
        $Setting->save();
        $this->modx->cacheManager->refresh(array('system_settings' => array()));
        return true;

    }

}