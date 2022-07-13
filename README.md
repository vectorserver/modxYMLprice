Компонент **modxYMLprice**

Данный компонет предназначен дла генерации XML прайса в формате Yandex YML (рис. 1)

**Вывод информации через сниппет:**
```php
[[!modxYMLprice_snippet]]
```
```php
[[!modxYMLprice_snippet? &parents=`8`]]
```
```php
[[!modxYMLprice_snippet? &parents=`8` &context=`web`]]
```
По умолчанию для обработки данных `modxYMLprice_snippet_handler` используется [pdoResources](https://docs.modx.pro/komponentyi/pdotools/snippetyi/pdoresources "pdoResources"), так что все параметры этого снниппета работают.

**Настройка**
На данные момент существует 2 страницы: (рис.2 , рис.3)
  **Страница сопоставления** полей (http://localhost/manager/index.php?a=index&panel=main&namespace=modxYMLprice) и **Системные настройки** (http://localhost/manager/index.php?a=system/settings&ns=modxYMLprice)

- **Страница сопоставления** тут все просто, сопоставляем поле ресурса с полем (tag - yml), (image)
	- `pagetitle` - Заголовок ресурса
	`ms_price` - цена товара из minishop2 (префикс поля `ms_`)
	`tv_price` - цена товара дополнительного поля (TV)  (префикс поля `tv_`)

- **Системные настройки**
	 `modxYMLprice_offers_key_mapping` - настрйки сопоставления полей в формате JSON
	 `modxYMLprice_shop_catalog_id` - ID родительского каталога
	 `modxYMLprice_shop_company` - !Компания - [[++site_name]]
	 `modxYMLprice_shop_currencyId` - RUR
	 `modxYMLprice_shop_name` - Название вашего магазина
	 `modxYMLprice_shop_platform` - MODX Revolution [[++settings_version]] ([[++settings_distro]])
	 `modxYMLprice_shop_url` - Адрес сайта магазина, записанный согласно стандарту RFC 3986 [[++site_url]]
	 modxYMLprice_snippet_handler - Сниппет обработчик, По умолчанию pdoResources

