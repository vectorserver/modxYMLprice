<?php
/*docs fields: https://yandex.ru/support/marketplace/assortment/fields/index.html*/
//build 0.2.123

class modxYMLprice
{
    /* @var modX $modx */
    public $modx;
    public $config = array();
    public $offerTpl;


    /**
     * @param $modx
     * @param $config
     */
    public function __construct(&$modx, $config)
    {
        $this->modx = $modx;


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

        $this->offerTpl = $this->getOfferTpl();


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

        $categories[] = "\t\t\t<category id=\"{$rootCat->id}\">{$rootCat->pagetitle}</category>";

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

        $offers_key_mapping = str_replace(array("\r", "\n", "\t", " "), "", trim($this->config['offers']['offers_key_mapping']));
        $key_mapping = explode("||", $offers_key_mapping);


        //Подготовка полей из настроек
        $prepare_tags = array();
        foreach ($key_mapping as $key_item) {
            $m_arr = explode("==", $key_item);
            $yml_tag = $m_arr[0];
            $field_resource = $m_arr[1];

            $types = array("tv_", "ms_");
            $typeCheck = mb_substr($field_resource, 0, 3);
            $typeField = in_array($typeCheck, $types) ? $typeCheck : "resource";

            if ($typeField != "resource") {
                $field_resource = mb_substr($field_resource, 3);
            }

            //bool
            $typeField = (is_bool($field_resource) || $field_resource == "true" || $field_resource == "false") ? "bool" : $typeField;


            $prepare_params[$typeField][] = $field_resource;
            //key - tag yml
            //type - resource,tv,msproduct,boo
            $prepare_tags[$yml_tag] = array("type" => $typeField, "field" => $field_resource);
        }


        $params_offers = array(
            'fastMode' => '1',
            'sdhowParentroot' => '1',
            'select' => 'id,class_key',
            'hideContainers' => 1,
        );

        $offers_data = $this->getData($params_offers);
        $outputData = [];
        foreach ($offers_data as $offer_item) {
            /* @var msProduct $product */
            /* @var modResource $product */
            $product = $this->modx->getObject($offer_item->class_key, $offer_item->id);

            $gotoXmlArray = array();
            //$gotoXmlArray['id'] = $product->id;
            $gotoXmlArray['name'] = $product->pagetitle;
            $gotoXmlArray['description'] = $product->description;
            $gotoXmlArray['url'] = $this->config["shop"]["shop_url"] . $product->uri;
            $gotoXmlArray['categoryId'] = $product->parent;;

            if ($offer_item->class_key == 'msProduct') {
                $data = $product->toArray();


                foreach ($data as $typeKey => $value) {
                    if ($value) {
                        switch ($typeKey) {
                            case "vendor.name";
                                $gotoXmlArray["vendor"] = $value;
                                break;
                            case "article";
                                $gotoXmlArray["vendorCode"] = $value;
                                break;
                            case "price";
                                $gotoXmlArray["price"] = $value;
                                break;
                            case "old_price";
                                $gotoXmlArray["oldprice"] = $value;
                                break;
                            case "image";
                                $gotoXmlArray["picture"] = $this->config["shop"]["shop_url"] . $value;
                                break;
                            case "vendor.country";
                                $gotoXmlArray["country_of_origin"] = $value;
                                break;

                            case "weight";
                                $gotoXmlArray["weight"] = $value;
                                break;

                        }
                    }
                }

                //options
                $optionsData = $product->getOne('Data');
                $options = $optionsData->get('options');
                if ($options) {
                    foreach ($options as $optKey => $iptem) {
                        $opt = array("name" => $data[$optKey . ".caption"], "#text" => $iptem[0]);
                        $gotoXmlArray["param"][] = $opt;
                    }
                }

            }

            //other product
            foreach ($prepare_tags as $tagXml => $item) {

                if ($item["type"] == "tv_") {

                    $tvtvalue = $product->getTVValue($item['field']);
                    if ($tvtvalue) {
                        /* @var modTemplateVar $tvs */
                        $tvData = $this->modx->getObject('modTemplateVar', array('name' => $item['field']));

                        if ($tvData && $tvData->get('output_properties')["delimiter"] == "||") {
                            $tvOpt = explode('||', $tvtvalue);

                            $tmpOpt = array();
                            foreach ($tvOpt as $iparam) {
                                $tmpOpt[] = array("name" => $tvData->get('caption'), "#text" => $iparam);

                            }

                            $tvtvalue = $tmpOpt;

                        }

                        $gotoXmlArray[$tagXml] = $tvtvalue;
                    }


                } else if ($item["type"] == "bool") {

                    if ($item["field"]) {
                        $gotoXmlArray[$tagXml] = $item["field"];
                    }


                } else {
                    if ($product->get($item['field'])) {
                        $gotoXmlArray[$tagXml] = $product->get($item['field']);
                    }


                }

                //Migx images
                if ($tagXml == "picture") {

                    $im_tmp = $gotoXmlArray[$tagXml];


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

                        $gotoXmlArray[$tagXml] = $this->config["shop"]["shop_url"] . array_shift($images);
                    }
                }

            }


            //def
            $gotoXmlArray['currencyId'] = $this->config["shop"]["shop_currencyId"];
            //$gotoXmlArray['delivery'] = 'true';
            //$gotoXmlArray['pickup'] = 'true';
            $outputData[$product->id] = $gotoXmlArray;
        }

