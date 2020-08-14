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
if (!defined("InStock_Donor_3")) define('InStock_Donor_3',32); //- наличие на базовом складе более 2х
if (!defined("InStock_Donor_2")) define('InStock_Donor_2',16); //- наличие на базовом складе 2шт
if (!defined("InStock_Donor_1")) define('InStock_Donor_1',8); //- ограниченное количество на базовом складе 1шт.
if (!defined("InStock_Donor_0")) define('InStock_Donor_0',4); //- товар в пути на базовом складе
if (!defined("InStock_Dealers")) define('InStock_Dealers',2); //- наличие товара у региональных поставщиков
if (!defined("InStock_Production")) define('InStock_Production',1); //- можно заказать производство
if (!defined("InStock_Empty")) define('InStock_Empty',0); //- нет наличия

define("FEEDS_FILESPATH", APP_STORAGEFILEPATH.'feeds/');
define("FEEDS_TMP_FILESPATH", APP_DUMPPATH.'modules/feedgen/');
define("FEEDS_LOGSPATH", APP_LOGSPATH.'feedgen/');
define("FEEDS_CACHEPATH", APP_CACHEPATH.'feedgen/');
