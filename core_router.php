<?php
/**
 * router.php Маршрутизатор Первичный
 *
 * Created by Konstantin Khachaturyan (aga-c4)
 * @author Konstantin Khachaturyan (AGA-C4)
 * Date: 09.04.15
 * Time: 00:00
 */
//Сформируем строку маршрутизации
if (!empty(Glob::$vars['request']['route_url'])) Glob::$vars['mnbv_route_url'] = SysBF::checkStr(Glob::$vars['request']['route_url'],'url');
else Glob::$vars['request']['route_url'] = '/';

//Разбор URL для определния параметров
Glob::$vars['route_arr'] = array();
if (!empty(Glob::$vars['request']['route_url'])) {
    $route_arr = preg_split("/\//", Glob::$vars['request']['route_url']);
    foreach($route_arr as $key=>$value) {
        if (!empty($value)) {
            $route_arr[$key] = SysBF::checkStr($value,'routeitem');
        } else {
            unset($route_arr[$key]);
        }
    }

    Glob::$vars['route_arr'] = array();
    foreach($route_arr as $value) Glob::$vars['route_arr'][] = $value;

    $request_uri_str = (!empty($_SERVER['REQUEST_URI']))?$_SERVER['REQUEST_URI']:'';
    SysLogs::addLog('REQUEST_URI: ' . $request_uri_str);
    SysLogs::addLog('RouteStr: ' . implode('/',Glob::$vars['route_arr']));
}

//Выберем модуль из пользовательских, если нет, то из основных
Glob::$vars['module_path'] = APP_MODULESPATH;
if (!empty(Glob::$vars['route_arr'][0]) && !empty(Glob::$vars['app_module_alias'][strval(Glob::$vars['route_arr'][0])])) {
    Glob::$vars['module'] = Glob::$vars['app_module_alias'][strval(Glob::$vars['route_arr'][0])];
    Glob::$vars['module_path'] = USER_MODULESPATH;
}elseif (!empty(Glob::$vars['route_arr'][0]) && !empty(Glob::$vars['module_alias'][strval(Glob::$vars['route_arr'][0])])) {
    Glob::$vars['module'] = Glob::$vars['module_alias'][strval(Glob::$vars['route_arr'][0])];
}

//Прямое указание модуля в параметрах
if (!empty(Glob::$vars['request']['module'])) {
    Glob::$vars['module_request'] = SysBF::checkStr(Glob::$vars['request']['module'], 'routeitem');
    if (!empty(Glob::$vars['module_request']) && !empty(Glob::$vars['app_module_alias'][strval(Glob::$vars['module_request'])])) {
        Glob::$vars['module'] = Glob::$vars['app_module_alias'][strval(Glob::$vars['module_request'])];
        Glob::$vars['module_path'] = USER_MODULESPATH;
    }elseif (!empty(Glob::$vars['module_request']) && !empty(Glob::$vars['module_alias'][strval(Glob::$vars['module_request'])])) {
        Glob::$vars['module'] = Glob::$vars['module_alias'][strval(Glob::$vars['module_request'])];
        Glob::$vars['module_path'] = APP_MODULESPATH;
    }
}

SysLogs::addLog('Core router: Module=['.Glob::$vars['module'].']');
