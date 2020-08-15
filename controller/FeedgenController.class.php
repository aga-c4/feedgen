<?php
/**
 * Created by Konstantin Khachaturyan (aga-c4)
 * @author Konstantin Khachaturyan (AGA-C4)
 * Date: 29.05.20
 * Time: 23:58
 */
 
/**
 * Parser Controller class - контроллер парсеров
 */
class FeedGenController{

    /**
     * @var string - Имя модуля контроллера (Внимание, оно должно соответствовать свойству $thisModuleName фронт контроллера модуля (используется во View)
     */
    public $thisModuleName = '';
    
    public function __construct($thisModuleName) {
        $this->thisModuleName = $thisModuleName;
    }

    /**
     * Метод по-умолчанию
     * @param string $tpl_mode - формат вывода
     * @param bool $console - если true, то вывод в консоль
     */
    public function action_index($tpl_mode='html', $console=false){
        $this->action_help($tpl_mode, $console);//Покажем хелп
    }

    /**
     * Вывод страницы помощи
     * @param string $tpl_mode - формат вывода
     * @param bool $console - если true, то вывод в консоль
     */
    public function action_help($tpl_mode='html', $console=false){
                    
        $help_txt = 'FeedGen module.
-------
Format:
php console.php module=feedgen action=(get/generate) [refer=...] [group=...] [cron=yes] [nocache=yes] [cleartmp=yes]
Don\'t print Space near "="

';

        if ($tpl_mode=='html'){$help_txt = "<pre>$help_txt</pre>";}
        //if ($tpl_mode=='html'){$help_txt = "FeedGen!";}

        //Установим глобальные метатеги для данной страницы
        Glob::$vars['page_title'] = 'Help'; //Метатег title
        Glob::$vars['page_keywords'] = 'Help'; //Метатег keywords
        Glob::$vars['page_description'] = 'Help'; //Метатег description
        Glob::$vars['page_h1'] = 'Help'; //Содержание основного заголовка страницы

        $item = array(); //Массив данных, передаваемых во View
        $item['page_content'] = $help_txt;

        //Запишем конфиг и логи----------------------
        SysBF::putFinStatToLog(); //Запишем конфиг и логи

        //View------------------------
        switch ($tpl_mode) {
            case "html": //Вывод в html формате для Web 
                require_once APP_MODULESPATH . 'default/view/main.php';
                break;
            case "txt": //Вывод в текстовом формате для консоли
                require_once APP_MODULESPATH . 'default/view/txtmain.php';
                break;
            case "json": //Вывод в json формате
                if (!Glob::$vars['console']) header('Content-Type: text/json; charset=UTF-8');
                echo Glob::$vars['json_prefix'] . json_encode($item);
                break;
        }
        
    }
    
    /**
     * Вывод фида
     * @param string $tpl_mode - формат вывода
     * @param bool $console - если true, то вывод в консоль
     */
    public function action_get($tpl_mode='html', $console=false){
        
        SysLogs::$logView = false;
        SysLogs::$errorsView = false;
        SysLogs::$logRTView = false;
        SysLogs::$logSave = false;
        
        //Расширим временные лимиты
        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', 300);
        
        $curTime = time();
        $memoryLimit = ini_get("memory_limit");
        $maxExecutionTime = intval(ini_get("max_execution_time"));
        SysLogs::addLog(date("Y-m-d G:i:s",$curTime) . " Get feed [memoryLimit: $memoryLimit; maxExecutionTime: $maxExecutionTime]");

        require_once (Glob::$vars['module_path'] . 'feedgen/model/Feedgen.class.php');  
  
        $refer = SysBF::checkStr(SysBF::getFrArr(Glob::$vars['request'],'refer',''),'routeitem');
        $reg = SysBF::checkStr(SysBF::getFrArr(Glob::$vars['request'],'reg',''),'routeitem');
        Glob::$vars['feed_secure_key'] = SysBF::checkStr(SysBF::getFrArr(Glob::$vars['request'],'key',''),'string');
        Glob::$vars['no_cache'] = SysBF::checkStr(SysBF::getFrArr(Glob::$vars['request'],'nocache',''),'on');
        
        $feedObj = new Feedgen(Glob::$vars['feed_conf'],$refer, $reg);
        if ($feedObj->validate){
            $feedObj->timeStart = $curTime;  
            if ($feedObj->getParam('save_to_file',false)) $feedObj->loadFile(); //Выдача фида из файла 
            else {
                SysLogs::$logRTSave = true; //Начало записи в лог в реалтайме (надо после конструктора).
                $feedObj->generate(); //Генерация фида
            }
        }
        
        //Запишем конфиг и логи----------------------
        SysBF::putFinStatToLog(); //Запишем конфиг и логи
         
        //View------------------------
        /*
        if ($tpl_mode === 'html') echo "<pre>\n";
        if (SysLogs::getLog()!=''){echo "LOG:\n" . SysLogs::getLog() . "-------\n";}
        if (SysLogs::getErrors()!=''){echo "ERRORS:\n" . SysLogs::getErrors() . "-------\n";}
        if ($tpl_mode === 'html') echo "</pre>\n";
        */
    }
    
