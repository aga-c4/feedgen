<?php
################################################################################
# Конфиг фида по-умолчанию
################################################################################
$feedConfArr = array(
    //"clear_current_array" => true, //Если система встречает подобную запись, то при рекурсивной загрузке данный массив будет пересоздан, а не просто дописан
        
    //Общие параметры фида
    "alias" => 'default', //Корневой базовый фид по формату YML
    "feed_format" => 'xml', //Формат файла фида (xml,csv,json(пока не доступен))
    
    "secure_key" => false, //Строка с секретным кодом, которая должна передаваться в параметре key=строка при генерации с http(s)
    "date1" => false, //Дата начала вывода фида в формате "YYYY-MM-DD чч:мм" с http(s)
    "date2" => false, //Дата окончания вывода фида в формате "YYYY-MM-DD чч:мм" с http(s)
    "net_allow" => false, //Сеть для которой допустимо забирать фид в формате 000.000.000.000/24 или полная маска                  
    "secure_gen_key" => false, //Строка с секретным кодом, которая должна передаваться в параметре key=строка при генерации с http(s)
    "net_gen_allow" => false, //Сеть для которой допустимо забирать фид в формате 000.000.000.000/24 или полная маска            
    
    "feed_name" => null, //Название фида {#refer} - заменяется на алиас рефера, {#reg} - на описание региона
    "company_name" => null, //Название компании для фида (если не задано, то берем из основноо конфига)
    "dtd_string" => '<!DOCTYPE yml_catalog>', //<!DOCTYPE yml_catalog SYSTEM "/src/shops.dtd">', //Строка DTD или что еще надо в начале XML фида написать
    "header_add_block" => '', //Блок инструкций фида, который будет добавлен после хедера (общие настройки доставки и т.п. по заданному реферу).
    "footer_add_block" => '', //Блок инструкций фида, который будет добавлен перед футером (общие настройки доставки и т.п. по заданному реферу).
    "parent_feed" => false, //Алиас или массив алиасов родительских фидов в порядке применения, настройки последующих перекрывают предыдущие
    "charset" => "utf-8", //Кодировка фида (windows-1251/utf-8)
    "save_to_file" => false, //При генерации сохраняет в файл, при отдаче забирает из него.
    "add_to_file" => false, //При генерации если true, то будет добавлять в имеющийся файл без пересоздания .
    "save_bak" => false, //При генерации сохранить старый вариант файла фида
    "save_file_name" => '', //Название файла, куда будет сохряняться фид, если не задано, то в стандартный "ПРЕФИКС+РЕФЕР+АЛИАСРЕГИОНА.ТИП"
    "save_to_refer" => false, //Алиас рефера, в файлы которого мы будем выгружать результат генерации (надо чтоб в 1 фид попадали разные данные в разное время).
    "to_file_name" => '', //Название выдаваемого файла
    "type_dnload" => 'file', //Тип отображения - браузер 'browser' или файл 'file' (по-умолчанию)
    "use_categories" => true, //Маркер показа списка категорий товаров в хедере фида, если 'all', то выводятся все имеющиеся категории каталога. Товары вне списка - не выводятся
    "use_custom_cats" => false, //'googlecats.php', //Подгрузить массив специфичных категорий из папки config. Вызов из шаблона товара 
    "root_cat_id" => "1", //Стартовая родительская категория, если 0, то выдаются все категории, если массив, то несколько рутовых категорий
    "root_cat_view" => false, //Показывать в каталоге рутовую категорию (по-умолчанию не показывается)
    "use_vendors" => false, //Вывод вендоров: false - не выводить, true - выводить, 
    "use_products" => true, //Вывод товаров: false - не выводить, true - выводить, 'allReg' - выводить по всем регионам в один фид (последнее чуть позже включится) 
    "use_outlets_list" => false, //Пока не реализовано! Вывод списка всех складов во всех регионах 
    "use_outlets_stock" => false, //Пока не реализовано! Вывод наличия и цены товаров по всем складам
    "use_attr_list" => false, //Пока не реализовано! Вывод блока атрибутов (false/true/limit/filter).
    "reg" => '', //Принудительная установка региона
    "currency" => "RUR", //Валюта цен фида
    "csv_head_cat" => array(), //Ключи массива алиасов и названий полей для формирования csv и json (если все названия пустые, то заголовок не выводится). Если не задано, то выводится через систему шаблонов.
    /*
    "csv_head_cat" => array(
        array("alias"=>"cat_id", "name"=>"cat_id", ), 
        array("alias"=>"cat_parent_id", "name"=>"cat_parent_id"), 
        array("alias"=>"cat_id", "name"=>"cat_name")), //array("ключ"=>"алиас1","ключ2"=>"алиас2"), //Ключи массива алиасов и названий полей для формирования csv и json (если все названия пустые, то заголовок не выводится). Если не задано, то выводится через систему шаблонов.
    */
    "csv_head_prod" => false, //Ключи массива алиасов и названий полей для формирования csv и json (если все названия пустые, то заголовок не выводится). Если не задано, то выводится через систему шаблонов. 
    /*
    "csv_head_prod" => array(
        array("alias"=>"prod_id", "name"=>"prod_id"), 
        array("alias"=>"name", "name"=>"prod_name"), 
        array("alias"=>"price", "name"=>"price"),
        array("name"=>"Static", "value"=>"test")), //array("ключ"=>"алиас1","ключ2"=>"алиас2"), //Ключи массива алиасов и названий полей для формирования csv и json (если все названия пустые, то заголовок не выводится). Если не задано, то выводится через систему шаблонов.            
    */
    "csv_delim" => ',', //Разделитель в csv файле
    "csv_enclosure" => '"', //Экран в csv файле
    "use_cache" => null, //Включить кеширование (true/fals e)
    "cache_lag" => 24*60*60, //Время жизни кеша категорий, вендоров и параметров в секундах
    "save_log" => false, //Записывать лог генерации фида. Формат "ПРЕФИКС+РЕФЕР.log" (Сохраняется последний лог генерации и прошлый).
    "use_utm" => true, // Использование UTM меток
    "use_cat_utm" => true, // Использование UTM меток в URL категорий
    "use_img_utm" => true, // Использование UTM меток в URL изображений
    "utm_add_term" => true, // Добавлять к UTM метке utm_term={$ProductID} (устанавливается автоматически)
    "utm_add_term_type" => 'prodid', //тип идентификаторов для замены (prodid/1cid)
    "utm_add_refer" => false, // Добавлять к UTM метке utm_referfrom={АЛИАС_РЕФЕРА} (устанавливается автоматически)
    "utm_template" => 'utm_source={#refer}&utm_medium=cpc&utm_campaign={#refer}_{#reg}',
    "use_actions" => false, //Маркер показа акций в xml файле по стандарту Яндекса    
    
    //Параметры шаблонов формирования
    "tpl_header" => "yml_header_tpl.php",
    "tpl_footer" => "yml_footer_tpl.php",
    "tpl_cat_header" => "yml_cat_header_tpl.php",
    "tpl_cat_footer" => "yml_cat_footer_tpl.php",
    "tpl_category" => "yml_category_tpl.php",
    "tpl_vend_header" => "yml_vend_header_tpl.php",
    "tpl_vend_footer" => "yml_vend_footer_tpl.php",
    "tpl_vendor" => "yml_vendor_tpl.php",
    "tpl_prod_header" => "yml_prod_header_tpl.php",
    "tpl_prod_footer" => "yml_prod_footer_tpl.php",
    "tpl_product" => "yml_product_tpl.php",
    "tpl_actions_header" => "yml_actions_header_tpl.php",
    "tpl_actions_footer" => "yml_actions_footer_tpl.php",
    "tpl_action" => "yml_action_tpl.php",
    
    //Параметры формирования блока товаров (Параметры с префиксом "use_" регулируют формирование xml фида)
    "offerid_type" => 'prodid', //тип идентификаторов предложений (prodid/1cid)
    "use_prod_id_doptype" => false, //('prefix'/'postfix'/false). Алиас региона в начале ID товара  Пример: <g:id>123_msk</g:id>
    "prod_id_dopstr" => '', //Содержание префикса/постфикса, если не указано, то выводим алиас региона 
    "use_shop_sku" => false, //Выводить тег <shop_sku>
    "use_name" => false, //Выводить полное название товара по схеме: true=>"Префикс Вендор Модель"; 'short' => "Вендор Модель", 
    "name_postfix" => null, //Строка, добавляемая в конце имени
    "use_type_prefix" => true, //Выводить префикс имени
    "use_vendor" => true, //Выводить вендор
    "use_model" => true, //Выводить модель
    "use_manufacturer" => false, //Выводить производителя
    "use_sales_notes" => false, //Отображать строку с sales_notes, если она задана, если false, то не отображать
    "use_warranty" => false, //Выводить Гарантию
    "use_country" => true, //Выводить страну-производитель
    "use_barcode" => false, //Выводить блок EAN кодов товаров
    "use_1c_code" => false, //Выводить блок 1C кодов товаров
    "use_url" => true, // Выводить URL
    "use_descr" => false, //"full", //Выдавать ли описание товара (false,true,'full' -  полное)
    "descr_tags" => false, //Заменять перевод строки на <br> и по возможности оставить <ul><li> и <h3>
    "descr_no_s" => false, //Заменять все пробельные символы, включая перевод строки на одиночный пробел
    "descr_cdata" => false, // Использовать CDATA
    "descr_dop_top" => false, //Текст, который будет добавлен в начало description
    "descr_dop" => false, //Текст, который будет добавлен в конец description
    "use_instock_status_str" => false, //Выводить статус наличя как строку констант, разделенных запятыми
    "use_price" => true,  //Выводить в фид цену
    "price_koef" => false, //Коэффициент, который применяется к цене
    "price_type" => '', //Вариант вывода цены ('d2' - c 2 десятыми, 'google' => ISO 4217 => 1500.00 RUB можно без копеек).
    "use_profit" => false, //Выводить прибыль (true/false)
    "use_profit_pr" => false, //Выводить прибыль в процентах
    "max_profit_pr" => false, //Максимальный показываемый %прибыли или false, если без ограничений
    "use_profit_lvl" => false, //Выводить прибыль в уровнях по возрастанию. false или массив верхних границ уровней array('до 10%'=>0,'от 10%'=>10,'от 20%'=>20,'от 30%'=>30) 
    
    "use_oldprice" => false,  //Выводить в фид старую цену  
    "use_picture" => true, //Выводить изображения
    "use_gpicture" => false, //Изображения товара для  Гугля
    "pic_size" => "big", //Размер картинки (small по-умолчанию, medium, big)
    "pic_nums" => 0, //Количество выводимых изображений, если 0, то все.
    "pic_watermark" => 'all', //'all' - добавлять любую, watermark - добавлять только с вод.знаками, nowatermark - добавлять кроме водяных знаков
        "picSizeView" => false, //!!!Пока не доступно!!! Выводить размер в теге изображений
        "picRealMaxSize" => false, //!!!Пока не доступно!!! Массив максимальных размеров картинки для вывода типа array('w'=>700, 'h'=>700, 'maxside'=>123, 'minside'=>123)
        "picRealMinSize" => false, //!!!Пока не доступно!!! Массив минимальных размеров картинки для вывода типа array('w'=>700, 'h'=>700, 'maxside'=>123, 'minside'=>123)
        
    "use_delivery" => false, //Выводить тег доставки
    "use_delivery_cat" => false, //Выводить категорию доставки
    "use_delivery_options" => false, //Если есть, то вывести delivery_options. Выводится при включенном use_delivery
    "use_pickup" => false, // Выводить тег возможен самовывоз (по-умолчанию по состоянию InStock)
    "use_pickup_options" => false, //Если есть, то вывести pickup_options. Выводится при включенном use_delivery
    "use_store" => false, // Выводить тег можно купить в магазине без заказа (по-умолчанию по состоянию InStock)
    "use_qty" => false, // Выводить тег реального наличия <qty>
    "use_outlets" => false, //Выводить возможность резерва в теге Outlets (настройка в outlets)
    "mark_prods" => false, //Список маркированных продуктов для использования в хуках типа array("1cid"=>array("код1",...),"prid"=>array("код1",...)). "prid" имеет приоритет
    "use_marker" => false, // Выводить тег маркера <marker>
    "use_date_create" => false, // Выводить тег date_create
    "use_date_upd" => false, // Выводить тег date_upd
    "use_date_upd_price" => false, // Выводить тег date_upd_price
    "use_date_upd_photo" => false, // Выводить тег date_upd_photo
    
    //Параметры и замеры
    "use_params" => false, //Вывести в фид параметры (false/true/limit/filter).
    "use_gross_sw" => false, //Получить вес и размер БРУТТО
    "use_netto_sw" => false, //Получить вес и размер НЕТТО
    "use_gross_params" => false, //Выводить параметры БРУТТО в виде параметров
    "use_netto_params" => false, //Выводить параметры НЕТТО в виде параметров
    "gross_to_netto_upd" => false, //Заменить параметры НЕТТО на БРУТТО при отсутствии БРУТТО. Надо включить use_netto_params и use_gross_params    
    "use_gross_dimensions" => false, //Выводить параметры БРУТТО в виде dimensions
    "use_netto_dimensions" => false, //Выводить параметры НЕТТО в виде dimensions
    "use_gross_weight" => false, //Выводить вес БРУТТО в виде раздельных тегов
    "use_netto_weight" => false, //Выводить вес НЕТТО в виде раздельных тегов
    "use_gross_split" => false, //Выводить размеры БРУТТО в виде раздельных тегов
    "use_netto_split" => false, //Выводить размеры НЕТТО в виде раздельных тегов
    
    //Ограничение по количеству товаров в выдаче
    "max_products" => false, //Максимальное количество товаров в фиде
    "sort_by" => false, //Сортировка (prodid,catid,price,cost,profit,sales_rate,gross_profit,price_desc,cost_desc,profit_desc,sales_rate_desc,gross_profit_desc)
        
    //Разрешения вывода в фид (если не соблюдается, то предложение не выводится)
    //выделение группы товаров по габаритам и весу по ИЛИ (один из параметров true, значит условие выполняется. Можно задать верхние и нижние границы. Можно задавать не все параметры
    "max_gross_size_weight" => null, 
    /*
    "max_gross_size_weight" => array(выделение группы товаров по габаритам и весу
        "height" => 179, //высота в см (включая это значение)
        "gsize" => array(96,79), //ширина и глубина (в любом порядке) в см (включая это значение)
        "weight" => 24, //вес в см (включая это значение)    
    ),
     */
    "min_gross_size_weight" => null,
    /*
    "min_gross_size_weight" => array(
        "height" => 179, //высота в см (включая это значение)
        "gsize" => array(96,79), //ширина и глубина (в любом порядке) в см (включая это значение)
        "weight" => 24, //вес в см (включая это значение)    
    ),
     */
    
    "only_prod_active" => true, //Выводить только активные товары (true/false)
    "only_cat_active" => true, //Выводить только активные категории (true/false)
    "only_type_prefix_complete" => false, //Если нет type_prefix, то товар не выводить
    "only_vendor_complete" => false, //Если нет vendor, то товар не выводить
    "only_model_complete" => false, //Если нет model, то товар не выводить
    "only_picture_complete" => false, //Если нет ни одного изображения, то товар не выводить
    "only_country_complete" => false, //Если не задана страна-производитель, то товар не выводить (используется при use_country=true)
    "cat_list_only" => false, //Массив категорий вывода в фид (с учетом вложенных). Значение либо true, либо массив вендоров.
    "vend_list_only"=> false, //Массив вендоров вывода в фид
    "catvend_list_only" => false, //Массив категорий+вендоров вывода в фид (с учетом вложенных).  Ключ - категория, значение - массив вендоров
    "mrc_view" => false,   //Показ товаров с МРЦ ('<=0'-мягк(можно показывать скидку)/'>0'-жестк (нельзя показывать скидку)/'=0'-без/false-без).
    "sales_rate_limit1" => 0, //Нижний порог SalesRate (колич. продаж в сутки) до которого товары в фид не выводятся, если 0, то не применяется
    "sales_rate_limit2" => 0, //Верхний порог SalesRate (колич. продаж в сутки) после которого товары в фид не выводятся, если 0, то не применяется
    "profit_limit1" => false, //Нихний предел прибыли для вывода в фид
    "profit_limit2" => false, //Верхний предел прибыли для вывода в фид
    "prod_delta_ts_from" => false, //Отклонение в секундах от текущего момента измененные товары за которое будут выведены в фид (нужно для дельта фидов)
    "prod_date_from" => false, //Дата последнего изменения товара старше которой товары в фид не выводятся [YYYY-MM-DD hh:mm:ss] (нужно для дельта фидов)
    "prod_list_only" => false, //Список продуктов для вывода в фид типа array("1cid"=>array("код1",...),"prid"=>array("код1",...)). "prid" имеет приоритет 
    "prod_list_replace" => false, //Массив массивов изменений текущих свойств товара с учетом диапазона дат. Отключаем через 'prod_active' => false
    /*
    "prod_list_replace" => array( //Массив массивов изменений текущих свойств товара с учетом диапазона дат. Отключаем через 'prod_active' => false
        array(
            "date1"=>"2019-08-09 00:00", //Не обязательное поле
            "date2"=>"2019-08-08 21:00", //Не обязательное поле
            "1cid"=>array( //Для замены по 1с коду
                "16530"=>array('price'=>10000),
            ),
            "prid"=>array( //Для замены по идентификатору товара
                "16530" => array('price'=>10000),
                "16538" => array('price'=>20000, 'prod_active' => false),
            )
        ),
        //.......
    ),
     */
    
    
    //Исключения вывода в фид (если соблюдается, то предложение не выводится)
    "null_price" => true, //Блокировка вывода товаров с нулевой ценой
    "use_minprice" => true, //Использовать минимальную цену для ограничения вывода в фид
    "minprice" => 100, //Размер минимальной цены для вывода в фид, если не задано, то берем из общего конфига    
    "cat_list_exeptions" => false, //Массив категорий исключений (с учетом вложенных). Значение либо true, либо массив вендоров..
    "vend_list_exeptions"=> false, //Массив вендоров исключений
    "catvend_list_exeptions" => false, //Массив категорий+вендоров исключений (с учетом вложенных). Ключ - категория, значение - массив вендоров
    "gross_required" => false, //Блокировать вывод позиций по которым нет размеров брутто и веса (требуется use_brutto_params=true)
    "netto_required" => false, //Блокировать вывод позиций по которым нет размеров нетто и веса (требуется use_netto_params=true)
    "prod_list_exeptions" => false, //Исключение товаров с ограничением по времени действия 
    /*
    "prod_list_exeptions" => array( //Исключение товаров с ограничением по времени действия
        array(
            "date1"=>"2020-07-24 00:00", //От
            "date2"=>"2020-07-07 00:00", //До
            "1cid"=>array("код1","код2",...),
            "prid"=>array("код1","код2",...)
        ),
    ),
     */
    
    
    //Настройки статусов наличия и вывода товаров
    /* Текущие допустимые статусы
    'InStock_False' //- Данный статус недостижим. При этом статусе inStock всегда будет false
    'InStock_Regional_3' //- наличие на региональном складе более 2х
    'InStock_Regional_2' //- наличие на региональном складе 2шт.
    'InStock_Regional_1' //- окраниченное количество на региональном складе 1шт.
    'InStock_Regional_0' //- товар в пути на региональный склад
    'InStock_Shop_3' //- наличие на витрине в региональне
    'InStock_Shop_2' //- наличие на витрине в региональне
    'InStock_Shop_1' //- ограниченное количество на витрине в регионе
    'InStock_Donor_3' //- наличие в Москве на складе более 2х
    'InStock_Donor_2' //- наличие в Москве на складе 2шт
    'InStock_Donor_1' //- ограниченное количество в Москве на складе 1шт.
    'InStock_Donor_0' //- товар в пути в Москве
    'InStock_Dealers' //- наличие товара у региональных поставщиков
    'InStock_Production' //- можно заказать производство
    'InStock_Empty' //- нет наличия. При этом статусе inStock всегда будет true (в конфиге может быть 'all')
     */
    
    "reg_vals" => array(//Региональные настройки
        //"clear_current_array" => true, //Если система встречает подобную запись, то при рекурсивной загрузке данный массив будет пересоздан, а не просто дописан
        "default" => array (//По-умолчанию - используется, если не определено иное для региона.
            //Настройки URL для рефера, они могут быть различными для разных реферов, это полезно, когда работаем с многосайтовой системой.
            "protocol" => null, //Протокол по-умолчанию (может быть '//','http://','https://')
            "domain" => null, //Домен по-умолчанию для формирования фидов (если не задано, то берем из основноо конфига)
            "domain_img" => null, //Домен по-умолчанию для изображений (если не задано, то берем из основноо конфига)
            "uri_prefix" => null, //Региональный префикс после домена
            "uri_postfix" => null, //Региональный постфикс после урла
        
            "outprod_from_outlets" => false, //Взять статус отображения суммарный из складов региона
            "instock_from_outlets" => false, //Взять статус наличия суммарный из складов региона
            "instqty_from_outlets" => false, //Взять количество суммарное из складов региона
            
            "outprod_mask" => 'all', //Маска определения показа товара 
            "instock_mask" => (InStock_Regional_3|InStock_Regional_2|InStock_Regional_1), //Маска определения доступности товара
            "qty_mask" => array(InStock_Regional_3 => 3, InStock_Regional_2 => 2, InStock_Regional_1 => 1), //Маски наличия по региону и количество которое будет при этом показываться. Или 'real', если надо вывести реальные данные 
            "store_instock_mask" => (InStock_Regional_3|InStock_Regional_2|InStock_Regional_1), //Для значения тега <store>
            "pickup_instock_mask" => (InStock_Regional_3|InStock_Regional_2|InStock_Regional_1), //Для значения тега <pickup>
            
            "delivery_options" => array(
                "main" => array(
                    'instock_mask' => (InStock_Regional_3|InStock_Regional_2|InStock_Regional_1),
                    'check_cost' => true, // Берется из входных данных поле delivery_cost, если не задано, то используются максимальные значения
                    'cost_max' => 0, //Максимальная стоимость в рамках заданного региона.
                    'check_days' => true, // Берется из входных данных поле delivery_days, если не задано, то используются максимальные значения
                    'days_max' => 7, //Максимальное количество дней доставки (можно диапазон 1-3 и т.д.).
                    'days_instock_max' => 1, //Максимальное время заказа товара в наличии (можно диапазон 1-3 и т.д.).
                    'order_before' => 13, //Час заказа, до которого считается текущий день.
                )
            ),
            
            "pickup_options" => array(
                "main" => array(
                    'instock_mask' => (InStock_Regional_3|InStock_Regional_2|InStock_Regional_1),
                    'check_cost' => true, // Берется из входных данных поле pickup_cost, если не задано, то используются максимальные значения
                    'cost_max' => 0, //Максимальная стоимость в рамках заданного региона.
                    'check_days' => true, // Берется из входных данных поле pickup_days, если не задано, то используются максимальные значения
                    'days_max' => 7, //Максимальное количество дней доставки (можно диапазон 1-3 и т.д.).
                    'days_instock_max' => 1, //Максимальное время заказа товара в наличии (можно диапазон 1-3 и т.д.).
                    'order_before' => 13, //Час заказа, до которого считается текущий день.
                )
            ),
            
            "outlets" => array( //Данные по складам (id партнера, наличие, блокировка вывода...) или false если нет данных по складам
                //"clear_current_array" => true, //Если система встречает подобную запись, то при рекурсивной загрузке данный массив будет пересоздан, а не просто дописан
                "default" => array( //Универсальные настройки для всех складов региона (не выводится как отдельный регион)
                    //"qty_mask" => array(InStock_Regional_3 => 30, InStock_Regional_2 => 20, InStock_Regional_1 => 10), //Или 'real', если надо вывести реальные данные 
                ),
            ),
        ),
        
        /*
        'msk' => array(
            "outlets" => array( //Данные по складам
                "main_store" => array( //Настройки по конкретному складу региона (main_store - дефолтовое название основного склада)
                    "id" => "1",
                ),
            ),
        ),
        
        'spb' => array(
            "delivery_options" => array( //Параметры доставки.
                'main' => array('cost_max' => 3001), //Максимальная стоимость в рамках заданного региона.
            ),

            "pickup_options" => array( //Параметры самовывоза.
                'main' => array('cost_max' => 4001), //Максимальная стоимость в рамках заданного региона.
            ),

            "outlets" => array( //Данные по складам
                "spb_store" => array( //Настройки по конкретному складу региона (main_store - дефолтовое название основного склада)
                    "id" => "2",
                ),
            ),
        )
         */
    ),
    
    "custom_params" => null, //Если задан массив, то из него будут забраны статические параметры для заданных категорий и вендоров
    /* Формат custom_params:
    "customParams" => array( //Дополнительные параметры для категорий (с учетом вложенности) и вендоров по ИЛИ.
        array(
            "param" => array('id'=>'Нужна обрешетка', 'code'=>'nuzhna_obreshetka', 'name'=>'Нужна обрешетка', 'value'=>'да'),
            "cat_ids" => array(12146), //Условие вывода по категориям
            "vend_ids" => array("cisco"), //Условие вывода по вендорам
            "catvend_ids" => array("23847"=>array("cisco")), //Условие вывода по категориям+вендорам
        ),
    ),
     */

    "ya_actions" => null, //Выводимые акции Яндекса (с учетом доступности участвующих в них товаров)
    //Важно!!! Цена товара для акций со скидками берется из старой цены, старая цена не выводится, а снижение будет за счет акции!
    /* Формат ya_actions:
    "ya_actions" => array(
       
       array(
            "id" => "flash_discount1",
            "regions" => array("default",...),
            "type" => "flash discount",
            "start" => "2020-07-25 00:00",
            "end" => "2020-07-25 12:00",
            "description" => "Положите в корзину - получите скидку!",
            "url" => "https://ДОМЕН.ru/promos/black_friday/",
            //"products" => array( //Если так, то скидка берется из oldprice
            //    'prid' => array("16530","16538"),
            //    '1cid' => array("16530","16538")
            //),
            "products_discount" => array( //Если так, то скидка берется из этого массива, имеет приоритет перед products, если используется эта схема, то не используется products
                'prid' => array("16530"=>20000,"16538"=>30000), //"prid" имеет приоритет
                '1cid' => array("16530"=>40000,"16538"=>50000),
            )
        ),
            
       array( //При покупке n m в подарок
            "id" => "Promo2Plus1",
            "regions" => array("default",...),
            "type" => "n plus m",
            "start" => "2020-07-25 00:00",
            "end" => "2020-07-25 12:00",
            "description" => "Купи 2 упаковки корма и получи третью в подарок!",
            "url" => "https://ДОМЕН.ru/promos/2plus1",
            "required_quantity" =>2, 
            "free_quantity" => 1,
            "products" => array(
                'prid' => array("16530","16538"),
                '1cid' => array("16530","16538"),
            ),
            "categories" => array("23846"),
        ),

        array( //Скидка по промокоду
            "id" => "promocode1",
            "regions" => array("default",...),
            "type" => "promo code",
            "start" => "2020-07-25 00:00",
            "end" => "2020-07-25 12:00",
            "description" => "Скидки в Чёрную Пятницу по промокоду BLFR!",
            "url" => "https://ДОМЕН.ru/promos/black_friday/",
            "promo-code" => "BLFR",
            "discount" => array("unit"=>"percent","val"=>10), //array("unit"=>"currency","currency"=>"RUR","val"=>300),
            "products" => array(
                'prid' => array("16530","16538"),
                '1cid' => array("16530","16538"),
            ),
            "categories" => array("23846"),
        ),

        array( //Подарки
            "id" => "gifts1",
            "regions" => array("msk"),
            "type" => "gift with purchase",
            "start" => "2020-07-25 00:00",
            "end" => "2020-07-30 12:00",
            "description" => "Количество подарков ограничено!",
            "url" => "https://ДОМЕН.ru/promos/black_friday/",
            "required_quantity" => 1, //Количество товаров (в штуках), которое нужно приобрести, чтобы получить подарок. Можно указывать только числовые значения. Максимально допустимое значение — 24. Значение по умолчанию — 1 (один товар).
            "products" => array(
                'prid' => array("16530","16538"),
                '1cid' => array("16530","16538")
            ),
            "gifts" => array(
                'prid' => array("16530","16538"),
                '1cid' => array("16530","16538")
            ),
        ),
    
    ),
    */

);
        
        