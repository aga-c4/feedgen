<?php
################################################################################
# Конфиг фида по-умолчанию
################################################################################
$feedConfArr = array(
    "alias" => "avito", //Фиды для Яндекс Маркета по регионам
    
    "allow_email" => "Нет",
    "manager_name" => "",
    //"contact_phone" => "", //Либо один телефон на фид, либо массив, где ключ - телефон, а значение - массив корневых категорий для него
    "address" => "",
    "condition" => "Б/у", //Состояние: "Новое" / "Б/у"
    "feed_format" => 'xml', //Формат файла фида (xml,csv,json(пока не доступен))
    //"save_to_file" => true, //При генерации сохраняет в файл, при отдаче забирает из него. Формат "ПРЕФИКС+РЕФЕР+АЛИАСРЕГИОНА.ТИП
    //"type_dnload" => 'browser', //Тип отображения - браузер 'browser' или файл 'file' (по-умолчанию)
    "dtd_string" => '',
    //"secure_key" => '123', //false, //Строка с секретным кодом, которая должна передаваться в параметре key=строка
    "parent_feed" => "default", //array("default"), //Массив алиасов родительских фидов в порядке применения, настройки последующих перекрывают предыдущие
    "use_cache" => true, //Включить кеширование (true/false)
    //"cache_lag" => 1, //Время жизни кеша категорий, вендоров и параметров в секундах
    "use_vendors" => false, //Вывод вендоров: false - не выводить, true - выводить, 
    "use_categories" => false, //Маркер показа списка категорий товаров в хедере фида, если 'all', то выводятся все имеющиеся категории каталога. Товары вне списка - не выводятся
    "use_utm" => true,
    "use_img_utm" => false,
    
    "root_cat_view" => false, //Показывать в каталоге рутовую категорию (по-умолчанию не показывается)
    "root_cat_id" => "1", //Стартовая родительская категория, если 0, то выдаются все категории, если массив, то несколько рутовых категорий
    "only_cat_active" => false,
    
    "price_type" => '', //d2', //Вариант вывода цены: 'd2' - с десятыми; 'google' - 1500.00 RUB (ISO 4217)).
    
    "use_name" => true, //Выводить полное название товара по схеме: true=>"Префикс Вендор Модель"; 'short' => "Вендор Модель", 
    "use_type_prefix" => false, //Выводить префикс имени
    "use_vendor" => false, //Выводить вендор
    "use_model" => false, //Выводить модель
    "use_descr" => "full", //Выдавать ли описание товара (false,true,'full' -  полное)
    "descr_tags" => false, //Заменять перевод строки на <br> и по возможности оставить <ul><li> и <h3>
    "descr_no_s" => false, //Заменять все пробельные символы, включая перевод строки на одиночный пробел
    "descr_cdata" => false, // Использовать CDATA
    
    //"use_minprice" => true, //Использовать минимальную цену для ограничения вывода в фид
    //"minprice" => 50000, //Размер минимальной цены для вывода в фид, если не задано, то берем из общего конфига    
    
    "reg_vals" => array(//Региональные настройки
        "default" => array (//По-умолчанию - используется, если не определено иное для региона.
            "outprod_mask" => (InStock_Regional_3|InStock_Regional_2|InStock_Regional_1), //Маска определения показа товара
            "instock_mask" => (InStock_Regional_3|InStock_Regional_2|InStock_Regional_1), //Маска определения доступности товара
            "qty_mask" => 'real', //Маски наличия по региону и количество которое будет при этом показываться. Или 'real', если надо вывести реальные данные 

            "delivery_options" => false,
            "pickup_options" => false,
            "outlets" => false,
        ),
    ),
    
    //Параметры шаблонов формирования
    "tpl_arr" => array( 
        "header" => "avito_header_tpl.php",
        "footer" => "avito_footer_tpl.php",
        "prod_header" => "blank_tpl.php",
        "prod_footer" => "blank_tpl.php",
        "product" => "avito_product_tpl.php",
    ),
);
        
        