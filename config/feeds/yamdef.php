<?php
################################################################################
# Конфиг фида по-умолчанию
################################################################################
$feedConfArr = array(
    "alias" => "yamdef", //Фиды для Яндекс Маркета по регионам
    
    //"save_to_file" => true, //При генерации сохраняет в файл, при отдаче забирает из него. Формат "ПРЕФИКС+РЕФЕР+АЛИАСРЕГИОНА.ТИП
    "parent_feed" => "default", //array("default"), //Массив алиасов родительских фидов в порядке применения, настройки последующих перекрывают предыдущие
    
    //"only_country_complete" => true,
    "price_type" => '', //d2', //Вариант вывода цены: 'd2' - с десятыми; 'google' - 1500.00 RUB (ISO 4217)).
    
    "use_descr" => true, //"full", //Выдавать ли описание товара (false,true,'full' -  полное)
    "descr_tags" => false, //Заменять перевод строки на <br> и по возможности оставить <ul><li> и <h3>
    "descr_no_s" => false, //Заменять все пробельные символы, включая перевод строки на одиночный пробел
    "descr_cdata" => false, // Использовать CDATA
    
    "use_delivery" => true, //Выводить тег доставки
    "use_delivery_options" => true, //Если есть, то вывести delivery_options. Выводится при включенном use_delivery
    "use_pickup" => true, // Выводить тег возможен самовывоз (по-умолчанию по состоянию InStock)
    "use_pickup_options" => true, //Если есть, то вывести pickup_options. Выводится при включенном use_delivery
    "use_store" => true, // Выводить тег можно купить в магазине без заказа (по-умолчанию по состоянию InStock)
    
    "reg_vals" => array(//Региональные настройки
        "default" => array (//По-умолчанию - используется, если не определено иное для региона.
            
            "outprod_mask" => 'all', //Маска определения показа товара
            "instock_mask" => (InStock_Regional_3|InStock_Regional_2|InStock_Regional_1), //Маска определения доступности товара
            "qty_mask" => array(InStock_Regional_3 => 3, InStock_Regional_2 => 2, InStock_Regional_1 => 1), //Маски наличия по региону и количество которое будет при этом показываться. Или 'real', если надо вывести реальные данные 

            "delivery_options" => array( 
                "main" => array(
                    'instock_mask' => (InStock_Regional_3|InStock_Regional_2|InStock_Regional_1), //Маска определения доступности товара (Если не задано, то используется маска InStockMask)
                    'check_cost' => true, // Берется из входных данных поле deliv_cost, если не задано, то используются максимальные значения
                    'cost_max' => 0, //Максимальная стоимость в рамках заданного региона.
                    'check_days' => true, // Берется из входных данных поле deliv_days, если не задано, то используются максимальные значения
                    'days_max' => 7, //Максимальное количество дней доставки (можно диапазон 1-3 и т.д.).
                    'days_instock_max' => 1, //Максимальное время заказа товара в наличии (можно диапазон 1-3 и т.д.).
                    'order_before' => 13, //Час заказа, до которого считается текущий день.
                ),
            ),
            
            "pickup_options" => array(
                "main" => array(
                    'instock_mask' => (InStock_Regional_3|InStock_Regional_2|InStock_Regional_1), //Маска определения доступности товара (Если не задано, то используется маска InStockMask)
                    'check_cost' => true, // Берется из входных данных поле pickup_cost, если не задано, то используются максимальные значения
                    'cost_max' => 0, //Максимальная стоимость в рамках заданного региона.
                    'check_days' => true, // Берется из входных данных поле pickup_days, если не задано, то используются максимальные значения
                    'days_max' => 7, //Максимальное количество дней доставки (можно диапазон 1-3 и т.д.).
                    'days_instock_max' => 1, //Максимальное время заказа товара в наличии (можно диапазон 1-3 и т.д.).
                    'order_before' => 13, //Час заказа, до которого считается текущий день.
                ),
            ),
            
            "outlets" => array( //Данные по складам (id партнера, наличие, блокировка вывода...) или false если нет данных по складам (требуется use_outlets=true)
                //"clear_current_array" => true, //Если система встречает подобную запись, то при рекурсивной загрузке данный массив будет пересоздан, а не просто дописан
                "default" => array( //Универсальные настройки для всех складов региона (не выводится как отдельный регион)
                    //"qty_mask" => array(InStock_Regional_3 => 300, InStock_Regional_2 => 200, InStock_Regional_1 => 100), //Или 'real', если надо вывести реальные данные 
                ),
            ),
            
            "store_instock_mask" => (InStock_Regional_3|InStock_Regional_2|InStock_Regional_1),
            
        ),
        
        /*
        "msk" => array(
            "outlets" => array( //Данные по складам
                "main_store" => array( //Настройки по конкретному складу региона (main_store - дефолтовое название основного склада)
                    "id" => "111",
                    //"outprod_mask" => InStock_Regional_3,
                    "qty_mask" => array(InStock_Regional_3 => 3, InStock_Regional_2 => 2, InStock_Regional_1 => 1), //'real',
                ),
            ),
        ),
        
        "spb" => array(
            "delivery_options" => array( //Параметры доставки.
                'main' => array('cost_max' => 30001), //Максимальная стоимость в рамках заданного региона.
            ),

            "pickup_options" => array( //Параметры самовывоза.
                'main' => array('cost_max' => 40001), //Максимальная стоимость в рамках заданного региона.
            ),

            "outlets" => array( //Данные по складам
                "spb_store" => array( //Настройки по конкретному складу региона (main_store - дефолтовое название основного склада)
                    "id" => "222",
                ),
            ),
        ),
         */
    ),
    
);
        
        