# Компонент modxYMLprice v 0.1.123 beta1
Выгрузка данных в формат Яндекс Маркет (YML, XML)

DOWNLOAD: https://github.com/vectorserver/modxYMLprice/raw/1e38091b833ecb773713bf6dbe9fee9b1c36dbf6/_build/dist/modxymlprice-0.2.123-pl.transport.zip

Компонент modxYMLprice v 0.1.123 beta1

Выгрузка данных в формат Яндекс Маркет (YML, XML)

После установки и настройки компонента можно проверить по адресу https://mysite.ru/modxYMLprice.xml
Также доступны параметры parents (ID категории) https://mysite.ru/modxYMLprice.xml?parents=8

Параметры сопоставления полей в настройках https://mysite.ru/manager/index.php?a=system/settings&ns=modxYMLprice

Примеры вызова:
Прямой: https://mysite.ru/modxYMLprice.xml или https://mysite.ru/modxYMLprice.xml?parents=8
В виде сниппета: [[!modxYMLprice_snippet? &parents=`8`]]
 - Также работают все параметры из сниппета pdoResources
 [[!modxYMLprice_snippet? &limit=`10` &parents=`8`]]



Сопоставление ключей TV или опций msProduct (offers_key_mapping)

 - Поле name == pagetitle, это значит что данные будут браться из заголовка документа
 - Поле price == tv_price, это значит что данные будут подгружаться с TV price (! Важно, префикс tv_ обязателен)
 - Поле price == ms_price, аналогично tv_price, теперь данные будут тянуться из msProduct (При условии, если установлен miniShop2)
 
Конфиг по умолчанию:
	name==pagetitle||
	description==description||
	price==ms_price||
	oldprice==ms_oldprice||
	picture==tv_slider_up||
	vendor==ms_vendor||
	weight==ms_weight||
	color==ms_color||
	dimensions==ms_size||
	country_of_origin==ms_made_in||
	vendorCode==ms_article||
	delivery==true||
	pickup==true
	
ID родительского каталога (shop_catalog_id)	
 - Родитель каталога, можно перечислять парамеры через запятую
 
Валюта (shop_currencyId)
 - По умолчанию RUR
 + В бедующем планирую добавить конвертирование в другие валюты
 
Название вашего магазина (shop_name)
  - По умолчанию: [[++site_name]]
  
Название вашего магазина (shop_company)
  - По умолчанию: Компания - [[++site_name]]
  
Сниппет обработчик(snippet_handler)
  - По умолчанию: Компания - pdoResources

 

