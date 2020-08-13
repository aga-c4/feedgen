<?php
/**
 * main.php Файл инициализации основного модуля системы
 *
 * Created by Konstantin Khachaturyan
 * User: AGA-C4
 * Date: 30.06.2020
 * Time: 23:59
 */
$thisModuleName = Glob::$vars['module'];
$thisModulePath = Glob::$vars['module_path'] = APP_MODULESPATH; //(USER_MODULESPATH) Где находится модуль, он может быть в app и в modules
if (!isset($_SERVER['SERVER_NAME'])) $_SERVER['SERVER_NAME'] = '';
Glob::$vars['current_domen'] = strtolower($_SERVER['SERVER_NAME']);

//Загрузка дефолтовых и пользовательских констант и конфигов. Конфиги работают по принципу замещения.
if(file_exists(USER_MODULESPATH . $thisModuleName . '/config/constants.php')) require_once (USER_MODULESPATH . $thisModuleName . '/config/constants.php');
require_once APP_MODULESPATH . $thisModuleName . '/config/constants.php';

/**
 * Основной загрузчик классов фидогенератора
 * @param $class_name
 */
spl_autoload_register (function ($class_name) {
    if (false===stripos($class_name,'\\')) { //Неймспейсы мы уже обработали в core
        $test = (Glob::$vars['console'] && !empty(Glob::$vars['autoload_console_log_view']))?true:false; //Если true, то в консоли выведет сообщения о загрузке классов
        $class =  APP_MODULESPATH . Glob::$vars['module'] . '/model/' . $class_name . '.class.php';
        if ($test) echo 'Try to load class: ' . $class . "\n";
        if(file_exists($class)) {
            require_once ($class);
            if ($test) echo ' Ok!'; 
        }else{
            if ($test) echo ' Not found!';
        }
        if ($test) echo "\n";
    }
} );

SysLogs::addLog('Start module [feedgen]');

if (!class_exists('MNBVf')) require_once APP_MODULESPATH . $thisModuleName . '/'. MOD_MODELSPATH .'/MNBVf.class.php'; //Если установлена CMS.MNBV, то в кастомном конфиге подключите родной класс CMS
require_once APP_MODULESPATH . 'core/'. MOD_MODELSPATH .'/DbMysql.class.php'; //Класс MySql
require_once APP_MODULESPATH . 'core/'. MOD_MODELSPATH .'/SysStorage.class.php';    //Класс хранилищ данных
if (file_exists(USER_MODULESPATH . $thisModuleName . '/'. MOD_MODELSPATH .'/CustomFeedgenMethods.trait.php'))  {
    SysLogs::addLog('Feedgen: Use CustomFeedgenMethods from APP');
    require_once (USER_MODULESPATH . $thisModuleName . '/'. MOD_MODELSPATH .'/CustomFeedgenMethods.trait.php'); //Пользовательские методы Feedgen, если есть
}elseif (file_exists(APP_MODULESPATH . $thisModuleName . '/'. MOD_MODELSPATH .'/CustomFeedgenMethods.trait.php'))  require_once (APP_MODULESPATH . $thisModuleName . '/'. MOD_MODELSPATH .'/CustomFeedgenMethods.trait.php'); //Пользовательские методы Feedgen, если есть
 
//Загрузка дефолтовых и пользовательских констант и конфигов. Конфиги работают по принципу замещения.
if(file_exists(APP_MODULESPATH . $thisModuleName . '/config/config.php')) require_once ($thisModulePath . $thisModuleName . '/config/config.php');
if(file_exists(USER_MODULESPATH . $thisModuleName . '/config/config.php')) require_once (USER_MODULESPATH . $thisModuleName . '/config/config.php');

//Инициализация БД
SysStorage::setdb('mysql1', Glob::$vars['db_access']);

if(file_exists(USER_MODULESPATH . $thisModuleName . '/router.php'))  require_once (USER_MODULESPATH . $thisModuleName . '/router.php'); //Пользовательский маршрутизатор, если есть
else require_once ($thisModulePath . $thisModuleName . '/router.php'); //Маршрутизатор модуля

$controllerFile =  APP_MODULESPATH . Glob::$vars['module'] . '/' . MOD_CONTROLLERSPATH . $controller . 'Controller.class.php';
if(file_exists($controllerFile)) {

    require_once $controllerFile;
    $controllerName = $controller."Controller";
    $actionName = "action_".$action;

    $controllerObj = new $controllerName($thisModuleName);
    if(method_exists($controllerObj, $actionName))
        $controllerObj->$actionName(Glob::$vars['tpl_mode'],Glob::$vars['console']);
    else
        $controllerObj->action_index(Glob::$vars['tpl_mode'],Glob::$vars['console']);

}else{//Действие при ошибочном контроллере
    SysLogs::addError('Error: Wrong controller ['.$controller.']');
    switch (Glob::$vars['tpl_mode']) {
        case "html": //Вывод в html формате для Web
            require_once APP_MODULESPATH . 'default/view/404.php';
            break;
        case "txt": //Вывод в текстовом формате для консоли
            require_once APP_MODULESPATH  . 'default/view/txtmain.php';
            break;
        case "json": //Вывод в json формате
            if (!Glob::$vars['console']){header('Content-Type: text/json; charset=UTF-8');}
            echo Glob::$vars['json_prefix'] . '{}';
            break;
    }
}
