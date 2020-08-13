<?php
/**
 * router.php Маршрутизатор модуля default
 *
 * Created by Konstantin Khachaturyan
 * User: AGA-C4
 * Date: 09.04.15
 * Time: 16:53
 */

//if (!empty(Glob::$vars['request']['module'])) { Glob::$vars['module'] = Glob::$vars['request']['module'];}
if (!empty(Glob::$vars['request']['controller'])) Glob::$vars['controller'] = Glob::$vars['request']['controller']; 
else Glob::$vars['controller'] = SysBF::trueName(Glob::$vars['module'],'title');
if (!empty(Glob::$vars['request']['action'])) Glob::$vars['action'] = Glob::$vars['request']['action'];

//Выберем модуль из пользовательских, если нет, то из основных
if (!empty(Glob::$vars['route_arr'][1])) {
    Glob::$vars['action'] = strval(Glob::$vars['route_arr'][1]);
}
$controller = Glob::$vars['controller'];
$action = Glob::$vars['action'];

SysLogs::addLog('FeedGen router: Module=['.Glob::$vars['module'].'] Controller=['.$controller.'] Action=['.$action.']');