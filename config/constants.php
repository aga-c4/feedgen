<?php
/**
 * constants.php Основные константы фидогенератора
 */
if (!defined("InStock_False")) define('InStock_False',8192); //- Данный статус недостижим, поэтому приводит к InStock=false
if (!defined("InStock_Regional_3")) define('InStock_Regional_3',4096); //- наличие на региональном складе более 2х
if (!defined("InStock_Regional_2")) define('InStock_Regional_2',2048); //- наличие на региональном складе 2шт.
if (!defined("InStock_Regional_1")) define('InStock_Regional_1',1024); //- окраниченное количество на региональном складе 1шт.
if (!defined("InStock_Regional_0")) define('InStock_Regional_0',512); //- товар в пути на региональный склад
if (!defined("InStock_Shop_3")) define('InStock_Shop_3',256); //- наличие на витрине в региональне
if (!defined("InStock_Shop_2")) define('InStock_Shop_2',128); //- наличие на витрине в региональне
if (!defined("InStock_Shop_1")) define('InStock_Shop_1',64); //- ограниченное количество на витрине в регионе
if (!defined("InStock_Donor_3")) define('InStock_Donor_3',32); //- наличие в Москве на складе более 2х
if (!defined("InStock_Donor_2")) define('InStock_Donor_2',16); //- наличие в Москве на складе 2шт
if (!defined("InStock_Donor_1")) define('InStock_Donor_1',8); //- ограниченное количество в Москве на складе 1шт.
if (!defined("InStock_Donor_0")) define('InStock_Donor_0',4); //- товар в пути в Москве
if (!defined("InStock_Dealers")) define('InStock_Dealers',2); //- наличие товара у региональных поставщиков
if (!defined("InStock_Production")) define('InStock_Production',1); //- можно заказать производство
if (!defined("InStock_Empty")) define('InStock_Empty',0); //- нет наличия

//Внимание! Значения констант используются при формировании пути к шаблонам вывода!
//define('FIDTYPE_Google','google');
//define('FIDTYPE_FB','facebook');
//define('FIDTYPE_Addigital','addigital');
define('FIDTYPE_YandexMarket','yml');
//define('FIDTYPE_Beru','beru');
//define('FIDTYPE_MMarket','mmarket');
//define('FIDTYPE_ICML','icml');
//define('FIDTYPE_Wikimart','wikimart');
//define('FIDTYPE_Criteo','criteo');
define('FIDTYPE_CSV','csv');
define('FIDTYPE_JSON','json');
//define('FIDTYPE_SiteMap','sitemap');

define("FEEDS_FILESPATH", APP_STORAGEFILEPATH.'feeds/');
define("FEEDS_TMP_FILESPATH", APP_DUMPPATH.'modules/feedgen/');
define("FEEDS_LOGSPATH", APP_LOGSPATH.'feedgen/');
define("FEEDS_CACHEPATH", APP_CACHEPATH.'feedgen/');