    /**
     * Генерация фида
     * @param string $tpl_mode - формат вывода
     * @param bool $console - если true, то вывод в консоль
     */
    public function action_generate($tpl_mode='html', $console=false){
                
        SysLogs::$logView = false;
        SysLogs::$errorsView = false;
        SysLogs::$logRTView = false;
        SysLogs::$logSave = false;
        
        //Расширим временные лимиты
        //if (!Glob::$vars['console']){
            if (!empty(Glob::$vars['feed_conf']['generation_memory_limit'])) ini_set('memory_limit', Glob::$vars['feed_conf']['generation_memory_limit'] . 'M');
            if (!empty(Glob::$vars['feed_conf']['generation_max_execution_time'])) ini_set('max_execution_time', Glob::$vars['feed_conf']['generation_max_execution_time']); 
        //}
        $memoryLimit = ini_get("memory_limit");
        $maxExecutionTime = intval(ini_get("max_execution_time"));
                
        require_once (Glob::$vars['module_path'] . 'feedgen/model/Feedgen.class.php');          
        
        $refer = SysBF::checkStr(SysBF::getFrArr(Glob::$vars['request'],'refer',''),'routeitem');
        $reg = SysBF::checkStr(SysBF::getFrArr(Glob::$vars['request'],'reg',''),'routeitem');
        Glob::$vars['feed_group'] = SysBF::checkStr(SysBF::getFrArr(Glob::$vars['request'],'group',''),'routeitem');
        Glob::$vars['feed_secure_key'] = SysBF::checkStr(SysBF::getFrArr(Glob::$vars['request'],'key',''),'string');
        Glob::$vars['no_cache'] = SysBF::checkStr(SysBF::getFrArr(Glob::$vars['request'],'nocache',''),'on');
        Glob::$vars['feed_by_cron'] = SysBF::checkStr(SysBF::getFrArr(Glob::$vars['request'],'cron',''),'on'); 
        Glob::$vars['feed_cleartmp'] = SysBF::checkStr(SysBF::getFrArr(Glob::$vars['request'],'cleartmp',''),'on'); 
        
        
        $startMode = '';
        if (!empty($refer)) $startMode = ' refer ' . $refer;
        elseif (!empty(Glob::$vars['feed_group'])) $startMode = ' group ' . Glob::$vars['feed_group'];
        elseif (Glob::$vars['feed_by_cron']) $startMode = ' cron';
        else{
            $this->action_help($tpl_mode, $console);//Покажем хелп
            return;
        }
        
        if (Glob::$vars['feed_cleartmp']) $startMode .= ' cleartmp';
        if (Glob::$vars['no_cache']) $startMode .= ' nocache';
        
        $procHash = md5(time().rand(0,9999));
        SysLogs::addLog(date("Y-m-d G:i:s") . " FeedGen start$startMode [memoryLimit: $memoryLimit; maxExecutionTime: $maxExecutionTime]");
        if (Glob::$vars['console']) echo date("Y-m-d G:i:s") . " [$procHash] FeedGen start$startMode [memoryLimit: $memoryLimit; maxExecutionTime: $maxExecutionTime]\n";

        //Сформируем список запускаемых реферов в порядке приоритета: рефер / группа / крон список
        $refersStartList = array();
        $refersFound = array();
        $refersListStr = '';
        $feed_groups = (isset(Glob::$vars['feed_conf']['groups']) && is_array(Glob::$vars['feed_conf']['groups']))?Glob::$vars['feed_conf']['groups']:array();
        
        if (!empty($refer)) {//Задан рефер
            
            $refersStartList[] = array('refer'=>$refer, 'reg'=>$reg);
        
        }elseif(!empty(Glob::$vars['feed_group']) &&  isset($feed_groups[Glob::$vars['feed_group']])) { //Задана группа
            
            if (isset($feed_groups[Glob::$vars['feed_group']]['refers']) && is_array($feed_groups[Glob::$vars['feed_group']]['refers'])){
                foreach ($feed_groups[Glob::$vars['feed_group']]['refers'] as $key=>$value){
                    if (is_array($value)) foreach ($value as $curReg){
                        if (!isset($refersFound["$key$curReg"])){
                            $refersStartList[] = array('refer'=>$key, 'reg'=>$curReg);
                            $refersListStr .= (($refersListStr!=='')?', ':'') . $key . '['.$curReg.']';
                            $refersFound["$key$curReg"] = true;
                        }
                    }
                }
            }
            
        }elseif(Glob::$vars['feed_by_cron']){ //Задан списко по крону
               
            $groupsListStr = '';
            foreach ($feed_groups as $grAlias=>$grArr){
                if (isset($grArr['refers']) && is_array($grArr['refers'])
                        && isset($grArr['cron']) && is_array($grArr['cron']) 
                        //&& SysBF::cronTsValidate(time(), $grArr['cron']) 
                        ){
                    foreach ($grArr['refers'] as $key=>$value){
                        if (is_array($value)) foreach ($value as $curReg){
                            if (!isset($refersFound["$key$curReg"])){
                                $refersStartList[] = array('refer'=>$key, 'reg'=>$curReg);
                                $refersListStr .= (($refersListStr!=='')?', ':'') . $key . '['.$curReg.']';
                                $refersFound["$key$curReg"] = true;
                            }
                        }
                    }
                }
                $groupsListStr .= (($groupsListStr!=='')?', ':'') . $grAlias;
            }
            if (!empty($groupsListStr)){
                SysLogs::addLog(date("Y-m-d G:i:s") . " Found cron groups: {$refersListStr}");
                if (Glob::$vars['console']) echo date("Y-m-d G:i:s") . " [$procHash] Found cron groups: {$refersListStr}\n";
            }

        }
        
        if (!empty($refersListStr)){
            SysLogs::addLog(date("Y-m-d G:i:s") . " Found groups refers: {$refersListStr}");
            if (Glob::$vars['console']) echo date("Y-m-d G:i:s") . " [$procHash] Found groups refers: {$refersListStr}\n";
        }
        
        foreach($refersStartList as $curFeedArr){
            
            $refer = $curFeedArr['refer'];
            $reg = $curFeedArr['reg']; 
        
            $curTime = time();
            SysLogs::addLog(date("Y-m-d G:i:s",$curTime) . " Get feed {$refer}[$reg] $startMode");
            if (Glob::$vars['console']) echo date("Y-m-d G:i:s",$curTime) . " [$procHash] Start feed {$refer}[$reg] $startMode\n";

            $feedObj = new Feedgen(Glob::$vars['feed_conf'],$refer, $reg);
            SysLogs::$logRTSave = true; //Начало записи в лог в реалтайме (надо после конструктора).
            if ($feedObj->validate){
                $feedObj->timeStart = $curTime;
                if ($feedObj->getParam('save_to_file')) {
                    $timeStart = SysBF::getmicrotime();
                    $res = $feedObj->generate();
                    $timeGenerate = sprintf ("%01.4f",(SysBF::getmicrotime() - $timeStart));
                    $memory_peak_usage = intval(memory_get_peak_usage()/1024) . 'kB';
                    $memory_fin_usage = intval(memory_get_usage()/1024) . 'kB';
                    $finMess = date("Y-m-d G:i:s");
                    if (Glob::$vars['console']) $finMess .= " [$procHash]";
                    if ($res) $finMess .= " Fin feed {$refer}[" . $feedObj->getRegAlias() . "] generation finish in {$timeGenerate}s!";
                    else $finMess .= " Fin feed {$refer}[" . $feedObj->getRegAlias() . "] whith errors in {$timeGenerate}s!";                
                    $finMess .=  " Mem peak=[$memory_peak_usage] Mem fin=[$memory_fin_usage]";
                    SysLogs::addLog($finMess);
                    if (Glob::$vars['console']) echo $finMess."\n";
                }else{
                    date("Y-m-d G:i:s",$curTime) . " [$procHash] It's realtime generation feed $refer[$reg].\n";
                }
            }
        
        }
        
        //Запишем конфиг и логи----------------------
        SysBF::putFinStatToLog(); //Запишем конфиг и логи
         
        //View------------------------
        /*
        if ($tpl_mode === 'html') echo "<pre>\n";
        if (SysLogs::getLog()!=''){echo "LOG:\n" . SysLogs::getLog() . "-------\n";}
        if (SysLogs::getErrors()!=''){echo "ERRORS:\n" . SysLogs::getErrors() . "-------\n";}
        if ($tpl_mode === 'html') echo "</pre>\n";
        */      
    }

}

