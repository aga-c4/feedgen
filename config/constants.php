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

define("FEEDS_FILESPATH", APP_STORAGEFILEPATH.'feeds/');
define("FEEDS_TMP_FILESPATH", APP_DUMPPATH.'modules/feedgen/');
define("FEEDS_LOGSPATH", APP_LOGSPATH.'feedgen/');
define("FEEDS_CACHEPATH", APP_CACHEPATH.'feedgen/');



#################################################################
## Константы системы MNBV
## Подключены, чтоб сработала базовая библиотека MNBVF
#################################################################
if (!defined("MNBVSID")) 
/**
 * Идентификатор технической стабильной сессии на MNBVSID_TTL секунд если нет основного идентификатора PHPSESSID, то он заберется из этого
 */
define("MNBVSID",'MNBVSID');

if (!defined("MNBVSID_TTL")) 
/**
 * Время жизни куки MNBVSID в секундах
 */
define("MNBVSID_TTL",2592000); //30 дней

if (!defined("MNBVSIDSHORT")) 
/**
 * Идентификатор технической стабильной сессии, которая живет только во время текущей сессии
 */
define("MNBVSIDSHORT",'MNBVSIDSHORT');

if (!defined("MNBVSIDLONG")) 
/**
 * Идентификатор сессии персонализации, которая живет максимально долго (до конца эпохи Unix)
 */
define("MNBVSIDLONG",'MNBVSIDLONG');

if (!defined("MNBVSIDLV")) 
/**
 * Дата последнего захода Unix метка времени
 */
define("MNBVSIDLV",'MNBVSIDLV');

if (!defined("MNBVSID_TO_PHPSESSID")) 
/**
 * Если нет идентификатора сессии, а кука MNBVSID существует, то взять идентификатор из этой куки (true/false)
 */
define("MNBVSID_TO_PHPSESSID",true);

if (!defined("MNBV_MAINMODULE")) 
/**
 * Путь к модулю ядра MNBV
 */
define("MNBV_MAINMODULE",'mnbv');

if (!defined("MNBV_PATH")) 
/**
 * Путь к модулю ядра MNBV
 */
define("MNBV_PATH",'modules/mnbv/');

if (!defined("MNBV_DEF_TPL_FOLDER")) 
/**
* Путь к основной папке с шаблонами MNBV 
*/
define("MNBV_DEF_TPL_FOLDER",'templates/');

if (!defined("MNBV_TPL_FOLDER")) 
/**
* Путь к папке с пользовательскими шаблонами MNBV - имеют приоритет перед дефолтовыми. Если идентична основной папке, то работаем без пользовательских шаблонов.
*/
define("MNBV_TPL_FOLDER",MNBV_DEF_TPL_FOLDER);

if (!defined("MNBV_DEF_TPL")) 
/**
 * Шаблон MNBV по-умолчанию
 */
define("MNBV_DEF_TPL",'default');

if (!defined("MNBV_DEF_TPL_PATH")) 
/**
 * Путь к папке с шаблоном по-умолчанию MNBV
 */
define("MNBV_DEF_TPL_PATH", MNBV_TPL_FOLDER . MNBV_DEF_TPL . '/');

if (!defined("MNBV_DEF_SITE_STORAGE")) 
/**
 * Стартовое хранилище сайта MNBV по-умолчанию
 */
define("MNBV_DEF_SITE_STORAGE",'site');

if (!defined("MNBV_DEF_SITE_OBJ")) 
/**
 * Стартовая страница сайта MNBV по-умолчанию
 */
define("MNBV_DEF_SITE_OBJ",1);

//Пути к WWW папкам ---------------------------------------------
if (!defined("WWW_IMGPATH")) 
/**
 * Путь к директории img модуля mnbv
 */
define("WWW_IMGPATH",WWW_SRCPATH.'mnbv/img/');

if (!defined("MNBV_WWW_DATAPATH")) 
/**
 * Путь к директории приложенных файлов хранилищ через http
 */
define("MNBV_WWW_DATAPATH", '/data/');

if (!defined("MNBV_WWW_DATAPATH_SEC")) 
/**
 * Путь к закрытой директории приложенных файлов хранилищ через http
 */
define("MNBV_WWW_DATAPATH_SEC", '/sdata/');