        //render
        $offers = array();
        foreach ($outputData as $offer_id => $item_data) {
            $offerTags = "";
            foreach ($item_data as $tag => $value) {
                if ($value) {
                    if ($tag == "name" || $tag == "description") {
                        $value = trim($value);
                        $value = strip_tags($value);
                        $value = htmlspecialchars($value);
                        $value = "<![CDATA[{$value}]]>";
                    }

                    if ($tag == "param" && is_array($value)) {
                        foreach ($value as $param_item) {
                            //<param name="Мощность">750 Вт</param>
                            $offerTags .= "\n\t\t\t\t<param name='{$param_item["name"]}'>{$param_item["#text"]}</param>";
                        }
                        unset($item_data['param']);
                    } else {
                        $offerTags .= "\n\t\t\t\t<$tag>{$value}</$tag>";
                    }
                }


            }

            $offers[] = "\t\t\t<offer id=\"{$offer_id}\">{$offerTags}\n\t\t\t</offer>";

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
     * @return array[]
     */
    public function getOfferTpl(): array
    {


        return [
            "id" => "9012",
            "bid" => "80",
            "name" => "Мороженица Brand 3811",
            "vendor" => "Brand",
            "vendorCode" => "A1234567B",
            "url" => "http://best.seller.ru/product_page.asp?pid=12345",
            "price" => "8990",
            "oldprice" => "9990",
            "enable_auto_discounts" => "true",
            "currencyId" => "",
            "categoryId" => "101",
            "vat" => "VAT_20",
            "picture" => "http://best.seller.ru/img/model_12345.jpg",
            "delivery" => "true",
            "pickup" => "true",
            "delivery-options" => [
                "option" => [
                    "cost" => "300",
                    "days" => "1",
                    "order-before" => "18"
                ]
            ],
            "pickup-options" => [
                "option" => [
                    "cost" => "300",
                    "days" => "1-3"
                ]
            ],
            "store" => "true",
            "description" => "<h3>Мороженица Brand 3811</h3><p>Это прибор, который придётся по вкусу всем любителям десертов и сладостей, ведь с его помощью вы сможете делать вкусное домашнее мороженое из натуральных ингредиентов.</p>",
            "sales_notes" => "Необходима предоплата.",
            "manufacturer_warranty" => "true",
            "country_of_origin" => "Китай",
            "barcode" => "4601546021298",
            "param" => [
                "name" => "Цвет",
                "#text" => "белый"
            ],
            "weight" => "3.6",
            "dimensions" => "20.1/20.551/22.5",
            "count" => "100"
        ];
    }


}