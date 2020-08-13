<?php
/**
 * Настройки генератора фидов (Конфиг) 
 * сгруппированы в массиве, который используется классом.
 *
 */
################################################################################
# Основной конфиг генератора фидов
################################################################################
Glob::$vars['feed_conf'] = array(
    "db_charset" => "windows-1251", //Кодировка фида (windows-1251/utf-8)
    "feed_name" => null, //Название фида
    "company_name" => null, //Название компании для фида
    "def_protocol" => "http://", //Протокол по-умолчанию (может быть '//','http:','https:')
    "def_domain" => "", //Домен по-умолчанию для формирования фидов БЕЗ протокола
    "def_domain_img" => "", //Домен по-умолчанию для изображений с протоколом
    "file_prist" => "feed_", //Приставка сохраняемых файлов
    "use_cache" => false, //Включить кеширование
    "cache_lag" => 24*60*60, //Время жизни кеша в секундах
    "params" => array(), //массив параметров текущего фида
    "currency" => "RUR", //Валюта цен фида
    "reg_def_alias" => "default", //Алиас дефолтового фида, будет выбран, если не задан другой
    "def_reg_alias_to" => "msk", //Замена default на это значение в выдачах и именах файлов
    "product_block_maxsize" => 20000, //Максимальное количество товаров в последовательной выгрузке (чтоб не перерасходовать память).
    "max_cat_levels" => 10, //Максимальное количество уровней вложения категорий
    'generation_memory_limit' => '512M', //Допустимый объем используемой памяти при генерации (в режиме html запроса)
    'generation_max_execution_time' => 300, //Допустимое время выполнения скрипта при генерации (в режиме html запроса)
        
);

//Допустимые реферы
if(file_exists(APP_MODULESPATH . $thisModuleName . '/config/refers.php')) require_once ($thisModulePath . $thisModuleName . '/config/refers.php');
if(file_exists(USER_MODULESPATH . $thisModuleName . '/config/refers.php')) require_once (USER_MODULESPATH . $thisModuleName . '/config/refers.php');

//Настройки регионов
if(file_exists(APP_MODULESPATH . $thisModuleName . '/config/regions.php')) require_once ($thisModulePath . $thisModuleName . '/config/regions.php');
if(file_exists(USER_MODULESPATH . $thisModuleName . '/config/regions.php')) require_once (USER_MODULESPATH . $thisModuleName . '/config/regions.php');

//Привязка категорий к категорям гугля ключ - строчный соответствует идентификатору категории.
if(file_exists(APP_MODULESPATH . $thisModuleName . '/config/googlecats.php')) require_once ($thisModulePath . $thisModuleName . '/config/googlecats.php');
if(file_exists(USER_MODULESPATH . $thisModuleName . '/config/googlecats.php')) require_once (USER_MODULESPATH . $thisModuleName . '/config/googlecats.php');

//Массив данных по странам и их кодам разного вида
if(file_exists(APP_MODULESPATH . $thisModuleName . '/config/countries.php')) require_once ($thisModulePath . $thisModuleName . '/config/countries.php');
if(file_exists(USER_MODULESPATH . $thisModuleName . '/config/countries.php')) require_once (USER_MODULESPATH . $thisModuleName . '/config/countries.php');

//Массив данных по странам и их кодам разного вида
if(file_exists(APP_MODULESPATH . $thisModuleName . '/config/cron_list.php')) require_once ($thisModulePath . $thisModuleName . '/config/cron_list.php');
if(file_exists(USER_MODULESPATH . $thisModuleName . '/config/cron_list.php')) require_once (USER_MODULESPATH . $thisModuleName . '/config/cron_list.php');

//Массив перекрывающий стандартный массив определения типа файла для заголовка
Glob::$vars['feed_conf']['mime_arr'] = array(
    'xml' => 'text/xml',
);
