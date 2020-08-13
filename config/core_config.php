<?php
################################################################################
# Основные переменные
################################################################################
Glob::$vars['db_access'] = array(
    'host' => '127.0.0.1',
    'database' => 'feedgen', 
    'user' => 'root', 
    'password' => '',
    'charset' => 'cp1251',
    'collation' => 'cp1251_general_ci'
);
define("FEEDS_PATH",APP_STORAGEFILESPATH.'feeds/'); //Путь к папке с фидами

Glob::$vars['module'] = 'default'; //загружаемый модуль - по-умолчанию загружаем default TODO - Поменя
//йте, если хотите сделать базовым другой модуль
Glob::$vars['controller'] = null; //исполняемый контроллер
Glob::$vars['action'] = 'index'; //исполняемое действие - по-умолчанию выполняем index

##############################################################################
# Доступные модули системы и их алиасы (ключи - используются при вызове модуля)
##############################################################################
Glob::$vars['module_alias'] = array( //Базовые модули в папке modules
    'core' => 'core', //Ядро системы
    'default' => 'default', //Пустой модуль, для демонстрации
    'parser' => 'parser', //Корневой модуль CMS MNBV
    'feedgen' => 'feedgen', //Модуль API MNBV
);
Glob::$vars['app_module_alias'] = array( //Пользовательские модули в папке app имеют приоритет перез базовыми, при этом в модуле могут быть лишь измененные файлы по отношению к такому же базовому
    //По аналогии с module_alias
);
