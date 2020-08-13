<?php
################################################################################
# Группы фидов и расписания их запуска при необходимости 
################################################################################
Glob::$vars['feed_conf']['groups'] = array(
    "crone_allmin" => array(
        "refers" => array(
            "yam" => array("msk","spb"), //массив алиасов региона или 'all' для генерации по всем регионам
        ),
        //Время запуска, если не задан элемент, то во всех случаях по данному элементу. Если есть TMP файл по текущему фиду. то генерация не стартует. Если что-то встало, надо руками удалить tmp. Есть режим запуска с предварительным удалением TMP.
        "cron" => array( //Если конфиг запуска по крону, если не задан, то по крону запуска нет.
            //Если не задано ограничение определенного типа, то считается что его нет.
            //"m" => 0, //минута (0 - 59)
            //"h" => 12, //час (0 - 23)
            //"day" => array(28,29), //день месяца (1 - 31)
            //"mon" => 7, //месяц (1 - 12) ИЛИ jan,feb,mar ...
            //"week" => 2020, //день недели (0 - 6) (Воскресенье=0 или 7) ИЛИ sun,mon,tue ... 
        ),
        
        //Временная метка по аналогии с cron может быть целым значением, несколькими значениями, диапазоном, дробью или дробным диапазоном.
        //Примеры временных меток для колонки час:
        // 9	Целое значение	Выполнять в 9am
        // 6,7,10	Несколько значений	Выполнять в 6, 7 и 10am
        // 6-9	Диапазон	Выполнять каждый час между 6-9 AM (включительно)
        // */2	Дробь	Выполнять каждый 2-ой час, т.е. 0 (полночь), 2am, 4am, 6am, и т.д.
        // 3-12/3	Дробный диапазоном	Выполнять каждый 3-ий час между 3am и 12pm, т.е. 3am, 6am, 9am, 12pm
    ),
    "console1" => array(
        "feeds" => array(
            "yam" => array("msk"), //массив алиасов региона или 'all' для генерации по всем регионам
        ),
    ),
);

