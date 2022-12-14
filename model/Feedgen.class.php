<?php
/**
 * Класс работы с фидами
 *
 * Created by Konstantin Khachaturyan (aga-c4)
 * @author Konstantin Khachaturyan (AGA-C4)
 * Date: 29.05.20
 * Time: 23:58
 */

class Feedgen {
    
    use CustomFeedgenMethods;
    
    
    /**
     * @var int время начала генерации фида 
     */
    private $startTs = 0;
    
    /**
     * @var type статус валидации конфига фида 
     */
    public $validate = false;
    
    /**
     * @var string формат фида
     */
    private $feedFormat = 'xml';
    
    /**
     * @var string протокол 
     */
    private $protokol = "//"; //Протокол по-умолчанию (может быть '//','http:','https:')
    
    /** 
     * @var string Базовый URL для фида
     */
    private $imgDomain = '';

    /**
     * @var string Базовый URL для фида
     */
    private $baseDomain = '';

    /**
     * @var string Окончание URL при необходимости
     */
    private $baseUrlDop = '';
    
    /**
     * @var string алиас региона 
     */
    private $regAlias = '';
    
    public function getRegAlias(){
        return $this->regAlias;
    }

    /**
     * @var string алиас рефера 
     */
    private $refer = '';
    
    /**
     * @var string валюта фида 
     */
    private $currency = '';
    
    /**
     * @var string название сохраняемого файла
     */
    private $tmpFileName = '';
    
    /**
     * @var string название сохраняемого файла
     */
    private $saveFileName = '';
    
    /**
     * @var string если не пусто, то имена файлов будут формироваться на базе этого алиаса рефера 
     */
    private $toRefer = '';
    
    /**
     * @var type название сохраняемого файла
     */
    private $saveFileLink = '';
    
    /**
     * @var string строка меток, которая будет добавлена к URL товара
     */
    private $utm = '';
    
    /**
     * @var boolean Включить кеширование
     */
    private $useCache = false;
    
    /**
     * @var int Время жизни кеша в секундах
     */
    private $сacheLag = 14400; //4*60*60;
    
    /**
     * @var array массив маркеров видимости акций Ключ акции => true/false 
     */
    private $actionsView = array();
    
    /**
     * @var array массив с найденными товарами по акции (формируется в процессе вывода товаров из наличия и установленного oldprice)
     * Массив типа: {"id":{"id":"Идентификатор_акции","prid":["id1":price,"id2":price,...]}}
     * Служебный фид, генерится в процессе вывода блока товаров и наполняется ценами из price, в товаре вместо price выводится oldprice.
     * Очищается после вывода блока акции для последующего использования в новом генерируемом фиде в рамках текущего процесса
     */
    private $actionsProds = null;
    
    /**
     * @var array массив найденных категорий по аналогии с $actionsProds, вместо цены - идентификатор категории
     */
    private $actionsСats = null;

    /**
     * @var array массив найденных подарков для акций по аналогии с $actionsProds
     */
    private $actionsGifts = null;

    /**
     * @var array массив категорий типа ("id"=>array("id"=>,"parent_id"=>,"name"=>,"alias"=>,"list"=>array("id1","id2","id3"....),"attr"=>array("id1","id2","id3"....))) 
     */
    private static $catArr = null;
    
    /**
     * @var array массив значений атрибутов товаров с их свойствами. Формат {"{prodid}"=>{"prodid"=>"1","typeid"=>123, "value"=>""}}
     */
    private static $prodAttr = null;
    
    /**
     * @var array массив видов атрибутов товаров с их свойствами. Формат {"{attrid}"=>{"id"=>"1","sort"=>100,"name"=>"","alias"=>"","filter"=>false,"type"=>"(value/list)","short"=>false,"vals_list"=>array("id"=>123,"alias"=>"","value"=>...)}}
     */
    private static $prodAttrType = null;
    
    /**
     * @var array массив вендоров
     */
    private static $vendArr = null;
    
    /**
     * @var array массив вендоров
     */
    private $vendView = null;
    
    /**
     * @var type настройки по наличию и доставке для текущего региона 
     */
    private $regVals = array();   
    
    /**
     * @var array массив данных по складам, или false, если нет данных 
     */
    private $outlets = false;

    /**
     * @var array массив деф размеров и веса БРУТТО категорий типа ("catId1"=>array('weight_unit'=>'кг','size_unit'=>'см','weight' => 0,'length' => 0,'width' => 0,'height' => 0},"catId2"=>false, ...)))
     */
    private $grossCatDef = array();

    /**
     * @var array массив деф размеров и веса НЕТТО категорий типа ("catId1"=>array('weight_unit'=>'кг','size_unit'=>'см','weight' => 0,'length' => 0,'width' => 0,'height' => 0},"catId2"=>false, ...)))
     */
    private $nettoCatDef = array();

    /**
     * @var array массив видимости категорий типа ("catId1"=>array("vendId","vendId2",...),"catId2"=>1,"list"=>array("id1","id2","id3"....))) 
     */
    private $catViewOnly = null;
    
    /**
     * @var array массив видимости категорий типа ("id"=>array("id"=>,"parent_id"=>,"name"=>,"alias"=>,"list"=>array("id1","id2","id3"....))) 
     */
    private $catViewExeptions = null;
    
    /**
     * @var array массив видимости вендоров 
     */
    private $vendViewOnly = null;
    
    /**
     * @var array массив НЕвидимости вендоров 
     */
    private $vendViewExeptions = null;

    /**
     * @var array массив кастомных категорий, привязанных ключами к базовым категориям или false, если нет такового 
     */
    private $custom_categories = array();
    
    /**
     * @var array конфиг фидогенератора
     */
    private $config = array();
    
    /**
     * @var array конфиг фидогенератора по текущему региону (собранный из конфига фидогенератора)
     */
    private $regConfig = array();
    
    /**
     * @var string тип выдачи (file/browser)
     */
    private $typeDnLoad = 'file';
    
    /**
     * @var array конфиг рефера
     */
    private $referConfig = array();
    
    public function setRegId($regStr) {
        $regId = intval($regId);
        if (isset($regArr[$regId])) $this->regId = $regId;
        else $regId = 0;
    }
    
    /**
     * Возвращает параметр из конфига реферов по алиасу
     * @param string $paramAlias
     * @param mixed $defVal
     * @param string $type - тип проверки (int,decimal,datetime,strictstr,string,text,email,on,id,routeitem,url):
     * @param int $lenght - если задано и больше 0, то количество символов в результате
     * @return mixed
     */
    public function getParam($paramAlias='',$defVal=null, $type="", $lenght=0){
        return SysBF::checkStr(SysBF::getFrArr($this->referConfig,"$paramAlias",$defVal),$type,$lenght);
    }
    
    /**
     * Возвращает параметр из конфига региона по алиасу
     * @param string $paramAlias
     * @param mixed $defVal
     * @param string $type - тип проверки (int,decimal,datetime,strictstr,string,text,email,on,id,routeitem,url):
     * @param int $lenght - если задано и больше 0, то количество символов в результате
     * @return mixed
     */
    public function getRegConf($paramAlias='',$defVal=null, $type="", $lenght=0){
        return SysBF::checkStr(SysBF::getFrArr($this->regConfig,"$paramAlias",$defVal),$type,$lenght);
    }
    
    /**
     * Возвращает параметр из основного конфига по алиасу
     * @param string $paramAlias
     * @param mixed $defVal
     * @param string $type - тип проверки (int,decimal,datetime,strictstr,string,text,email,on,id,routeitem,url):
     * @param int $lenght - если задано и больше 0, то количество символов в результате
     * @return mixed
     */
    public function getConf($paramAlias='',$defVal=null, $type="", $lenght=0){
        return SysBF::checkStr(SysBF::getFrArr($this->config,"$paramAlias",$defVal),$type,$lenght);
    }

    /**
     * Возвращает параметр из основного конфига по алиасу
     * @param string $paramAlias
     * @param mixed $defVal
     * @param string $type - тип проверки (int,decimal,datetime,strictstr,string,text,email,on,id,routeitem,url):
     * @param int $lenght - если задано и больше 0, то количество символов в результате
     * @return mixed
     */
    public function getRegVals($paramAlias='',$defVal=null, $type="", $lenght=0){
        return SysBF::checkStr(SysBF::getFrArr($this->regVals,"$paramAlias",$defVal),$type,$lenght);
    }

    /**
     * Конструктор
     * @param array $confArr
     * @param string $refer
     * @param string $reg
     */
    public function __construct(array $confArr, $refer='', $regStr=''){
        
        //Сформируем общий конфиг
        $this->config = (is_array($confArr))?$confArr:array();
        
        $regStr = strtolower(strval($regStr));
        $refer = strtolower(strval($refer));
        
        $this->regAlias = $regStr;
        $this->startTs = time();
        
        $this->validate = true;
        if (!is_array($confArr)) {
            $this->validate = false;
            SysLogs::addError('Feedgen Error: No config data!');
        }
        if (empty($this->config['regions']['default']['alias'])) {
            $this->validate = false;
            SysLogs::addError('Feedgen Error: No default region alias in regions config!');
        }
        
        //Подтянем конфиг по реферу с учетом родительских настроек
        $this->referConfig = array();                
        $res = $this->getRefer($refer);
        if (empty($res)) {
            SysLogs::addError("Feedgen Error: Refer not found!");
        }else{
            SysLogs::addLog("Feedgen: Refer $refer Ok!");
            $this->refer = $refer;
            if ($setReg = strtolower($this->getParam('reg','')) && isset($this->config['regions']["$setReg"])) {
                $this->regAlias = $regStr = $setReg; //Жесткая установка региона
            }
        }
        
        //Проверка доступа ---------------------------
        if (!Glob::$vars['console']) {
        
            $secure_key = $this->getParam('secure_key',false);
            $secure_gen_key = $this->getParam('secure_gen_key',false);
            $net_allow = $this->getParam('net_allow',false);
            $net_gen_allow = $this->getParam('net_gen_allow',false);
            $date1 = $this->getParam('date1',false);
            $date2 = $this->getParam('date2',false);
            $curr_secure_key = (!empty(Glob::$vars['feed_secure_key']))?Glob::$vars['feed_secure_key']:false;
            
            if (!empty($secure_key)){
                if (!($curr_secure_key===$secure_key || (!empty($secure_gen_key) && $curr_secure_key===$secure_gen_key))) {
                    SysLogs::addError("Feedgen: Wrong secure key!");
                    $this->validate = false;
                }
            }
            
            if (!empty($net_allow)){
                if (!(SysBF::ipNetValidate($_SERVER["REMOTE_ADDR"],$net_allow) 
                        || (!empty($net_gen_allow) && SysBF::ipNetValidate($_SERVER["REMOTE_ADDR"],$net_gen_allow)))) {
                    SysLogs::addError("Feedgen: Wrong net!");
                    $this->validate = false;
                }
            }
            
            if (!empty($date1) && $this->startTs<self::tmstFrStr($date1)){
                SysLogs::addError("Feedgen: Wrong time. It is more then date1!");
                $this->validate = false;
            }
            
            if (!empty($date2) && $this->startTs>self::tmstFrStr($date2)){
                SysLogs::addError("Feedgen: Wrong time. It is more then date2!");
                $this->validate = false;
            }
            
        }   
        //Конец проверка доступа ---------------------
        
        
        //Получим алиас текущего региона
        if (empty($regStr) || !isset($this->config['regions']["$regStr"])) {
            $this->regAlias = $this->config['regions']['default']['alias'];  
            SysLogs::addLog('Feedgen: Select default region => ['.$this->regAlias.']');
        }else{
            SysLogs::addLog('Feedgen: Select '.$this->regAlias.' region');
        }
        
        //Сформируем региональный конфиг
        $this->regConfig = (isset($this->config['regions']["default"]) 
                && is_array($this->config['regions']["default"]))?$this->config['regions']["default"]:array();
        if (!empty($this->config['regions']["$this->regAlias"]) && is_array($this->config['regions']["$this->regAlias"])){
            $this->regConfig = SysBF::arrayRecurMerge($this->regConfig,$this->config['regions']["$this->regAlias"]);
        }        
                
        SysLogs::setLogFile($this->getLogFileName('full'));
        
        //Подмена рефера в файле
        $this->toRefer = $this->getParam('save_to_refer','');
        $this->toRefer = strtolower(trim($this->toRefer));
        
        //Определим параметры складов, наличия и доставки для фидов по текущему региону
        $this->regVals = (is_array($this->regConfig))?$this->regConfig:array();
        $regVals = $this->getParam('reg_vals',array());
        if (!is_array($regVals)) $regVals = array();
        
        //Применим настройки $regVals из конфига регионов
        if (!empty($regVals["default"]) && is_array($regVals["default"])) {
            $this->regVals = SysBF::arrayRecurMerge($this->regVals,$regVals["default"]);
            SysLogs::addLog('Feedgen: get reg def regVals');
        }
        if (!empty($regVals["$this->regAlias"]) && is_array($regVals["$this->regAlias"])){
            SysLogs::addLog('Feedgen: get reg '.$this->regAlias.' regVals');
            $this->regVals = SysBF::arrayRecurMerge($this->regVals,$regVals["$this->regAlias"]);
        }
        
        //Подкорректируем склады текущего региона
        $outletsArr = array();
        //print_r($this->regVals['outlets']);
        if (!empty($this->regVals['outlets']) && is_array($this->regVals['outlets'])) {        
            $outletsDef = array();
            if (!empty($this->regVals['outlets']['default']) && is_array($this->regVals['outlets']['default'])) {
                $outletsDef = $this->regVals['outlets']['default'];
            }
            foreach($this->regVals['outlets'] as $key=>$curOutlet) {
                if ($key!=='default' && is_array($curOutlet)) $outletsArr[$key] = SysBF::arrayRecurMerge($outletsDef,$curOutlet);
            }
            $this->regVals['outlets'] = $outletsArr;
        }
        
        //Установим кодировку
        $charset = $this->getParam('charset','utf-8');
        if ($charset!=='windows-1251') $charset = 'utf-8';
        $this->charset = $charset;
        
        $this->feedFormat = $this->getParam('feed_format','xml');
        
        $this->protokol = $this->getParam('protocol',Glob::$vars['feed_conf']['def_protocol']);
        $this->baseDomain = $this->getParam('domain',Glob::$vars['feed_conf']['def_domain']);
        $this->imgDomain = $this->getParam('domain_img',Glob::$vars['feed_conf']['def_domain_img']);
        if (empty($this->protokol)) $this->protokol = Glob::$vars['current_protokol'];
        if (empty($this->baseDomain)) $this->baseDomain = Glob::$vars['current_domen'];
        if (empty($this->imgDomain)) $this->imgDomain = $this->protokol . Glob::$vars['current_domen'];
        
        SysLogs::addLog("Feedgen: protokol [$this->protokol]; BaseDomain [$this->baseDomain]; ImgDomain [$this->imgDomain]");
        
        $this->useCache = $this->getParam('use_cache',Glob::$vars['feed_conf']['use_cache']);
        $this->cacheLag = $this->getParam('cache_lag',Glob::$vars['feed_conf']['cache_lag']);
        SysLogs::addLog("Feedgen: useCache=[".(($this->useCache)?'TRUE':'FALSE')."] cacheLag=[".$this->cacheLag."s]");
        
        $this->typeDnLoad = $this->getParam('type_dnload','file');
        if (!empty(Glob::$vars['request']['dnloadto']) && Glob::$vars['request']['dnloadto']==='browser') $this->typeDnLoad = 'browser';
        elseif (!empty(Glob::$vars['request']['dnloadto']) && Glob::$vars['request']['dnloadto']==='file') $this->typeDnLoad = 'file';
    }

    /**
     * Сформировать полный фид по заданному реферу
     * @param type $refer
     * @return boolean
     */
    private function getRefer($refer=''){
        
        //Рекурсивно подцепим родительские параметры фидов
        static $iterCounter = 0;

        if (empty($refer)) {
            SysLogs::addError("Feedgen Error: Refer is clear!");
            return false;
        }
        if ($iterCounter===0 && !in_array($refer, Glob::$vars['feed_conf']['refers_allow'])) {
            SysLogs::addError("Feedgen Error: Refer not allow!");
            return false;
        }
            
        $result = false;
        $thisModuleName = Glob::$vars['module'];
        //$thisModulePath = Glob::$vars['module_path'];
        $confFile =  USER_MODULESPATH . $thisModuleName . '/config/feeds/' . $refer . '.php';
        $confFileApp =  APP_MODULESPATH . $thisModuleName . '/config/feeds/' . $refer . '.php';
        if(file_exists($confFile)) include ($confFile);
        elseif(file_exists($confFileApp)) include ($confFileApp);
        
        if (isset($feedConfArr) && is_array($feedConfArr)){
            
            if ($iterCounter<10 && isset($feedConfArr["parent_feed"])) {
                if (is_array($feedConfArr["parent_feed"])){ //Корневые фиды заданы в массиве, последующие перезаписывают предыдущие
                    foreach ($feedConfArr["parent_feed"] as $value) {
                        if(!empty($value)){
                            $iterCounter++;
                            $this->getRefer($value);
                            $iterCounter--;
                        }
                    }
                }elseif(!empty($feedConfArr["parent_feed"])){ //Корневой фид задан как алиас
                    $iterCounter++;
                    $this->getRefer($feedConfArr["parent_feed"]);
                    $iterCounter--;
                }
            }

            $this->referConfig = SysBF::arrayRecurMerge($this->referConfig,$feedConfArr);
            $result = true;

        }

        SysLogs::addError("Feedgen: Get conf from $confFile " . (($result)?'Ok!':'Error!'));
        return $result;

    }
    
    /**
     * Отдача статического фида
     */
    public function loadFile(){

        $result = false;

        //Откроем файл, откуда будем выгружать
        $fromFile = $this->getParam('save_file_name','');
        if (!empty($fromFile)) $filename = FEEDS_FILESPATH . $fromFile;
        else $filename = $this->getFeedFileName('full');

        if (!file_exists($filename)) {
            SysLogs::addError("Feedgen Error: Feed file [$filename] not found!");
            return false;
        }

        $fp = file_get_contents($filename);
        if ($fp!==false){

            //Определим имя отдаваемого файла
            $extStr = '.xml';
            if ('csv' === $this->feedFormat){$extStr = '.csv';}
            if ('json' === $this->feedFormat){$extStr = '.json';}
            
            $toFile = $this->getParam('to_file_name','');
            if (empty($toFile)) $toFile = $this->getFeedFileName('','time');
            
            //Выведем заголовок
            $charsetStr = '; charset='.$this->charset;
            if (Glob::$vars['console']) {//Был запуск из консоли
                ;//Заголовок не отдаемtype_dnLoad
            }elseif($this->typeDnLoad === 'browser'){//Этим в браузер
                $contentType = SysBF::mime_content_type($toFile,Glob::$vars['feed_conf']['mime_arr']);
                header("Content-Type: $contentType$charsetStr");
                header("Content-Disposition: inline; filename=".$toFile);
            }else{//Остальным - файл для скачивания
                header("Content-Type: application/force-download$charsetStr");
                header("Content-Disposition: attachment; filename=".$toFile);
            }
            echo $fp;
            
            $result = true;
            
        }else{
            SysLogs::addError("Feedgen Error: Feed file open error!");
            return false;
        }

        return $result;
    }
    
    /**
     * Применение настроек UTM меток для текущего фида
     */
    private function setUTM(){
        $addUTM = $this->getParam('use_utm','');
        if($addUTM) {
            $reg = $this->regAlias;
            if ($reg==='default') $reg = Glob::$vars['feed_conf']['def_reg_alias_to']; 
            $utmTemplate = strtolower($this->getParam('utm_template',''));
            if (empty($utmTemplate)) { //Шаблон по-умолчанию
                $this->utm = 'utm_source={#refer}&utm_medium=cpc&utm_campaign={#refer}_{#reg}';
            } 
            
            $this->utm = $utmTemplate;
            $this->utm = str_replace('{#refer}', $this->refer, $this->utm);
            $this->utm = str_replace('{#reg}', $reg, $this->utm);
            $utmAddRefer = strtolower($this->getParam('utm_add_refer',true));
            if($utmAddRefer) $this->utm .= ((!empty($this->utm))?'&':'')."utm_referfrom={$this->refer}";
            
            SysLogs::addLog('Feedgen: Utm=['.$this->utm.']');
            return $this->utm;
        }
    }

    /**
     * Генерация фида
     * @return bool результат операции
     */
    public function generate(){

        $saveToFile = $this->getParam('save_to_file',false); //Забирается ли фид из файла при отдаче
        //Проверка доступа ---------------------------
        if (!Glob::$vars['console'] && $saveToFile) {
            $secure_gen_key = $this->getParam('secure_gen_key',false);
            $net_gen_allow = $this->getParam('net_gen_allow',false);
            $curr_secure_key = (!empty(Glob::$vars['feed_secure_key']))?Glob::$vars['feed_secure_key']:false;
            
            if (!empty($secure_gen_key) && $curr_secure_key!==$secure_gen_key) {
                SysLogs::addError("Feedgen: Wrong gen secure key!");
                return false;
            }
            
            if (!empty($net_gen_allow) && !SysBF::ipNetValidate($_SERVER["REMOTE_ADDR"],$net_gen_allow)){
                SysLogs::addError("Feedgen: Wrong gen net!");
                return false;
            }
        }   
        //Конец проверки доступа ---------------------
        
        $addToFile = $this->getParam('add_to_file',false); //При генерации если true, то будет добавлять в имеющийся файл без пересоздания.
        $saveBak = $this->getParam('save_bak',false); //При генерации сохранить старый вариант файла фида
        if ($saveToFile) {
            $fromFile = $this->getParam('save_file_name','');
            if (!empty($fromFile)) $this->saveFileName = FEEDS_FILESPATH . $fromFile;
            else $this->saveFileName = $this->getFeedFileName('full');
            SysLogs::addLog("FeedGen: fin save to file [$this->saveFileName]");
            
            $tmpFileName = $this->getFeedFileName('fulltmp');
            if(file_exists($tmpFileName) && empty(Glob::$vars['feed_cleartmp'])) $tmpFileName = ''; 
            $this->tmpFileName = $tmpFileName;
            if (empty($this->tmpFileName)) {
                SysLogs::addLog("FeedGen: tmp file allready exist. Stop generation");
                return false;
            }else{
                SysLogs::addLog("FeedGen: tmp file [$this->tmpFileName]");
            }
            $this->saveFileLink = fopen($this->tmpFileName,(($addToFile)?'a':'w'));
        }
        
        $this->setUTM();
        
        $this->currency = $this->getParam('currency', SysBF::getFrArr(Glob::$vars['feed_conf'],'currency','RUR'));
        
        $this->actionsProds = array(); 
        $this->actionsCats = array();
        $this->actionsGifts = array();
        $this->actionsView = array();
        
        //Загрузка данных
        $useParams = false;
        if ($this->getParam('use_params',false) || $this->getParam('use_attr_list',false)) { //Если используются параметры, то загрузим их
            self::genAttrArrs(array('cache_lag'=>$this->cacheLag,'use_cache'=>$this->useCache));
            $useParams = true;
        }
        self::genVendArr(array('cache_lag'=>$this->cacheLag,'use_cache'=>$this->useCache));
        self::genCatArr(array('cache_lag'=>$this->cacheLag,'use_cache'=>$this->useCache,'use_params'=>$useParams));
        
        //Подгрузка массива специфичных категорий
        if ($use_custom_cats = $this->getParam('use_custom_cats',false)) {
            $thisModuleName = Glob::$vars['module'];
            $confFile =  USER_MODULESPATH . $thisModuleName . '/config/' . $use_custom_cats;
            $confFileApp =  APP_MODULESPATH . $thisModuleName . '/config/' . $use_custom_cats;
            if(file_exists($confFile)) include ($confFile);
            elseif(file_exists($confFileApp)) include ($confFileApp);
            if (isset($custom_categories) && is_array($custom_categories)) $this->custom_categories = $custom_categories;
            else $this->custom_categories = array();
        }
        
        $this->genCatView(); //Генерация статуса видимости категорий от рута на базе разрешений и исключений конфига
        $this->genVendView(); //Генерация статуса видимости вендоров на базе разрешений и исключений конфига
        
        //Генерация блоков фида
        $this->generateHeader();
        $this->generateCategories();
        $this->generateAttr();
        $this->generateProducts();
        $this->generateActions();
        $this->generateVendors();
        $this->generateCustomBlocks();
        $this->generateFooter();
        
        if ($saveToFile) {
            fclose($this->saveFileLink);
            if ($saveBak) SysBF::createBakFile($this->saveFileName);
            self::renameFile($this->tmpFileName,$this->saveFileName);
        }
        
        return true;
    }
    
    /**
     * Генерация хедера
     * @return bool результат операции
     */
    private function generateHeader(){
        $result = true;
        $item = array();
        
        $saveToFile = $this->getParam('save_to_file',false);
        
        //Определим имя отдаваемого файла
        $extStr = '.xml';
        if ('csv' === $this->feedFormat){$extStr = '.csv';}
        if ('json' === $this->feedFormat){$extStr = '.json';}

        $toFile = $this->getParam('to_file_name','');
        if (empty($toFile)) $toFile = $this->getFeedFileName('','time');
        
        $charsetStr = '; charset='.$this->charset;
        if ($saveToFile || Glob::$vars['console']) {//Был запуск из консоли
            ;//Заголовок не отдаемtype_dnLoad
        }elseif($this->typeDnLoad === 'browser'){//Этим в браузер
            $contentType = SysBF::mime_content_type($toFile,Glob::$vars['feed_conf']['mime_arr']);
            //if ('csv' === $this->feedFormat) header("Content-Type: text/plain$charsetStr");
            //else header("Content-Type: ".$contentType);
            header("Content-Type: $contentType$charsetStr");
            header("Content-Disposition: inline; filename=".$toFile);
        }else{//Остальным - файл для скачивания
            header("Content-Type: application/force-download$charsetStr");
            header("Content-Disposition: attachment; filename=".$toFile);
        }

        $regName = (!empty(Glob::$vars['feed_conf']['regions'][$this->regAlias]['name']))?(' '.Glob::$vars['feed_conf']['regions'][$this->regAlias]['name']):'';
        $item['feed_name'] = $this->updXmlStr($this->getParam('feed_name', SysBF::getFrArr(Glob::$vars['feed_conf'],'company_name','Интернет-магазин')) . $regName);
        $item['feed_company_name'] = $this->updXmlStr($this->getParam('company_name', SysBF::getFrArr(Glob::$vars['feed_conf'],'company_name','Интернет-магазин')));
        $item['feed_base_url'] = $pArr['url'] = $this->finUpdURL($this->protokol . $this->baseDomain);
        $item['feed_currency'] = $this->currency;
        $item['feed_ts'] = $this->startTs;

        if (empty($this->regVals['delivDays'])) {
            $item['delivery_options'] = null;
        } else {
            $item['delivery_options'] = array(
                'cost'=>$this->regVals['delivCostMax'],
                'days'=>$this->regVals['delivDaysMax'],
                'order_before'=>$this->regVals['orderBefore']
            );
        }
        
        if (empty($this->regVals['pickupDays'])) {
            $item['delivery_options'] = null;
        } else {
            $item['delivery_options'] = array(
                'cost'=>$this->regVals['delivCostMax'],
                'days'=>$this->regVals['delivDaysMax'],
                'order_before'=>$this->regVals['orderBefore']
            );
        }
                
        SysLogs::addLog('Feedgen: Header generate Ok!');
        return $this->render($item,'header');
        
    } 
    
    /**
     * Генерация хедера
     * @return bool результат операции
     */
    private function generateCategories(){
        if (!$this->getParam('use_categories', true)) return false;
        $result = true;
        
        $this->render(array(),'cat_header');
        
        //Сформируем список категорий (делается 1 раз за генерацию)
        $rootCatId = $this->getParam('root_cat_id',0);
        $rootStr = '';
        if (is_array($rootCatId)){
            foreach ($rootCatId as $catId) {
                $this->recurRenderCat($catId);
                $rootStr .= ((!empty($rootStr))?',':'').$catId;
            }
        }else{
            $this->recurRenderCat($rootCatId);
            $rootStr = $rootCatId;
        }
        
        $this->render(array(),'cat_footer');

        SysLogs::addLog('Feedgen: Categories generate from root['.$rootStr.'] Ok!');
        return $result;
    }
    
    /**
     * Рекурсивно рендерит категории
     */
    private function recurRenderCat($catId=0){
        static $itemCounter = 0;
        if (!isset(self::$catArr["$catId"]) || !is_array(self::$catArr["$catId"])) return false;
        if (isset($this->catViewExeptions["$catId"]) && $this->catViewExeptions["$catId"]===true) return false;
        
        //массив категорий типа ("id"=>array("id"=>,"parent_id"=>,"name"=>,"alias"=>,"list"=>array("id1","id2","id3"....)))
        $catInfo = self::$catArr["$catId"];
        $rootCatView = $this->getParam('root_cat_view',0);
        
        $no_parentid = false;
        if ($rootCatView && $itemCounter===0) $no_parentid = true;
        elseif (!$rootCatView && $itemCounter===1) $no_parentid = true;
        
		$catInfo['cat_name'] = !empty($catInfo["cat_name"])?$this->updXmlStr($catInfo['cat_name']):'';
		$catViewOk = (false===$this->getParam('only_cat_active',false) || !empty(self::$catArr["$catId"]['cat_active']));
        if ($catViewOk && ($rootCatView || $itemCounter>0)  && !empty($catInfo['cat_name'])) $this->render($catInfo,'category',array('no_parentid'=>$no_parentid));        
        $catInfo['full_list'] = array();
        $maxCatLevels = SysBF::getFrArr(Glob::$vars['feed_conf'],'max_cat_levels',10);
        if ($itemCounter<$maxCatLevels && isset($catInfo['list']) && is_array($catInfo['list'])){
            $itemCounter++;
            $catInfo['full_list'] = $catInfo['list']; //Список всех вложенных ниже категорий и свой id там же
            foreach ($catInfo['list'] as $inCatId) {
                $full_list = $this->recurRenderCat($inCatId);
                if (is_array($full_list) && count($full_list)) foreach ($full_list as $value) $catInfo['full_list'][] = $value;
                self::$catArr["$catId"]['full_list'] = $catInfo['full_list'];
            }
            
            $itemCounter--;
        }
        
        
        return $catInfo['full_list'];
    }
    
    /**
     * Генерация массивов видимости/невидимости категорий на базе разрешений и исключений конфига
     * @return boolean
     */
    private function genCatView(){
        
        $this->catViewOnly = null;
        $this->catViewExeptions = null;
        $catListOnly = $this->getParam('cat_list_only',false);
        $catvendListOnly = $this->getParam('catvend_list_only',false);
        $catListExeptions = $this->getParam('cat_list_exeptions',false);
        $catvendListExeptions = $this->getParam('catvend_list_only',false);
        $grossCatDef = $this->getParam('gross_cat_def',false);
        $nettoCatDef = $this->getParam('netto_cat_def',false);
        
        //От рутовых категорий проставим разрешение или запрещение видимости товаров в категориях
        if (!is_array($catListOnly) && !is_array($catvendListOnly)) {
            $rootCatId = $this->getParam('root_cat_id',0);
            if (is_array($rootCatId)){
                foreach ($rootCatId as $catId) $this->catViewUpd($catId,'catview');
            }else{
                $this->catViewUpd($rootCatId,'catview');
            }  
        }
        
        if (self::$catArr === null) return false;
        
        //Разрешения
        if (is_array($catListOnly)) {//Список разрешенных чистых категорий
            foreach($catListOnly as $catId) $this->catViewUpd($catId,'catview');
        }
        
        if (is_array($catvendListOnly)) {//Список разрешенных категория+вендор
            foreach($catvendListOnly as $catId=>$catVal) $this->catViewUpd($catId,'catview',$catVal);
        }
            
        //Исключения
        if (is_array($catListExeptions)) {//Список запрещенных чистых категорий
            foreach($catListExeptions as $catId) $this->catViewUpd($catId,'catnoview');
        }
        
        if (is_array($catvendListExeptions)) {//Список запрещенных категория+вендор
            foreach($catvendListExeptions as $catId=>$catVal) $this->catViewUpd($catId,'catnoview',$catVal);
        }

        //Размеры и вес
        if (is_array($grossCatDef)) {//Список запрещенных категория+вендор
            foreach($grossCatDef as $catId=>$catVal) $this->catViewUpd($catId,'grossCatDef',$catVal);
        }
        if (is_array($nettoCatDef)) {//Список запрещенных категория+вендор
            foreach($nettoCatDef as $catId=>$catVal) $this->catViewUpd($catId,'nettoCatDef',$catVal);
        }

        SysLogs::addLog('Feedgen: Categories view generate Ok!');
        return true;
    }
    
    /**
     * Рекурсивно изменяет видимость категорий
     * @param type $catId идентификатор обрабатываемой категории
     * @param type $typeVal тип видимости ('catview'/'catnoview')
     * @param type $catVal значение (true или массив идентификаторов вендоров).
     * @return boolean
     */
    private function catViewUpd($catId,$typeVal,$catVal=true){
        static $levelCounter = 0;
        
        //Проапдейтим требуюмую категорию
        if (is_array($catVal)) $curValue = $catVal; else $curValue = true;
        if ($typeVal==='catview'){ //Добавление разрешения
            if (!is_array($this->catViewOnly)) $this->catViewOnly = array();
            $this->catViewOnly["$catId"] = $curValue;
        }elseif($typeVal==='catnoview'){ //Добавление исключения
            if (!is_array($this->catViewExeptions)) $this->catViewExeptions = array();
            $this->catViewExeptions["$catId"] = $curValue;
        }elseif($typeVal==='grossCatDef'){ //Добавление исключения
            if (!is_array($this->grossCatDef)) $this->grossCatDef = array();
            $this->grossCatDef["$catId"] = $curValue;
        }
        elseif($typeVal==='nettoCatDef'){ //Добавление исключения
            if (!is_array($this->nettoCatDef)) $this->nettoCatDef = array();
            $this->nettoCatDef["$catId"] = $curValue;
        }

        //Рекурсивно вызовем функцию для нижестоящих
        $full_list_upd = false;
        if (isset(self::$catArr["$catId"])){
            $catInfo = self::$catArr["$catId"];
            $catInfo['full_list'] = array();

            $maxCatLevels = SysBF::getFrArr(Glob::$vars['feed_conf'],'max_cat_levels',10);
            if ($levelCounter<$maxCatLevels 
                    && isset(self::$catArr["$catId"]) && isset(self::$catArr["$catId"]['list']) && is_array(self::$catArr["$catId"]['list'])){
                $levelCounter++;
                $catInfo['full_list'] = $catInfo['list']; //Список всех вложенных ниже категорий и свой id там же
                foreach ($catInfo['list'] as $inCatId) {
                    $full_list = $this->catViewUpd($inCatId,$typeVal,$curValue);
                    if ($typeVal==="catview" && $catVal===true){ //Заполняем при первичном прогоне
                        if (is_array($full_list) && count($full_list)) foreach ($full_list as $value) $catInfo['full_list'][] = $value;
                        self::$catArr["$catId"]['full_list'] = $catInfo['full_list'];
                        $full_list_upd = true;
                    }
                }
                $levelCounter--;             
            }
        }else{
            SysLogs::addError("Feedgen Error: catViewUpd cat[$catId] not found! typeVal=[$typeVal] levelCounter=[$levelCounter]");          
        }

        if ($full_list_upd) return $catInfo['full_list'];
        else return true;

    }


    /**
     * Генерация списка параметров
     * @return bool результат операции
     */
    private function generateAttr(){
        $result = null;
        if (!$this->getParam('use_attr_list', true)) return false;
        
        
        SysLogs::addLog('Feedgen: attrArr generate Ok!');
        return $result;
    }    
    
    /**
     * Генерация списка складов по регионам
     * @return bool результат операции
     */
    private function generateCustomBlocks(){
        if (!$this->getParam('use_custom_blocks', true)) return false;
        
        $this->genCustomBlocks(); //Запуск обертки генератора кастомных блоков из трейта
        
        return true;
    }    
        
    
    /**
     * Генерация списка товаров
     * @return bool результат операции
     */
    private function generateVendors(){
        if (!$this->getParam('use_vendors', true)) return false;
        
        $result = true;
        $this->render(array(),'vend_header');
        
        //Сформируем список товаров
        if (is_array($this->vendView)) foreach ($this->vendView as $vendInfo) $this->render($vendInfo,'vendor');
        
        $this->render(array(),'vend_footer');
        
        SysLogs::addLog('Feedgen: Vendors generate Ok!');
        return $result;
    }
        
    /**
     * Генерация статуса видимости вендоров на базе разрешений и исключений конфига
     */
    private function genVendView(){
        
        $this->vendViewOnly = null;
        $this->vendViewExeptions = null;
            
        if (self::$catArr === null) return false;
        
        //Разрешения
        $vendListOnly = $this->getParam('vend_list_only',false);
        if (is_array($vendListOnly)) {//Список разрешенных вендоров
            $this->vendViewOnly = $vendListOnly;
        }
        
        //Исключения
        $vendListExeptions = $this->getParam('vend_list_exeptions',false);
        if (is_array($vendListExeptions)) {//Список запрещенных вендоров
            $this->vendViewExeptions = $vendListExeptions;
        }
        
        SysLogs::addLog('Feedgen: Vendors view generate Ok!');
        return true;
    }
    
    
    /**
     * Генерация списка товаров
     * @return bool результат операции
     */
    private function generateProducts(){
        if (!$this->getParam('use_products', true)) return false;
        $prodViewCounter = 0;
        $result = true;
        $this->render(array(),'prod_header');
        $maxSize = Glob::$vars['feed_conf']['product_block_maxsize']; //максимальный размер пачки
        $maxProducts = $this->getParam('max_products',false);
        $useParams = $this->getParam('use_params',false);
        $domainImg = $this->getRegVals('domain_img',$this->getRegConf('domain_img',Glob::$vars['feed_conf']['def_domain_img']));
        if (empty($domainImg)) $domainImg = Glob::$vars['current_domen'];

        $maxProductsArr = array(); //Массив категорий, входящих в рутовые категории
        $catItemsCounters = array(); //Массив с счетчиками товаров по рутовым категориям
        if (is_array($maxProducts)) {
            foreach ($maxProducts as $cat_id=>$cat_lim){
                $maxProductsArr["$cat_id"] = $cat_id;
                $catItemsCounters["$cat_id"] = 0;
                if (isset(self::$catArr["$cat_id"]['full_list']) && is_array(self::$catArr["$cat_id"]['full_list'])) {
                    foreach (self::$catArr["$cat_id"]['full_list'] as $value) {
                        $maxProductsArr["$value"] = $cat_id;
                    }
                }
            }
        }
        
        //Сформируем список товаров в соответствии с конфигом и выдадим пачками
        $prodViewParams = array(
            'max_size'=>$maxSize,
            'max_products' => $maxProducts,
            'domain_img' => $domainImg,
            );
        if (!empty($useParams)) $prodViewParams['attr_view'] = true;
        if ($this->getParam('use_gross_sw',false)) $prodViewParams['use_gross_sw'] = true;
        if ($this->getParam('use_netto_sw',false)) $prodViewParams['use_netto_sw'] = true;
        $prodViewParams['sort_by'] = ($this->getParam('sort_by',false));
                
        $this->vendView = array(); //Массив вендоров, который мы сгенерим при выводе товаров
        if ($prodArr = self::getProdArr($prodViewParams)){ //TODO - поменять на while потом как сделаем пачки
            if (is_array($prodArr)){
                foreach ($prodArr as $prodInfo) {
                    $prodOut = $this->updateProdInfo($prodInfo);
                    $cat_id = SysBF::getFrArr($prodInfo,'cat_id',0);
                    if (method_exists($this,'updateProdInfoCustom')) $prodOut = $this->updateProdInfoCustom($prodOut,$prodInfo);
                    if ($prodOut!==false) {
                        if (is_array($maxProducts) && isset($maxProductsArr["$cat_id"]) && !empty($maxProducts[strval($maxProductsArr["$cat_id"])])) { //Массив с лимитами по рутовым категориям
                            $curRootCat = strval($maxProductsArr["$cat_id"]);
                            $catItemsCounters[$curRootCat]++;
                            if ($catItemsCounters[$curRootCat]>$maxProducts[$curRootCat]) continue;
                        } elseif (!empty($maxProducts) && $prodViewCounter>$maxProducts) break;

                        $this->render($prodOut,'product');
                        $prodViewCounter++;
                        if (!empty($prodOut['vendor_arr']['vend_id'])) $this->vendView[$prodOut['vendor_arr']['vend_id']] = $prodOut['vendor_arr'];
                    }
                }
            }
        }
        
        $this->render(array(),'prod_footer');
        
        SysLogs::addLog('Feedgen: '.$prodViewCounter.' products generate Ok!');
        return $result;
    }
    
    /**
     * Дорабатывает данные о товаре в соответствии с настройками фида, можно кастомно добавить updateProdInfoCustom() которая применится после
     * @param type $prodInfo массив данных о товаре
     * @return mixed - либо массив данных о товаре, либо false, если нет возможности его сформировать
     */
    private function updateProdInfo($prodInfo){
        if (!is_array($prodInfo)) return false;
        //$prodInfoSource = $prodInfo; //На всякий случай, может понадобится исходный вариант
        
        $offerId = SysBF::getFrArr($prodInfo,'offer_id',0);
        $catId = SysBF::getFrArr($prodInfo,'cat_id',0);
        $vendId = SysBF::getFrArr($prodInfo,'vendor_id',0);
        
        //Проверим на разрешения по вендорам и категориям
        if ($this->vendViewOnly!==null && empty($this->vendViewOnly["$vendId"])) return false;
        if ($this->catViewOnly!==null){
            if (isset($this->catViewOnly["$catId"])){
                if (is_array($this->catViewOnly["$catId"]) && !in_array($vendId, $this->catViewOnly["$catId"])) return false;
                elseif (empty($this->catViewOnly["$catId"])) return false;
            }else return false;
        }
                
        //Проверим на исключения по вендорам и категориям
        if ($this->vendViewExeptions!==null && !empty($this->vendViewExeptions["$vendId"])) return false;
        if ($this->catViewExeptions!==null){
            if (isset($this->catViewExeptions["$catId"])){
                if (is_array($this->catViewExeptions["$catId"]) && in_array($vendId, $this->catViewExeptions["$catId"])) return false;
                elseif (!empty($this->catViewExeptions["$catId"])) return false;
            }
        }
        
        //Обработки данных товара
        $prodInfo = $this->updStart($prodInfo); //Стартовые преобразования

        $prodInfo = $this->updSizeWeight($prodInfo); //Обработка размеров и веса, стоит в начале, т.к. легкая и по ней может быть блокировка
        $prodInfo = $this->updInStock($prodInfo); //Обработка статусов наличия, отключение видимости, связанные элементы
        $prodInfo = $this->updPrice($prodInfo); //Обработка всех видов цен, отключение видимости, связанные элементы
        $prodInfo = $this->updPhoto($prodInfo);
        $prodInfo = $this->updDelivery($prodInfo);
        $prodInfo = $this->updName($prodInfo);
        $prodInfo = $this->updManufacturer($prodInfo);
        $prodInfo = $this->updDescr($prodInfo);
        $prodInfo = $this->updSalesNotes($prodInfo);
        $prodInfo = $this->updWarranty($prodInfo);
        $prodInfo = $this->updParams($prodInfo);

        $prodInfo = $this->updFin($prodInfo); //Финальные преобразования: отключение видимость xml блоков и предустановленные обработки
        $prodInfo = $this->updCustomParams($prodInfo);
        
        $prodInfo = $this->updCustom($prodInfo); //Пользовательские преобразования     

        return $prodInfo;
    }
                    
    /**
     * Общие начальные обработки
     * @param type $prodInfo
     * @return mixed обновленный $prodInfo или false, если тован не подлежит публикации
     */
    private function updStart($prodInfo){
        if (!is_array($prodInfo)) return $prodInfo;
        
        //Начальные исключения
        if (true ===$this->getParam('only_country_complete',false) && empty($prodInfo['country'])) return false;
                
        //Разрешения по id
        $prod_list_only = $this->getParam('prod_list_only',false);
        if (is_array($prod_list_only)){            
            if (isset($prod_list_only['1cid']) && is_array($prod_list_only['1cid']) && !in_array(strval($prodInfo['1c_id']), $prod_list_only['1cid'])
             && isset($prod_list_only['prid']) && is_array($prod_list_only['prid']) && !in_array(strval($prodInfo['prod_id']), $prod_list_only['prid'])) {
                return false;
            }
        }
        
        //Исключения по id
        static $prod_ts_exept_arr = array(); //Нужен чтоб не считать на каждой итерации
        $prod_list_exeptions = $this->getParam('prod_list_exeptions',false);
        if (is_array($prod_list_exeptions)){ 
            foreach ($prod_list_exeptions as $key=>$curItem) {
                if (!isset($prod_ts_exept_arr[$key])) $prod_ts_exept_arr[$key] = array();
                //Проверим по дате
                if (!isset($prod_ts_exept_arr[$key]['prod_ts_from'])) {
                    $dt = SysBF::getFrArr($curItem, 'date1', '');
                    if (empty($dt)) $prod_ts_exept_arr[$key]['prod_ts_from'] = false;
                    else $prod_ts_exept_arr[$key]['prod_ts_from'] = self::tmstFrStr($dt);
                }
                if (!isset($prod_ts_exept_arr[$key]['prod_ts_to'])) {
                    $dt = SysBF::getFrArr($curItem, 'date2', '');
                    if (empty($dt)) $prod_ts_exept_arr[$key]['prod_ts_to'] = false;
                    else $prod_ts_exept_arr[$key]['prod_ts_to'] = self::tmstFrStr($dt);
                }
                if (!empty($prod_ts_exept_arr[$key]['prod_ts_from']) && $this->startTs<$prod_ts_exept_arr[$key]['prod_ts_from']) continue;
                if (!empty($prod_ts_exept_arr[$key]['prod_ts_to']) && $this->startTs>$prod_ts_exept_arr[$key]['prod_ts_to']) continue;
                if ((isset($curItem['1cid']) && is_array($curItem['1cid']) && in_array(strval($prodInfo['1c_id']), $curItem['1cid']))
                 || (isset($curItem['prid']) && is_array($curItem['prid']) && in_array(strval($prodInfo['prod_id']), $curItem['prid']))) return false;
            }   
        }
        
        //Исключения по ид с учетом текущей даты
        static $prod_delta_ts_from = null;
        static $prod_date_ts_from = null;
        if ($prod_delta_ts_from === null) { //Обрезка по отклонению от текущей даты
            $prod_delta_ts_from = intval($this->getParam('prod_delta_ts_from',0));
            if (!empty($prod_delta_ts_from)) $prod_delta_ts_from = $this->startTs - $prod_delta_ts_from; 
        }
        if (!empty($prod_delta_ts_from) && $prod_delta_ts_from>intval($prodInfo['ts_upd'])) return false;
        
        if ($prod_date_ts_from === null){ //Обрезка от конкретной даты
            $prod_date_from = $this->getParam('prod_date_from','');
            if (!empty($prod_date_from)) $prod_date_ts_from = self::tmstFrStr($prod_date_from);
            else $prod_date_ts_from = false;
        }
        if (!empty($prod_date_ts_from) && $prod_date_ts_from>(intval($prodInfo['ts_upd']))) return false;
        
        
        static $prod_ts_replace_arr = array(); //Нужен чтоб не считать на каждой итерации
        $prod_list_replace = $this->getParam('prod_list_replace',false);
        if (is_array($prod_list_replace)){ 
            foreach ($prod_list_replace as $key=>$curItem) {
                if (!isset($prod_ts_replace_arr[$key])) $prod_ts_replace_arr[$key] = array();
                //Проверим по дате
                if (!isset($prod_ts_replace_arr[$key]['prod_ts_from'])) {
                    $dt = SysBF::getFrArr($curItem, 'date1', '');
                    if (empty($dt)) $prod_ts_replace_arr[$key]['prod_ts_from'] = false;
                    else $prod_ts_replace_arr[$key]['prod_ts_from'] = self::tmstFrStr($dt);
                }
                if (!isset($prod_ts_replace_arr[$key]['prod_ts_to'])) {
                    $dt = SysBF::getFrArr($curItem, 'date2', '');
                    if (empty($dt)) $prod_ts_replace_arr[$key]['prod_ts_to'] = false;
                    else $prod_ts_replace_arr[$key]['prod_ts_to'] = self::tmstFrStr($dt);
                }
                if (!empty($prod_ts_replace_arr[$key]['prod_ts_from']) && $this->startTs<$prod_ts_replace_arr[$key]['prod_ts_from']) continue;
                if (!empty($prod_ts_replace_arr[$key]['prod_ts_to']) && $this->startTs>$prod_ts_replace_arr[$key]['prod_ts_to']) continue;
                
                //Тут замена свойств
                if ((isset($curItem['1cid']) && is_array($curItem['1cid']) 
                        && isset($curItem['1cid'][strval($prodInfo['1c_id'])]) && is_array($curItem['1cid'][strval($prodInfo['1c_id'])]))){
                    foreach($curItem['1cid'][strval($prodInfo['1c_id'])] as $key=>$value){
                        $prodInfo[$key] = $value;
                    }
                }
                if ((isset($curItem['prid']) && is_array($curItem['prid']) 
                        && isset($curItem['prid'][strval($prodInfo['prod_id'])]) && is_array($curItem['prid'][strval($prodInfo['prod_id'])]))){
                    foreach($curItem['prid'][strval($prodInfo['prod_id'])] as $key=>$value){
                        $prodInfo[$key] = $value;
                    }
                }
            }   
        }
        
        
        //формат основного идентификатора фида
        $offeridType = $this->getParam('offerid_type','prodid');
        $offerId = ($offeridType==='1cid')?$prodInfo['1c_id']:$prodInfo['prod_id'];
        
        $prod_id_dopstr = $this->getParam('prod_prefix_str','');
        if (empty($prod_id_dopstr)) $prod_id_dopstr = $this->regAlias;
        
        $use_prod_id_doptype = $this->getParam('use_prod_id_doptype',''); //('prefix'/'postfix'/false). Алиас региона в начале ID товара  Пример: <g:id>123_msk</g:id>
    
        if ($use_prod_id_doptype === 'prefix') $prodInfo['offer_id'] = $prod_id_dopstr . $offerId;
        elseif ($use_prod_id_doptype === 'postfix') $prodInfo['offer_id'] = $offerId . $prod_id_dopstr;
        else  $prodInfo['offer_id'] = $offerId;

        //Обработка URL товара
        if (false===$this->getParam('use_url',false) || empty($prodInfo['url'])) $prodInfo['url'] = null;
        else{
            $protocol = $this->getRegVals('protocol',$this->getRegConf('protocol',$this->getConf('def_protocol','//')));
            $domain = $this->getRegVals('domain',$this->getRegConf('domain',$this->getConf('def_domain',Glob::$vars['current_domen'])));
            $prefix = $this->getRegVals('uri_prefix',$this->getRegConf('uri_prefix',''));
            $postfix = $this->getRegVals('uri_postfix',$this->getRegConf('uri_postfix',''));
            $prodInfo['url'] = $protocol . $domain . $prefix . $this->finUpdURL($prodInfo['url'].$postfix, 'product', (('1cid'===$this->getParam('utm_add_term_type','prodid'))?$prodInfo['1c_id']:$prodInfo['prod_id']));
        }
        
        $prodInfo['currency'] = $this->currency;
        $prodInfo['price_type'] = $this->getParam('price_type','');
        
        $prodInfo['cat_active'] = (!empty(self::$catArr[strval($prodInfo['cat_id'])]['cat_active']))?true:false;
        if ($this->getParam('only_cat_active',false) && empty($prodInfo['cat_active'])) return false;
        if ($this->getParam('only_prod_active',false) && empty($prodInfo['prod_active'])) return false;
        
        //Если есть правила маркировки, то промаркируем продукт для дальнейшего использования маркера в кастомных методах обработки товара
        $prodInfo['marker'] = false;
        $mark_prods_conf=$this->getParam('mark_prods',false);
        if (is_array($mark_prods_conf)){
            //Список маркированных продуктов для использования в хуках типа 
            //array("1cid"=>array("код1",...),"prid"=>array("код1",...)). "prid" имеет приоритет             
            if (isset($mark_prods_conf['1cid']) && is_array($mark_prods_conf['1cid']) && in_array($prodInfo['1c_id'], $mark_prods_conf['1cid'])) $prodInfo['marker'] = true;
            elseif (isset($mark_prods_conf['prid']) && is_array($mark_prods_conf['prid']) && in_array($prodInfo['prod_id'], $mark_prods_conf['prid'])) $prodInfo['marker'] = true;
        }
        
        //Данные по датам и TS 
        if (!empty($prodInfo['ts_create'])) $prodInfo['date_create'] = date("Y-m-d H:i:s",$prodInfo['ts_create']);
        if (!empty($prodInfo['ts_upd'])) $prodInfo['date_upd'] = date("Y-m-d H:i:s",$prodInfo['ts_upd']);
        if (!empty($prodInfo['ts_upd_price'])) $prodInfo['date_upd_price'] = date("Y-m-d H:i:s",$prodInfo['ts_upd_price']);
        if (!empty($prodInfo['ts_upd_photo'])) $prodInfo['date_upd_photo'] = date("Y-m-d H:i:s",$prodInfo['ts_upd_photo']);
        
        return $prodInfo;
    }
    
    /**
     * Общие обработки, включение и выключение элементов xml фидов.
     * @param type $prodInfo
     * @return mixed обновленный $prodInfo или false, если тован не подлежит публикации
     */
    private function updFin($prodInfo){
        if (!is_array($prodInfo)) return $prodInfo;
        
        //Блокировка вывода товара в фид
        if ($this->getParam('only_type_prefix_complete',false) && empty($prodInfo['type_prefix'])) return false;
        if ($this->getParam('only_vendor_complete',false) && empty($prodInfo['vendor'])) return false;
        if ($this->getParam('only_model_complete',false) && empty($prodInfo['model'])) return false;

        //Изменения параметров
        if (empty($prodInfo['oldprice']) || $prodInfo['oldprice']<$prodInfo['price']) $prodInfo['oldprice'] = null;
        if (empty($prodInfo['barcode'])) $prodInfo['barcode'] = '';
        if (empty($prodInfo['1c_id'])) $prodInfo['1c_id'] = '';        
        
        
        //Формирование массивов actionsProds и actionsGifts
        $ya_actions = $this->getParam('ya_actions',false);
        if ($this->getParam('use_actions',false) && is_array($ya_actions)){ //Формирование массива товаров акций и подарков
            if (!is_array($this->actionsProds)) $this->actionsProds = array();
            if (!is_array($this->actionsGifts)) $this->actionsGifts = array();
            
            $curPrice = $prodInfo['price'];
            $curOldprice = $prodInfo['oldprice'];
            
            foreach($ya_actions as $actionKey=>$actionArr){
            
                $actionId = $actionKey;
                
                //Проверка на условия выполнения акции
                if (empty($actionArr["id"])||empty($actionArr["type"])) continue;
                if (isset($actionArr["regions"]) && is_array($actionArr["regions"]) && !in_array(strval($this->regAlias),$actionArr["regions"])) continue;
               
                //Проверим по дате
                if (!isset($this->actionsView[$actionId])){
                    
                    $dt = SysBF::getFrArr($actionArr, 'start', '');
                    if (empty($dt)) $actions_prod_ts_from = false;
                    else $actions_prod_ts_from = self::tmstFrStr($dt);

                    $dt = SysBF::getFrArr($actionArr, 'end', '');
                    if (empty($dt)) $actions_prod_ts_to = false;
                    else $actions_prod_ts_to = self::tmstFrStr($dt);

                    if (!empty($actions_prod_ts_from) && $this->startTs<$actions_prod_ts_from) $this->actionsView[$actionId] = false;
                    elseif (!empty($actions_prod_ts_to) && $this->startTs>$actions_prod_ts_to) $this->actionsView[$actionId] = false;
                    else $this->actionsView[$actionId] = true;
                
                }
                
                if (empty($this->actionsView[$actionId])) continue;
                
                
                if ($actionArr["type"]==="flash discount" && $prodInfo['instock_status']){ //Цены для акции берем из массива, старая цена=текущая
                    
                    if (isset($actionArr["products_discount"]) && is_array($actionArr["products_discount"])){
                        //Заданы базовые идентификаторы с ценами
                        if (isset($actionArr["products_discount"]["prid"]) && is_array($actionArr["products_discount"]["prid"]) 
                                && !empty($actionArr["products_discount"]["prid"][$prodInfo['prod_id']])){
                            if (!isset($this->actionsProds[$actionId])) $this->actionsProds[$actionId] = array();
                            $this->actionsProds[$actionId][$prodInfo['prod_id']] = $actionArr["products_discount"]["prid"][$prodInfo['prod_id']];
                            $prodInfo["oldprice"] = null;
                        }
                        //Заданы 1c идентификаторы с ценами
                        if (isset($actionArr["products_discount"]["1cid"]) && is_array($actionArr["products_discount"]["1cid"]) 
                                && !empty($actionArr["products_discount"]["1cid"][$prodInfo['1c_id']])){
                            if (!isset($this->actionsProds[$actionId])) $this->actionsProds[$actionId] = array();
                            $this->actionsProds[$actionId][$prodInfo['prod_id']] = $actionArr["products_discount"]["1cid"][$prodInfo['1c_id']];
                            $prodInfo["oldprice"] = null;
                        }
                    }

                    if (isset($actionArr["products"]) && is_array($actionArr["products"]) && !empty($prodInfo["oldprice"])){ //Цены для акции берем из текущей цены и старой цены
                        //Заданы 1c идентификаторы БЕЗ цен
                        if (isset($actionArr["products"]["1cid"]) && is_array($actionArr["products"]["1cid"]) 
                                && in_array($prodInfo['1c_id'],$actionArr["products"]["1cid"])){
                            if (!isset($this->actionsProds[$actionId])) $this->actionsProds[$actionId] = array();
                            $this->actionsProds[$actionId][$prodInfo['prod_id']] = $prodInfo["price"];
                            $prodInfo["price"] = $curOldprice;
                            $prodInfo["oldprice"] = null;
                            //SysLogs::addLog('Add action 1c prod=['.$prodInfo['prod_id'].'] price=['.$prodInfo['price'].'] action=['.$this->actionsProds[$actionId][$prodInfo['prod_id']].']');
                        }
                        //Заданы базовые идентификаторы БЕЗ цен
                        if (isset($actionArr["products"]["prid"]) && is_array($actionArr["products"]["prid"]) 
                                && in_array($prodInfo['prod_id'],$actionArr["products"]["prid"])){
                            if (!isset($this->actionsProds[$actionId])) $this->actionsProds[$actionId] = array();
                            $this->actionsProds[$actionId][$prodInfo['prod_id']] = $prodInfo["price"];
                            $prodInfo["price"] = $curOldprice;
                            $prodInfo["oldprice"] = null;
                            //SysLogs::addLog('Add action prod=['.$prodInfo['prod_id'].'] price=['.$prodInfo['price'].'] action=['.$this->actionsProds[$actionId][$prodInfo['prod_id']].']');
                        }
                        
                    }
                    
                }elseif ($actionArr["type"]=="promo code" && $prodInfo['instock_status']){
                    
                    if (isset($actionArr["products"]) && is_array($actionArr["products"])){
                        //Заданы базовые идентификаторы
                        if (isset($actionArr["products"]["prid"]) && is_array($actionArr["products"]["prid"]) 
                                && in_array($prodInfo['prod_id'],$actionArr["products"]["prid"])){
                            if (!isset($this->actionsProds[$actionId])) $this->actionsProds[$actionId] = array();
                            $this->actionsProds[$actionId][$prodInfo['prod_id']] = $prodInfo["price"];
                            if (!empty($prodInfo["oldprice"])){
                                $prodInfo["price"] = $curOldprice;
                                $prodInfo["oldprice"] = null;
                            }
                        }
                        //Заданы 1c идентификаторы
                        if (isset($actionArr["products"]["1cid"]) && is_array($actionArr["products"]["1cid"]) 
                                && in_array($prodInfo['1c_id'],$actionArr["products"]["1cid"])
                                && !isset($this->actionsProds[$actionId][$prodInfo['prod_id']])){
                            if (!isset($this->actionsProds[$actionId])) $this->actionsProds[$actionId] = array();
                            $this->actionsProds[$actionId][$prodInfo['prod_id']] = $prodInfo["price"];
                            if (!empty($prodInfo["oldprice"])){
                                $prodInfo["price"] = $curOldprice;
                                $prodInfo["oldprice"] = null;
                            }
                        }
                    }
                    /*
                    //Задана категория
                    if (isset($actionArr["categories"]) && is_array($actionArr["categories"]) 
                            && !in_array($prodInfo['cat_id'],$actionArr["categories"])){
                        if (!isset($this->actionsProds[$actionId])) $this->actionsProds[$actionId] = array();
                        $this->actionsProds[$actionId][$prodInfo['prod_id']] = $prodInfo["price"];
                        if (!empty($prodInfo["oldprice"])){
                            $prodInfo["price"] = $prodInfo["oldprice"];
                            $prodInfo["oldprice"] = null;
                        }
                    }
                    */
                }elseif ($actionArr["type"]=="n plus m" && $prodInfo['instock_status']){
                    
                    if (isset($actionArr["products"]) && is_array($actionArr["products"])){
                        //Заданы базовые идентификаторы
                        if (isset($actionArr["products"]["prid"]) && is_array($actionArr["products"]["prid"]) 
                                && in_array($prodInfo['prod_id'],$actionArr["products"]["prid"])){
                            if (!isset($this->actionsProds[$actionId])) $this->actionsProds[$actionId] = array();
                            $this->actionsProds[$actionId][$prodInfo['prod_id']] = $prodInfo["price"];
                        }
                        //Заданы 1c идентификаторы
                        if (isset($actionArr["products"]["1cid"]) && is_array($actionArr["products"]["1cid"]) 
                                && in_array($prodInfo['1c_id'],$actionArr["products"]["1cid"])
                                && !isset($this->actionsProds[$actionId][$prodInfo['prod_id']])){
                            if (!isset($this->actionsProds[$actionId])) $this->actionsProds[$actionId] = array();
                            $this->actionsProds[$actionId][$prodInfo['prod_id']] = $prodInfo["price"];
                        }
                    }
                    /*
                    //Задана категория
                    if (isset($actionArr["categories"]) && is_array($actionArr["categories"]) 
                            && !in_array($prodInfo['cat_id'],$actionArr["categories"])){
                        if (!isset($this->actionsProds[$actionId])) $this->actionsProds[$actionId] = array();
                        $this->actionsProds[$actionId][$prodInfo['prod_id']] = $prodInfo["price"];
                        if (!empty($prodInfo["oldprice"])){
                            $prodInfo["price"] = $prodInfo["oldprice"];
                            $prodInfo["oldprice"] = null;
                        }
                    }
                    */
                }elseif($actionArr["type"]=="gift with purchase" && $prodInfo['instock_status']){

                    if (isset($actionArr["products"]) && is_array($actionArr["products"])){                        
                        //Заданы базовые идентификаторы
                        if (isset($actionArr["products"]["prid"]) && is_array($actionArr["products"]["prid"]) 
                                && in_array($prodInfo['prod_id'],$actionArr["products"]["prid"])){
                            if (!isset($this->actionsProds[$actionId])) $this->actionsProds[$actionId] = array();
                            $this->actionsProds[$actionId][$prodInfo['prod_id']] = $prodInfo["price"];
                        }
                        //Заданы 1c идентификаторы
                        if (isset($actionArr["products"]["1cid"]) && is_array($actionArr["products"]["1cid"]) 
                                && in_array($prodInfo['1c_id'],$actionArr["products"]["1cid"])
                                && !isset($this->actionsProds[$actionId][$prodInfo['prod_id']])){
                            if (!isset($this->actionsProds[$actionId])) $this->actionsProds[$actionId] = array();
                            $this->actionsProds[$actionId][$prodInfo['prod_id']] = $prodInfo["price"];
                        }
                    }
                    
                    if (isset($actionArr["gifts"]) && is_array($actionArr["gifts"])){                        
                        //Заданы базовые идентификаторы
                        if (isset($actionArr["gifts"]["prid"]) && is_array($actionArr["gifts"]["prid"]) 
                                && in_array($prodInfo['prod_id'],$actionArr["gifts"]["prid"])){
                            if (!isset($this->actionsGifts[$actionId])) $this->actionsGifts[$actionId] = array();
                            $this->actionsGifts[$actionId][$prodInfo['prod_id']] = $prodInfo["price"];
                        }
                        //Заданы 1c идентификаторы
                        if (isset($actionArr["gifts"]["1cid"]) && is_array($actionArr["gifts"]["1cid"]) 
                                && in_array($prodInfo['1c_id'],$actionArr["gifts"]["1cid"])
                                && !isset($this->actionsGifts[$actionId][$prodInfo['prod_id']])){
                            if (!isset($this->actionsGifts[$actionId])) $this->actionsGifts[$actionId] = array();
                            $this->actionsGifts[$actionId][$prodInfo['prod_id']] = $prodInfo["price"];
                        }
                    }

                }

            }

        }

        
        //Блокировка элементов XML фида
        if ($this->feedFormat==='xml'){
            if (false===$this->getParam('use_shop_sku',false)) $prodInfo['shop_sku'] = null;
            if (false===$this->getParam('use_delivery_cat',false)) $prodInfo['delivery_cat'] = null;
            if (false===$this->getParam('use_name',false)) $prodInfo['name'] = null;
            if (false===$this->getParam('use_type_prefix',false)) $prodInfo['type_prefix'] = null;
            if (false===$this->getParam('use_vendor',false)) $prodInfo['vendor'] = null;
            if (false===$this->getParam('use_model',false)) $prodInfo['model'] = null;
            if (false===$this->getParam('use_descr',false)) $prodInfo['description'] = null;
            if (false===$this->getParam('use_manufacturer',false)) $prodInfo['manufacturer'] = null;
            if (false===$this->getParam('use_warranty',false)) $prodInfo['warranty'] = null;
            if (false===$this->getParam('use_country',false)) $prodInfo['country'] = null;
            if (false===$this->getParam('use_country',false)) $prodInfo['country'] = null;
            if (false===$this->getParam('use_delivery',false)) {$prodInfo['delivery'] = null; $prodInfo['delivery_options'] = null;}
            if (false===$this->getParam('use_delivery_options',false)) $prodInfo['delivery_options'] = null;
            if (false===$this->getParam('use_pickup',false)) $prodInfo['pickup'] = null;
            if (false===$this->getParam('use_pickup_options',false)) $prodInfo['pickup_options'] = null;
            if (false===$this->getParam('use_store',false)) $prodInfo['store'] = null;
            if (false===$this->getParam('use_qty',false)) $prodInfo['instock_qty'] = null;
            if (false===$this->getParam('use_outlets',false)) $prodInfo['outlets'] = null;
            if (false===$this->getParam('use_price',false)) $prodInfo['price'] = null;
            if (false===$this->getParam('use_sales_notes',false)) $prodInfo['sales_notes'] = null;
            if (false===$this->getParam('use_oldprice',false) || empty($prodInfo['oldprice'])) $prodInfo['oldprice'] = null;
            if (false===$this->getParam('use_barcode',false)) $prodInfo['barcode'] = null;
            if (false===$this->getParam('use_1c_code',false)) $prodInfo['1c_id'] = null;
            if (false===$this->getParam('use_marker',false)) $prodInfo['marker'] = null;     
            if (false===$this->getParam('use_profit',false)) $prodInfo['profit'] = null;
            if (false===$this->getParam('use_profit_pr',false)) $prodInfo['profit_pr'] = null;
            if (false===$this->getParam('use_profit_lvl',false)) $prodInfo['profit_lvl'] = null;
            if (false===$this->getParam('use_date_create',false)) $prodInfo['date_create'] = null;
            if (false===$this->getParam('use_date_upd',false)) $prodInfo['date_upd'] = null;
            if (false===$this->getParam('use_date_upd_price',false)) $prodInfo['date_upd_price'] = null;
            if (false===$this->getParam('use_date_upd_photo',false)) $prodInfo['date_upd_photo'] = null;
            if (false===$this->getParam('use_instock_status_str',false)) $prodInfo['instock_status_str'] = null;
        }
        
        return $prodInfo;
    }
    
    /**
     * Обработка размера и веса, блокировка вывода по этом показателям
     * @param type $prodInfo
     * @return mixed обновленный $prodInfo или false, если тован не подлежит публикации
     */
    private function updSizeWeight($prodInfo){
        if (!is_array($prodInfo)) return $prodInfo;

        if (isset($prodInfo['netto'])) $prodInfo['netto'] = $this->sizeWaightUpd($prodInfo['netto']);
        if (isset($prodInfo['gross'])) $prodInfo['gross'] = $this->sizeWaightUpd($prodInfo['gross']);

        if ((!isset($prodInfo['gross']) || $prodInfo['gross']===false)
            && is_array($this->grossCatDef)
            && isset($this->grossCatDef[(string)$prodInfo['cat_id']])
            && is_array($this->grossCatDef[(string)$prodInfo['cat_id']])) {
            $prodInfo['gross'] = $this->grossCatDef[(string)$prodInfo['cat_id']];
        }

        if ((!isset($prodInfo['netto']) || $prodInfo['netto']===false)
            && is_array($this->nettoCatDef)
            && isset($this->nettoCatDef[(string)$prodInfo['cat_id']])
            && is_array($this->nettoCatDef[(string)$prodInfo['cat_id']])) {
            $prodInfo['netto'] = $this->nettoCatDef[(string)$prodInfo['cat_id']];
        }
        
        if ((!isset($prodInfo['netto']) || !is_array($prodInfo['netto'])) 
                && isset($prodInfo['gross']) && is_array($prodInfo['gross']) 
                && $this->getParam('gross_to_netto_upd',false)) $prodInfo['netto'] = $prodInfo['gross'];
        if ($this->getParam('gross_required',false) && (!isset($prodInfo['gross']) || !is_array($prodInfo['gross']))) return false;
        if ($this->getParam('netto_required',false) && (!isset($prodInfo['netto']) || !is_array($prodInfo['netto']))) return false;
        
        //Проверка на размеры и веса всех видов
        $max_gross_size_weight = $this->getParam('max_gross_size_weight',false);
        if (is_array($max_gross_size_weight)){
            if (isset($max_gross_size_weight["height"]) && $prodInfo['gross']['height']>floatval($max_gross_size_weight["height"])) return false;
            if (isset($max_gross_size_weight["weight"]) && $prodInfo['gross']['weight']>floatval($max_gross_size_weight["weight"])) return false;
            
            if (isset($max_gross_size_weight["gsize"])){
                if (!is_array($max_gross_size_weight["gsize"]) || !isset($max_gross_size_weight["gsize"][1])){
                    if (count($max_gross_size_weight["gsize"])) { 
                        if (is_array($max_gross_size_weight["gsize"])) $max_gross_size_weight["gsize"] = $max_gross_size_weight["gsize"][0];
                        if ($prodInfo['gross']['width']>floatval($max_gross_size_weight["gsize"])
                                || $prodInfo['gross']['length']>floatval($max_gross_size_weight["gsize"])) return false;
                    }
                }else{
                    $curSizeArr = array($prodInfo['gross']['width'],$prodInfo['gross']['length']);
                    rsort($curSizeArr);
                    rsort($max_gross_size_weight["gsize"]);
                    if ($curSizeArr[0]>floatval($max_gross_size_weight["gsize"][0]) 
                            || $curSizeArr[1]>floatval($max_gross_size_weight["gsize"][1])) return false;
                }
            }

        }
        
        $min_gross_size_weight = $this->getParam('min_gross_size_weight',false);
        if (is_array($min_gross_size_weight)){
            if (isset($min_gross_size_weight["height"]) && $prodInfo['gross']['height']<floatval($min_gross_size_weight["height"])) return false;
            if (isset($min_gross_size_weight["weight"]) && $prodInfo['gross']['weight']<floatval($min_gross_size_weight["weight"])) return false;
            if (isset($min_gross_size_weight["gsize"])){
                if (!is_array($min_gross_size_weight["gsize"]) || !isset($min_gross_size_weight["gsize"][1])){
                    if (count($min_gross_size_weight["gsize"])) { 
                        if (is_array($min_gross_size_weight["gsize"])) $min_gross_size_weight["gsize"] = $min_gross_size_weight["gsize"][0];
                        if ($prodInfo['gross']['width']<floatval($min_gross_size_weight["gsize"])
                                || $prodInfo['gross']['length']<floatval($min_gross_size_weight["gsize"])) return false;
                    }
                }else{
                    $curSizeArr = array($prodInfo['gross']['width'],$prodInfo['gross']['length']);
                    rsort($curSizeArr);
                    rsort($min_gross_size_weight["gsize"]);
                    if ($curSizeArr[0]<floatval($min_gross_size_weight["gsize"][0]) 
                            || $curSizeArr[1]<floatval($min_gross_size_weight["gsize"][1])) return false;
                }
            }
        }
        
        //Формирование заданных элементов вывода
        if ($this->getParam('use_gross_params',false) && is_array($prodInfo['gross'])){
            $prodInfo['gross_params'] = array(
                array(
                    'code' => 'gross_weight',
                    'name' => 'БРУТТО вес ('.$prodInfo['gross']['weight_unit'].')',
                    'value' => $prodInfo['gross']['weight'],
                ),
                array(
                    'code' => 'gross_size_hwl',
                    'name' => 'БРУТТО размер (ВхШхГ) ('.$prodInfo['gross']['size_unit'].')',
                    'value' => $prodInfo['gross']['height'] . '/' . $prodInfo['gross']['width'] . '/' . $prodInfo['gross']['length'],
                ),
            );
        } 
        if ($this->getParam('use_netto_params',false) && is_array($prodInfo['netto'])){
            $prodInfo['netto_params'] = array(
                array(
                    'code' => 'netto_weight',
                    'name' => 'НЕТТО вес ('.$prodInfo['netto']['weight_unit'].')',
                    'value' => $prodInfo['netto']['weight'],
                ),
                array(
                    'code' => 'netto_size_hwl',
                    'name' => 'НЕТТО размер (ВхШхГ) ('.$prodInfo['netto']['size_unit'].')',
                    'value' => $prodInfo['netto']['height'] . '/' . $prodInfo['netto']['width'] . '/' . $prodInfo['netto']['length'],
                ),
            );
        } 
        
        if ($this->getParam('use_gross_dimensions',false) && is_array($prodInfo['gross'])){
            $prodInfo['gross_dimensions'] = $prodInfo['gross']['height'] . '/' . $prodInfo['gross']['width'] . '/' . $prodInfo['gross']['length'];
        }
        if ($this->getParam('use_netto_dimensions',false) && is_array($prodInfo['netto'])){
            $prodInfo['netto_dimensions'] = $prodInfo['netto']['height'] . '/' . $prodInfo['netto']['width'] . '/' . $prodInfo['netto']['length'];
        }
        
        if ($this->getParam('use_gross_weight',false) && is_array($prodInfo['gross'])){
            $prodInfo['gross_weight'] = $prodInfo['gross']['weight'];
        }
        if ($this->getParam('use_netto_weight',false) && is_array($prodInfo['netto'])){
            $prodInfo['netto_weight'] = $prodInfo['netto']['weight'];
        }
        
        if ($this->getParam('use_gross_split',false) && is_array($prodInfo['gross'])){
            $prodInfo['gross_split'] = array(
                'height' => $prodInfo['gross']['height'],
                'width' => $prodInfo['gross']['width'],
                'length' => $prodInfo['gross']['length'],
            );
        }
        if ($this->getParam('use_netto_split',false) && is_array($prodInfo['netto'])){
            $prodInfo['netto_split'] = array(
                'height' => $prodInfo['netto']['height'],
                'width' => $prodInfo['netto']['width'],
                'length' => $prodInfo['netto']['length'],
            );
        }
        
        return $prodInfo;
    }

    /**
     * Дорабатывает массив размера и веса, если это не возможно, то возвращает false
     * @param type $sizeWeight
     * @return boolean
     */
    private function sizeWaightUpd($sizeWeight=null){
        if (!is_array($sizeWeight)) return false;
        if (empty($sizeWeight['weight_unit'])) $sizeWeight['weight_unit'] = 'см';
        if (empty($sizeWeight['size_unit'])) $sizeWeight['size_unit'] = 'кг';
        if (!empty($sizeWeight['weight'])) $sizeWeight['weight'] = round($sizeWeight['weight'],2); else return false;
        if (!empty($sizeWeight['length'])) $sizeWeight['length'] = ceil($sizeWeight['length']); else return false;
        if (!empty($sizeWeight['width'])) $sizeWeight['width'] = ceil($sizeWeight['width']); else return false;
        if (!empty($sizeWeight['height'])) $sizeWeight['height'] = ceil($sizeWeight['height']); else return false;
        return $sizeWeight;
    } 
            
    /**
     * Обработка статусов наличия по региону и складам, отключение видимости, связанные элементы
     * @param type $prodInfo
     * @return mixed обновленный $prodInfo или false, если тован не подлежит публикации
     */
    private function updInStock($prodInfo){
        if (!is_array($prodInfo)) return $prodInfo;
        
        $outletsOutprodStatus = false;
        $outletsInstockStatus = InStock_Empty;
        $outletsInstockQty = 0;
        $outletsArr = $this->getRegVals('outlets',false);
        $outnCnt = 1;
        $prodInfo['outlets'] = array();
        if (is_array($outletsArr)) foreach ($outletsArr as $outletAlias=>$item) {
            $instock_outlets_status = (isset($prodInfo['instock_outlets_status']) && is_array($prodInfo['instock_outlets_status']) && isset($prodInfo['instock_outlets_status'][strval($outletAlias)]))?$prodInfo['instock_outlets_status'][strval($outletAlias)]:InStock_Empty;
            $curOutprod = $this->useMask($instock_outlets_status,SysBF::getFrArr($item,'outprod_mask',InStock_Empty));
            
            $instock_outlets_qty = (isset($prodInfo['instock_outlets_qty']) && is_array($prodInfo['instock_outlets_qty']) && isset($prodInfo['instock_outlets_qty'][strval($outletAlias)]))?$prodInfo['instock_outlets_qty'][strval($outletAlias)]:0;
            $maskQty = $this->useMaskQty($instock_outlets_status,SysBF::getFrArr($item,'qty_mask','real'),$instock_outlets_qty);
            
            //Подправим суммарные значения по всем складам региона
            $outletsInstockQty += $maskQty;
            $outletsOutprodStatus = $outletsOutprodStatus | $curOutprod;
            $outletsInstockStatus = $outletsOutprodStatus | $instock_outlets_status;
            
            $outletId = strval(SysBF::getFrArr($item,'id',$outnCnt));
            $prodInfo['outlets']["$outletAlias"] = array('id'=>$outletId,'qty'=>$maskQty);
            $outnCnt++;
        }
        
        //Проверим на необходимость выдачи товара в фид
        if ($this->getParam('outprod_from_outlets',false)) $prodInfo['outprod'] = $outletsOutprodStatus;
        else $prodInfo['outprod'] = $this->useMask($prodInfo['instock_status'],$this->getRegVals('outprod_mask',InStock_Empty));
        if (empty($prodInfo['outprod'])) return false;
        
        if ($this->getParam('instock_from_outlets',false)) $instockStatus = $outletsInstockStatus;
        else $instockStatus = $prodInfo['instock_status'];
        
        $prodInfo['offer_available'] = $InStock = $prodInfo['instock'] = $this->useMask($instockStatus,$this->getRegVals('instock_mask',InStock_Empty));
        $prodInfo['store'] = $this->useMask($instockStatus,$this->getRegVals('store_instock_mask',InStock_Empty));
        $prodInfo['pickup'] = $this->useMask($instockStatus,$this->getRegVals('pickup_instock_mask',InStock_Empty));
        
        if ($this->getParam('instqty_from_outlets',false)) {
            $prodInfo['instock_qty'] = $outletsInstockQty;
        }else{
            $maskQty = $this->useMaskQty($instockStatus,$this->getRegVals('qty_mask','real',$prodInfo['instock_qty']));
            if (empty($prodInfo['instock_qty']) || $maskQty>$prodInfo['instock_qty']) $prodInfo['instock_qty'] = $maskQty;
        }
        
        if (!isset($prodInfo['delivery_options'])){
            $delivOptionsArr = $this->getRegVals('delivery_options',false);
            if (is_array($delivOptionsArr)) foreach ($delivOptionsArr as $delivOptions) {
                if (!is_array($prodInfo['delivery_options'])) $prodInfo['delivery_options'] = array();
                $curInstockMask = SysBF::getFrArr($delivOptions, 'instock_mask',InStock_Empty);
                $curInstock = $this->useMask($instockStatus,$curInstockMask);
                $curCost = SysBF::getFrArr($delivOptions, 'cost_max',0);
                if (SysBF::getFrArr($delivOptions, 'check_cost',false) && isset($prodInfo['delivery_cost'])) $curCost = $prodInfo['delivery_cost'];
                $curDays = ($curInstock)?SysBF::getFrArr($delivOptions, 'days_instock_max',0):SysBF::getFrArr($delivOptions, 'days_max',0);
                if (SysBF::getFrArr($delivOptions, 'check_days',false) && isset($prodInfo['delivery_days'])) $curDays = $prodInfo['delivery_days'];
                $curOrdbf = SysBF::getFrArr($delivOptions, 'order_before',13);
                $prodInfo['delivery_options'][] = array('cost'=>$curCost,'days'=>strval($curDays),'order_before'=>strval($curOrdbf));
            }
        }
        
        if (!isset($prodInfo['pickup_options'])){
            $pickupOptionsArr = $this->getRegVals('pickup_options',false);
            if (is_array($pickupOptionsArr)) foreach ($pickupOptionsArr as $pickupOptions) {
                if (!is_array($prodInfo['pickup_options'])) $prodInfo['pickup_options'] = array();
                $curInstockMask = SysBF::getFrArr($pickupOptions, 'instock_mask',InStock_Empty);
                $curInstock = $this->useMask($instockStatus,$curInstockMask);
                $curCost = SysBF::getFrArr($pickupOptions, 'cost_max',0);
                if (SysBF::getFrArr($pickupOptions, 'check_cost',false) && isset($prodInfo['pickup_cost'])) $curCost = $prodInfo['pickup_cost'];
                $curDays = ($curInstock)?SysBF::getFrArr($pickupOptions, 'days_instock_max',0):SysBF::getFrArr($pickupOptions, 'days_max',0);
                if (SysBF::getFrArr($pickupOptions, 'check_days',false) && isset($prodInfo['pickup_days'])) $curDays = $prodInfo['pickup_days'];
                $curOrdbf = SysBF::getFrArr($pickupOptions, 'order_before',13);
                $prodInfo['pickup_options'][] = array('cost'=>$curCost,'days'=>strval($curDays),'order_before'=>strval($curOrdbf));
            }
        }
        
        $prodInfo['instock_status_str'] = '';
        if (InStock_Empty === $instockStatus) $prodInfo['instock_status_str'] .= (($prodInfo['instock_status_str']!='')?'|':'').'InStock_Empty';
        else{
            if (InStock_False & $instockStatus) $prodInfo['instock_status_str'] .= (($prodInfo['instock_status_str']!='')?'|':'').'InStock_False';
            if (InStock_Regional_3 & $instockStatus) $prodInfo['instock_status_str'] .= (($prodInfo['instock_status_str']!='')?'|':'').'InStock_Regional_3';
            if (InStock_Regional_2 & $instockStatus) $prodInfo['instock_status_str'] .= (($prodInfo['instock_status_str']!='')?'|':'').'InStock_Regional_2';
            if (InStock_Regional_1 & $instockStatus) $prodInfo['instock_status_str'] .= (($prodInfo['instock_status_str']!='')?'|':'').'InStock_Regional_1';
            if (InStock_Regional_0 & $instockStatus) $prodInfo['instock_status_str'] .= (($prodInfo['instock_status_str']!='')?'|':'').'InStock_Regional_0';
            if (InStock_Shop_3 & $instockStatus) $prodInfo['instock_status_str'] .= (($prodInfo['instock_status_str']!='')?'|':'').'InStock_Shop_3';
            if (InStock_Shop_2 & $instockStatus) $prodInfo['instock_status_str'] .= (($prodInfo['instock_status_str']!='')?'|':'').'InStock_Shop_2';
            if (InStock_Shop_1 & $instockStatus) $prodInfo['instock_status_str'] .= (($prodInfo['instock_status_str']!='')?'|':'').'InStock_Shop_1';
            if (InStock_Donor_3 & $instockStatus) $prodInfo['instock_status_str'] .= (($prodInfo['instock_status_str']!='')?'|':'').'InStock_Donor_3';
            if (InStock_Donor_2 & $instockStatus) $prodInfo['instock_status_str'] .= (($prodInfo['instock_status_str']!='')?'|':'').'InStock_Donor_2';
            if (InStock_Donor_1 & $instockStatus) $prodInfo['instock_status_str'] .= (($prodInfo['instock_status_str']!='')?'|':'').'InStock_Donor_1';
            if (InStock_Donor_0 & $instockStatus) $prodInfo['instock_status_str'] .= (($prodInfo['instock_status_str']!='')?'|':'').'InStock_Donor_0';
            if (InStock_Dealers & $instockStatus) $prodInfo['instock_status_str'] .= (($prodInfo['instock_status_str']!='')?'|':'').'InStock_Dealers';
            if (InStock_Production & $instockStatus) $prodInfo['instock_status_str'] .= (($prodInfo['instock_status_str']!='')?'|':'').'InStock_Production';
        }
        
        return $prodInfo;
    }
    
    /**
     * Подготовка пользовательских параметров (привязка к категориям и вендорам)
     * @param type $prodInfo массив данных о текущем товаре
     * @return array массив найденных фотографий
     */
    private function updCustomParams($prodInfo){
        if (!is_array($prodInfo)) return $prodInfo;
        
        $customParamsArr = $this->getParam('customParams',false);
        if (!is_array($customParamsArr) || !count($customParamsArr)) return $prodInfo;
        if (!isset($prodInfo['params']) || !is_array($prodInfo['params'])) $prodInfo['params'] = array();            
        foreach ($customParamsArr as $customParam) {
            if (!isset($customParam['param']) || !is_array($customParam['param'])) continue;
            $paramOk = false;
            
            if (isset($customParam['cat_ids']) && is_array($customParam['cat_ids']) && !empty($prodInfo['cat_id'])){
                foreach ($customParam['cat_ids'] as $curCatId) {
                    if (strval($prodInfo['cat_id'])===strval($curCatId)) {$paramOk = true; break;}
                    elseif (isset(self::$catArr["$curCatId"]['full_list']) 
                            && is_array(self::$catArr["$curCatId"]['full_list'])
                            && in_array(strval($prodInfo['cat_id']), self::$catArr["$curCatId"]['full_list'])) {$paramOk = true;break;}
                }                
            }
            if (isset($customParam['vend_ids']) && is_array($customParam['vend_ids']) 
                    && !empty($prodInfo['vendor_id']) && in_array($prodInfo['vendor_id'], $customParam['vend_ids'])) $paramOk = true;
            if (isset($customParam['catvend_ids']) && is_array($customParam['catvend_ids']) 
                    && !empty($prodInfo['cat_id']) && !empty($prodInfo['vendor_id'])){
                foreach ($customParam['catvend_ids'] as $cat_id => $vend_id) {
                    if (isset(self::$catArr["$cat_id"]['full_list']) && is_array(self::$catArr["$cat_id"]['full_list'])
                            && in_array(strval($prodInfo['cat_id']), self::$catArr["$cat_id"]['full_list'])) {    
                        if (is_array($vend_id)) {
                            if (in_array(strval($prodInfo['vendor_id']), $vend_id)) $paramOk = true;
                        }elseif($prodInfo['vendor_id']===$vend_id) $paramOk = true;
                    }
                    if ($paramOk) break;
                }
            }
            if ($paramOk) $prodInfo['params'][] = $customParam['param'];
        }
           
        if (!count($prodInfo['params'])) $prodInfo['params'] = null; //Пустого не выдаем          
        return $prodInfo;
    }
    
    /**
     * Подготовка системных параметров (привязка к категориям и вендорам)
     * @param type $prodInfo массив данных о текущем товаре
     * @return array массив найденных фотографий
     */
    private function updParams($prodInfo){
        if (!is_array($prodInfo)) return $prodInfo;
        //"use_params" => Вывести в фид параметры (false/true/limit/filter).
        $use_params = $this->getParam('use_params',false);
        if (false===$use_params) return $prodInfo;
        
        if (isset(self::$catArr[$prodInfo["cat_id"]]) 
                && isset(self::$catArr[$prodInfo["cat_id"]]["attr"]) 
                && is_array(self::$catArr[$prodInfo["cat_id"]]["attr"])
                && isset(self::$prodAttr[$prodInfo["prod_id"]])) {
            
            if (!isset($prodInfo['params']) || !is_array($prodInfo['params'])) $prodInfo['params'] = array();
            foreach (self::$catArr[$prodInfo["cat_id"]]["attr"] as $attrId) {
                
                if (isset(self::$prodAttrType["$attrId"]) && isset(self::$prodAttr[$prodInfo["prod_id"]]["$attrId"])) {
                    $prodAttrType = self::$prodAttrType["$attrId"];
                    $prodAttrVal = self::$prodAttr[$prodInfo["prod_id"]]["$attrId"];
                    
                    if ($use_params==='limit' && empty($prodAttrType['short'])) continue;
                    if ($use_params==='filter' && empty($prodAttrType['filter'])) continue;
                    
                    //array('id'=>'Нужна обрешетка', 'code'=>'nuzhna_obreshetka', 'name'=>'Нужна обрешетка', 'value'=>'да', 'val_id'=>null),
                    $customParam = array('id'=>$prodAttrType['id']);
                    if (isset($prodAttrType['alias'])) $customParam['code'] = $prodAttrType['alias']; 
                    if (isset($prodAttrType['name'])) $customParam['name'] = $prodAttrType['name']; 
                    
                    if (isset($prodAttrType['type']) && $prodAttrType['type']==='list'){
                        if (isset($prodAttrType['values']) && is_array($prodAttrType['values']) && isset($prodAttrType['values']["$prodAttrVal"])) {
                            $customParam['value'] = $prodAttrType['values']["$prodAttrVal"]["value"];
                            $customParam['val_id'] = $prodAttrVal;
                        }
                    }else{
                        $customParam['value'] = $prodAttrVal;
                    }           
                    
                    if (isset($customParam['value'])) $prodInfo['params'][] = $customParam;
                }
                
            }
        }
        
        return $prodInfo;
    }
    
    /**
     * Подготовка цены
     * @param type $prodInfo массив данных о текущем товаре
     * @return array массив найденных фотографий
     */
    private function updPrice($prodInfo){
        if (!is_array($prodInfo)) return $prodInfo;
        
        $priceKoef = $this->getParam('price_koef',false);
        if ($priceKoef !== false){
            $curVal = Feedgen::getRootCatVal($priceKoef,strval($prodInfo['cat_id']));
            if (null!==$curVal) $prodInfo['price'] = round(floatval($curVal) * $prodInfo['price']);
        }
        
        //Расчетные операции-------------------
        if (!isset($prodInfo['cost'])) $prodInfo['cost'] = 0;
        if (empty($prodInfo['price'])) {
            $prodInfo['profit'] = $prodInfo['profit_real'] = $prodInfo['profit_pr'] = 0;
        }else{
            $maxProfitPr = floatval($this->getParam('max_profit_pr',false));
            $prodInfo['profit_real'] = $prodInfo['price'] - $prodInfo['cost'];
            $prodInfo['profit'] = ($maxProfitPr)?(round($maxProfitPr*$prodInfo['price']/100,2)):$prodInfo['profit_real'];
            $prodInfo['profit_pr'] = round(100*$prodInfo['profit']/$prodInfo['price']);
        }
        $useProfitLvl = $this->getParam('use_profit_lvl',false);
        if (is_array($useProfitLvl)) {
            $prodInfo['profit_lvl'] = null;
            foreach($useProfitLvl as $lvlAlias=>$lvlMaxVol) {
                if ($prodInfo['profit_pr']>=floatval($lvlMaxVol)) $prodInfo['profit_lvl'] = $lvlAlias;
            }
        }
        
        $mrcView = $this->getParam('mrc_view',false);
        if ($mrcView){
            if ($mrcView==='<=0' && $prodInfo['mrc']>0) return false;
            elseif ($mrcView==='>0' && $prodInfo['mrc']<=0) return false;
            elseif ($mrcView==='=0' && $prodInfo['mrc']!=0) return false;
        }
        
        
        //Ограничения вывода ------------------------------
        $minprice = $this->getParam('minprice',false);
        $maxprice = $this->getParam('maxprice',false);
        if ($this->getParam('use_minprice',false) && !empty($minprice) && $prodInfo['price']<$minprice) return false;
        if ($this->getParam('use_maxprice',false) && !empty($maxprice) && $prodInfo['price']>$maxprice) return false;
        if ($this->getParam('null_price',false) && empty($prodInfo['price'])) return false;

        //Добавить цену до мин порога
        $minpriceto = $this->getParam('minpriceto',false);
        if (!empty($minpriceto) && $prodInfo['price']<$minpriceto) $prodInfo['price'] = $minpriceto;
        
        if (isset($prodInfo['sales_rate'])){
            $salesRateLimit1 = $this->getParam('sales_rate_limit1',0);
            $salesRateLimit2 = $this->getParam('sales_rate_limit2',0);
            if (!empty($salesRateLimit1) && $prodInfo['sales_rate']<$salesRateLimit1) return false;
            if (!empty($salesRateLimit2) && $prodInfo['sales_rate']>$salesRateLimit2) return false;
        }
        
        if (isset($prodInfo['profit'])){
            $profitLimit1 = $this->getParam('profit_limit1',0);
            $profitLimit2 = $this->getParam('profit_limit2',0);
            if (!empty($profitLimit1) && $prodInfo['profit_real']<$profitLimit1) return false;
            if (!empty($profitLimit2) && $prodInfo['profit_real']>$profitLimit2) return false;
        }
        
        return $prodInfo;
    }
    
    /**
     * Пользовательские преобразования свойств товара
     * @param type $prodInfo массив данных о текущем товаре
     * @return array массив найденных фотографий
     */
    private function updCustom($prodInfo){
        if (!is_array($prodInfo)) return $prodInfo;
        return $prodInfo;
    }
    
    /**
     * Добавляет к URL UTM метки и для xml фидов заменяет амперсанд на &amp;
     * @param string $url
     * @param string $pgtype тип URL ('product', 'prod_img', '')
     * @param string $prodId идентификатор товара, если есть, то будет подставлен в term
     * @return string
     */
    private function finUpdURL($url,$pgType='',$prodId=0) {
        $result = $url;

        if ($pgType==='prod_img' && false===$this->getParam('use_img_utm',false)) return $result;

        $prodId = intval($prodId);
        $delim = (false===strpos($url,'?'))?'?':'&';
        if (!empty($this->utm)) {
            $result .= $delim . $this->utm;
            $delim = '&';
        } 
        if (!empty($prodId) && ($pgType==='product' || $pgType==='prod_img') && $this->getParam('utm_add_term',false)) {
            $result .= $delim . 'utm_term=' . $prodId;
        }   
        
        return $this->updXmlStr($result);
    }
    
    /**
     * Заменяет недопустимые символы в строке для xml файла
     * @param string $str
     * @param bool $param - массив параметров
     * @return string
     */
    private function updXmlStr($str, $param=false) {
        if ($str===null) return '';
        if (!is_array($param)) $param = array('descr_tags'=>false, 'descr_no_s'=>false, 'descr_cdata'=>false);
        $descrTags = SysBF::getFrArr($param,'descr_tags',false); //Оставить короткий набор тегов
        $descrNoS = SysBF::getFrArr($param,'descr_no_s',false); //Заменить все пробельные символы на одиночный пробел
        $descrCdata = SysBF::getFrArr($param,'descr_cdata',false); //Использовать CDATA
        
        $result = $str;
        if ($result!==null && 'xml'===$this->feedFormat) {
            
            if ($descrTags){//Оставляем теги <br><ul><li><p> и ряд базовых
                //Оставим только нужные теги
                $result = str_replace("<p>", "&lt;p&gt;", $result);
                $result = str_replace("</p>", "&lt;/p&gt;", $result);
                $result = str_replace("<b>", "&lt;h3&gt;", $result);
                $result = str_replace("</b>", "&lt;/h3&gt;", $result);
                $result = str_replace("<strong>", "&lt;strong&gt;", $result);
                $result = str_replace("</strong>", "&lt;/strong&gt;", $result);
                //$result = str_replace("<h3>", "&lt;h3&gt;", $result);
                //$result = str_replace("</h3>", "&lt;/h3&gt;", $result);
                $result = str_replace("<li>", "&lt;li&gt;", $result);
                $result = str_replace("</li>", "&lt;/li&gt;", $result);
                $result = str_replace("<ul>", "&lt;ul&gt;", $result);
                $result = str_replace("</ul>", "&lt;/ul&gt;", $result);
                $result = str_replace("<ol>", "&lt;ol&gt;", $result);
                $result = str_replace("</ol>", "&lt;/ol&gt;", $result);
                $result = str_replace("<br>", "&lt;br&gt;", $result);
                $result = strip_tags($result);
                $result = str_replace("&lt;", "<", $result);
                $result = str_replace("&gt;", ">", $result);
            }else{//Стандартная замена
                //Заменим ряд тегов на разделители, уберем все теги и почистим текст
                $result = str_replace("<li>", ", ", $result);
                $result = str_replace("<br>", " \n", $result);
                $result = str_replace("<p>", " \n", $result);
                $result = strip_tags($result);
                //$result = preg_replace("/[^0-9a-zA-Zа-яА-ЯЁё_\\/:;\.,\-\s\*\(\)\[\]]/","",$result);
            }
            $result = preg_replace("/(\n){3,}/","\n",$result); //Уберем лишние пустые строки
                
            if ($descrCdata){
                $result = "<![CDATA[" . str_replace("]]>", " ", $result) . "]]>"; //Если встретим - удалим закрытие CDATA
            }else{                 
                $result = str_replace("`", "'", $result); //обратная кавычка
                $result = str_replace('”', '"', $result); //двойная кавычка нестандартная
                $result = str_replace("&#060;", "<", $result); //< &lt;
                $result = str_replace("&#062;", ">", $result); //> &gt;
                $result = str_replace("&#034;", '"', $result); //" &quot;
                $result = str_replace("&#039;", "'", $result); //' &apos;
                $result = str_replace("&#038;", "&", $result); //& &amp;       
                $result = preg_replace("/&#\d{3};/"," ",$result); //Уберем все мнемоники, которые не заменили        
                $result = htmlspecialchars($result, ENT_XML1, 'Windows-1251');
            }
            
            $result = $this->dellNoPrintChars($result);
            if ($descrNoS) $result = preg_replace("/\s+/", " ", $result);
            
        }
        return $result;
    }
    
    /**
     * Убирает непечатаемые символы из строки
     * @param $string
     * @return mixed
     */
    public function dellNoPrintChars($string){
        return $string = preg_replace('/[\x00-\x09\x0B-\x0C\x0E-\x1F\x7F]/', '', $string);
    }    
    
    /**
     * Обработка имени и его составляющих
     * @param type $prodInfo
     * @return mixed обновленный $prodInfo или false, если тован не подлежит публикации
     */
    private function updName($prodInfo){
        if (!is_array($prodInfo)) return $prodInfo;
        
        $useName = $this->getParam('use_name',false);
        $namePostfix = $this->getParam('name_postfix',null);
        $nameReplace = $this->getParam('name_replace',null);
        $modelReplace = $this->getParam('model_replace',null);
        if ($useName === 'short') $prodInfo['name'] = $prodInfo['short_name'];
        if (is_array($nameReplace)) {
            foreach($nameReplace as $searchStr=>$replaceTo) $prodInfo['name'] = str_replace(strval($searchStr),strval($replaceTo),$prodInfo['name']);
        }
        else $prodInfo['name'] = $prodInfo['full_name'];
        if (!empty($namePostfix)) $prodInfo['name'] .= ' ' . $namePostfix;
        $prodInfo['name'] = $this->updXmlStr($prodInfo['name']);
        
        $prodInfo['type_prefix'] = $this->updXmlStr($prodInfo['type_prefix']);
        $prodInfo['vendor'] = $this->updXmlStr($prodInfo['vendor']);
        $prodInfo['model'] = $this->updXmlStr($prodInfo['model']);
        if (is_array($modelReplace)) {
            foreach($nameReplace as $searchStr=>$replaceTo) $prodInfo['model'] = str_replace(strval($searchStr),strval($replaceTo),$prodInfo['model']);
        }
        $prodInfo['cat_name'] = (!empty(self::$catArr[strval($prodInfo['cat_id'])]['cat_name']))?self::$catArr[strval($prodInfo['cat_id'])]['cat_name']:'';
                
        return $prodInfo;
    }
    
    /**
     * Обработка описания
     * @param type $prodInfo
     * @return mixed обновленный $prodInfo или false, если тован не подлежит публикации
     */
    private function updDescr($prodInfo){
        if (!is_array($prodInfo)) return $prodInfo;
        
        $useDescr = $this->getParam('use_descr',false);
        if ($useDescr === 'short') $prodInfo['description'] = $prodInfo['description_min'];
        else $prodInfo['description'] = $prodInfo['description_full'];
        
        if ($descr_dop_top = $this->getParam('descr_dop_top',false)){
            $curVal = Feedgen::getRootCatVal($descr_dop_top,strval($prodInfo['cat_id']));
            if (null!==$curVal) $prodInfo['description'] = $curVal . $prodInfo['description'];
        }
        
        if ($descr_dop = $this->getParam('descr_dop',false)){
            $curVal = Feedgen::getRootCatVal($descr_dop,strval($prodInfo['cat_id']));
            if (null!==$curVal) $prodInfo['description'] .= $curVal;
        }
        
        $prodInfo['description'] = $this->updXmlStr($prodInfo['description'], array(
            "descr_tags" => $this->getParam('descr_tags',false), //Заменять перевод строки на <br> и по возможности оставить <ul><li> и <h3>
            "descr_no_s" => $this->getParam('descr_no_s',false), //Заменять все пробельные символы, включая перевод строки на одиночный пробел
            "descr_cdata" => $this->getParam('descr_cdata',false), // Использовать CDATA
        ));

        return $prodInfo;
    }
    
    /**
     * Обработка sales_notes
     * @param type $prodInfo
     * @return mixed обновленный $prodInfo или false, если тован не подлежит публикации
     */
    private function updSalesNotes($prodInfo){
        if (!is_array($prodInfo)) return $prodInfo;
        $prodInfo['sales_notes'] = $this->updXmlStr($prodInfo['sales_notes']);
        return $prodInfo;
    }
    
    /**
     * Обработка доставки
     * @param type $prodInfo
     * @return mixed обновленный $prodInfo или false, если тован не подлежит публикации
     */
    private function updDelivery($prodInfo){
        if (!is_array($prodInfo)) return $prodInfo;  
        $prodInfo['delivery'] = (!empty($prodInfo['delivery']))?true:false;  
        $prodInfo['delivery_cat'] = $this->updXmlStr($prodInfo['delivery_cat']);
        
        $delivery_cat_exeptions = $this->getParam('delivery_cat_exeptions',false);
        if (is_array($delivery_cat_exeptions) && in_array($prodInfo['delivery_cat'],$delivery_cat_exeptions)) {
            return false;
        }
        return $prodInfo;
    }
    
    
    /**
     * Обработка manufacturer
     * @param type $prodInfo
     * @return mixed обновленный $prodInfo или false, если тован не подлежит публикации
     */
    private function updManufacturer($prodInfo){
        if (!is_array($prodInfo)) return $prodInfo;
        if (empty($prodInfo['manufacturer'])) $prodInfo['manufacturer'] = null;
        else $prodInfo['manufacturer'] = $this->updXmlStr($prodInfo['manufacturer']);
        return $prodInfo;
    }
    
    /**
     * Обработка warranty
     * @param type $prodInfo
     * @return mixed обновленный $prodInfo или false, если тован не подлежит публикации
     */
    private function updWarranty($prodInfo){
        if (!is_array($prodInfo)) return $prodInfo;
        if (empty($prodInfo['warranty'])) $prodInfo['warranty'] = null;
        else $prodInfo['warranty'] = $this->updXmlStr($prodInfo['warranty']);
        return $prodInfo;
    }
    
    /**
     * Выдает результат проверки статуса по маске
     * @param type $curStatus
     * @param type $currMask
     * @return bool результат наложения маски на статус true/false
     */
    private function useMask($curStatus,$currMask){
        $result = false;
        if ($currMask==='all') $result = true;
        else $result = $curStatus & $currMask; 
        return $result;
    }
    
    /**
     * Выдает наличие по маске (максимальное из возможных)
     * @param type $curStatus
     * @param type $currMask - массив ключ - маска, занчение - колич, если не массив, а 'real', то берет по реальному количеству
     * @param type $realQty - реальное наличие, если есть
     * @return type
     */
    private function useMaskQty($curStatus,$currMask,$realQty=null){
        $result = 0;
        if ($currMask==='real' && isset($realQty)) return ($realQty!==null)?$realQty:0; 
        if (!is_array($currMask)) return $result;
        foreach ($currMask as $mask=>$curQty) if ($curStatus & $mask) $result += $curQty;    
        return $result;
    }
    
    
    /**
     * Обработка преобразует цену к требуемому выводу
     * @param type $price - цена
     * @param type $priceType - google для гуглевого формата типа 11.22 RUB
     * @return mixed обновленный $prodInfo или false, если тован не подлежит публикации
     */
    public static function formatPrice($price,$priceType,$currency='RUR'){
        $result = $price;
        if ('google'===$priceType)$result = sprintf("%.2f",$price).' ' . $currency;
        if ('d2'===$priceType)$result = sprintf("%.2f",$price);
        return $result;
    }

    /**
     * Подготовка изображений
     * @param type $prodInfo массив данных о текущем товаре
     * @return array массив найденных фотографий
     */
    private function updPhoto($prodInfo){
        if (!is_array($prodInfo)) return $prodInfo;
        
        if (false === $this->getParam('use_picture',false)){
            $prodInfo['picture'] = false;
            return $prodInfo;      
        }
        
        //Свойства, влияющие на вывод изображений     
        $useGpicture = $this->getParam('use_gpicture',false); //Изображения товара, подготовленное для Гугля
        $picSize = $this->getParam('pic_size','big','routeitem'); //"pic_size" => "big", //Размер картинки (small по-умолчанию, medium, big)
        $picNums = $this->getParam('pic_nums',0,'int'); //Количество выводимых изображений, если 0, то все.
        $picWatermark = $this->getParam('pic_watermark',false); //'all' - добавлять любую, watermark - добавлять только с вод.знаками, nowatermark - добавлять кроме водяных знаков
        $pic_real_maxSize = $this->getParam('pic_real_maxSize',false); //Макс.разм типа array('w'=>700, 'h'=>700, 'maxside'=>123, 'minside'=>123)
        $pic_real_minSize = $this->getParam('pic_real_minSize',false); //Мин.разм array('w'=>700, 'h'=>700, 'maxside'=>123, 'minside'=>123)
        
        //Подключим картинки
        $pictureArr = $prodInfo['picture_arr'];
        if (!in_array($picSize, array('big','medium','small'))) $picSize = 'big';
        
        $result = array();
        $pCnt = 0;
        if ($useGpicture && isset($pictureArr['gpic']) && is_array($pictureArr['gpic'])) {
            
            $pictureArr['gpic']['url'] = $this->finUpdURL($pictureArr['gpic']['url'], 'prod_img');
        
            if ($picWatermark==='watermark' && isset($pictureArr['gpicw']) && is_array($pictureArr['gpicw'])) $result[] = $pictureArr['gpicw'];
            elseif (isset($pictureArr['gpic']) && is_array($pictureArr['gpic'])) $result[] = $pictureArr['gpic'];
            $pCnt++;
        }
        if (isset($pictureArr[$picSize]) && is_array($pictureArr[$picSize])){
            foreach($pictureArr[$picSize] as $pnNum=>$pArr) {
                if (!empty($picNums) && $pCnt>=$picNums) break;
                if (is_array($pArr) && (
                        $picWatermark==='all' 
                        || ($picWatermark==='watermark' && !empty($pArr['watermark'])) 
                        || ($picWatermark==='nowatermark' && empty($pArr['watermark'])))) {
                    $pArr['url'] = $this->finUpdURL($pArr['url'],'prod_img');
                    
                    //Блокировка изображений по размеру
                    $picSizeStr = '';
                    $curPhotoSize = false;
                    if (!empty($pArr['width']) && !empty($pArr['height'])){
                        $curPhotoSize = array('w'=>$pArr['width'], 'h'=>$pArr['height']);
                    }
                    if ($curPhotoSize){
                        $curPhotoMaxSize = ($curPhotoSize['w']>$curPhotoSize['h'])?$curPhotoSize['w']:$curPhotoSize['h'];
                        $curPhotoMinSize = ($curPhotoSize['w']<$curPhotoSize['h'])?$curPhotoSize['w']:$curPhotoSize['h'];
                        if (is_array($pic_real_maxSize)) {
                            if (isset($pic_real_maxSize['w']) && $curPhotoSize['w']>$pic_real_maxSize['w']) continue;
                            if (isset($pic_real_maxSize['h']) && $curPhotoSize['h']>$pic_real_maxSize['h']) continue;
                            if (isset($pic_real_maxSize['maxside']) && $curPhotoMaxSize>$pic_real_maxSize['maxside']) continue;
                            if (isset($pic_real_maxSize['minside']) && $curPhotoMinSize>$pic_real_maxSize['minside']) continue;
                        }
                        if (is_array($pic_real_minSize)) {
                            if (isset($pic_real_minSize['w']) && $curPhotoSize['w']<$pic_real_minSize['w']) continue;
                            if (isset($pic_real_minSize['h']) && $curPhotoSize['h']<$pic_real_minSize['h']) continue;
                            if (isset($pic_real_minSize['maxside']) && $curPhotoMaxSize<$pic_real_minSize['maxside']) continue;
                            if (isset($pic_real_minSize['minside']) && $curPhotoMinSize<$pic_real_minSize['minside']) continue;
                        }
                    }else{//Если нет размера изображения, при этом есть исключения по размерам, то не выводим изображение
                        if (is_array($pic_real_maxSize) || is_array($pic_real_minSize)) continue;
                    }
        
                    //Блокировка вывода размера изображения
                    if (false===$this->getParam('pic_size_view',false)) {
                        unset($pArr['width']);
                        unset($pArr['height']);
                    }

                    $result[] = $pArr;
                }
                $pCnt++;
            }
        }
        
        //Дополнительные изображения жестко заданные для всех товаров
        $pic_add = $this->getParam('pic_add',false);
        if (is_array($pic_add)) {
            foreach($pic_add as $pnNum=>$pArr) {
                if (empty($pArr['url'])) continue;
                $pArr['url'] = $this->finUpdURL($pArr['url'],'prod_img');
                //Блокировка вывода размера изображения
                if (false===$this->getParam('pic_size_view',false)) {
                    unset($pArr['width']);
                    unset($pArr['height']);
                }
                $result[] = $pArr;
                $pCnt++;
            }
        }
        
        if (count($result)) $prodInfo['picture'] = $result;
        else $prodInfo['picture'] = false;
        
        //Блокировка товаров по отсутствию изображений
        if (true ===$this->getParam('only_picture_complete',false) && $prodInfo['picture']===false) return false;
        
        return $prodInfo;      
    }
    
    /**
     * Генерация Яндекс.акций
     * @return bool результат операции
     */
    private function generateActions(){
        if (!$this->getParam('use_actions', true)) return false;
        
        $result = true;
        $ya_actions = $this->getParam('ya_actions',false);
        if (is_array($ya_actions)) {
            
            $counter = 0;
            foreach ($ya_actions as $actionKey=>$actionArr) {
                
                if (empty($this->actionsView[$actionKey])) continue;
                    
                if (isset($actionArr['categories']) && is_array($actionArr['categories'])) {
                    foreach ($actionArr['categories'] as $rootCatId){
                        $this->actionsCats[$actionKey]["$rootCatId"] = $rootCatId;
                        if (isset(self::$catArr["$rootCatId"]['full_list'])) foreach (self::$catArr["$rootCatId"]['full_list'] as $catId){
                            $this->actionsCats[$actionKey]["$catId"] = $catId;
                        }
                    }
                }    
                $actionArr['products'] = (isset($this->actionsProds[$actionKey]) 
                        && is_array($this->actionsProds[$actionKey]) 
                        && count($this->actionsProds[$actionKey]))?$this->actionsProds[$actionKey]:false;
                $actionArr['categories'] = (isset($this->actionsCats[$actionKey])
                        && is_array($this->actionsCats[$actionKey]) 
                        && count($this->actionsCats[$actionKey]))?$this->actionsCats[$actionKey]:false;
                $actionArr['gifts'] = (isset($this->actionsGifts[$actionKey])
                        && is_array($this->actionsGifts[$actionKey]) 
                        && count($this->actionsGifts[$actionKey]))?$this->actionsGifts[$actionKey]:false; 
                if ($actionArr['products']===false && $actionArr['categories']===false) continue;
                if ($actionArr["type"]==='gift with purchase' && $actionArr['gifts']===false) continue;
                
                if (empty($counter)) $this->render(array(),'actions_header');
                $this->render($actionArr,'action');
                $counter++;
            }
            if (!empty($counter)) $this->render(array(),'actions_footer');
        }
        
        SysLogs::addLog('Feedgen: Actions generate Ok!');
        return $result;
    }
    
    /**
     * Генерация футера
     * @return bool результат операции
     */
    private function generateFooter(){
        $result = $this->render(array(),'footer');
        
        SysLogs::addLog('Feedgen: Footer generate Ok!');
        return $result;
    }
    
    
    /**
     * Получает название файла статики для данного фида Формат "ПРЕФИКС+РЕФЕР+АЛИАСРЕГИОНА.ТИП"
     * @param string $fullName если 'full', то выдается полный путь к файлу, если 'fulltmp' то полный путь к tmp, иначе только название файла
     * @param string $ts если 'time', то к файлу перед расширением допишется ГГГГММДДЧЧММСС
     * @param string $randType если 'rand', то в конце будет добавлено еще рандомное значение 0-9999
     * @return string имя файла
     */
    private function getFeedFileName($fullName='',$ts='',$randType=''){

        $toRefer = (!empty($this->toRefer))?$this->toRefer:$this->refer;
        
        $result = '';
        $prefix = SysBF::getFrArr(Glob::$vars['feed_conf'],'file_prist','');
        $extStr = '.xml';
        if ('csv' === $this->feedFormat){$extStr = '.csv';}
        if ('json' === $this->feedFormat){$extStr = '.json';}
        $regAlias = ($this->regAlias!=='default')?('_'.$this->regAlias):('_'.Glob::$vars['feed_conf']['def_reg_alias_to']);
        if ($fullName==='full') $result .= FEEDS_FILESPATH;
        elseif ($fullName==='fulltmp') {
            $result .= FEEDS_TMP_FILESPATH;
            $result .= 'tmp_';
            if ($randType==='fulltmp') $result .= rand(0,9999);
        }
        
        $result .= $prefix . $toRefer . $regAlias;
        if ($ts==='time') $result .= date("YmdHis");
        $result .= $extStr;
        
        return $result;
    }
    
    /**
     * Получает название файла лога для данного фида Формат "ПРЕФИКС+РЕФЕР+АЛИАСРЕГИОНА.log"
     * @param type $fullName если 'full', то выдается полный путь к файлу, иначе только название файла
     * @return string имя файла
     */
    private function getLogFileName($fullName=''){
        $result = '';
        if ($fullName==='full') $result .= FEEDS_LOGSPATH;
        $prefix = SysBF::getFrArr(Glob::$vars['feed_conf'],'file_prist','');
        $regAlias = ($this->regAlias!=='default')?('_'.$this->regAlias):('_'.Glob::$vars['feed_conf']['def_reg_alias_to']);
        $result .= $prefix . $this->refer . $regAlias . '.log';        
        return $result;
    }
    
    /**
     * Выдает проверенный полный путь к файлу требуемого шаблона компонента
     * @param type $tplType
     * @return boolean
     */
    private function getTplFileName($tplType){
        $result = true;
        
        $tplFile = false;
        $tpl_arr = $this->getParam('tpl_arr',array());  
        if (!empty($tpl_arr[$tplType])) $tplFile = $tpl_arr[$tplType]; 
        
        if (!empty($tplFile)){
            $thisModuleName = Glob::$vars['module'];
            $userFileName = USER_MODULESPATH . $thisModuleName . '/view/feeds/' . $tplFile;
            $appFileName = APP_MODULESPATH . $thisModuleName . '/view/feeds/' . $tplFile;
            if(file_exists($userFileName)) return $userFileName;
            elseif(file_exists($appFileName)) return $appFileName;
        }
    }
    
    /**
     * Выдает данные из массива $item в заданном формате в файл или на экран в зависимости от настроек фида
     * @param mixed $item массив входных данных
     * @param string $tplType тип шаблона: header, footer, cat_header, cat_footer, product, category, vendor, json, csv, csvheader, printr, если не задан и $item - строка, то выдаем эту сроку. 
     * @return boolean
     */
    private function render($item='', $tplType='', $param='') {
        $result = true;
        
        if (!is_array($param)) $param = array();
        
        //Различные варианты вывода
        $fidTxt = '';
        if (empty($tplType) && !is_array($item)) {//Вывод простой строки
            $fidTxt = $item;
        }
        
        if (is_array($item)){ //Все остальные типы вывода предполагают что данные приходят в массиве $item
            
            if ($tplType==='header'){
                
                if ('xml' === $this->feedFormat) {
                    $fidTxt .= '<?xml version="1.0" encoding="'.$this->charset.'"?>'."\n";
                    $dtd_String = $this->getParam('dtd_string','');
                    if (!empty($dtd_String)) $fidTxt .= $dtd_String . "\n";
                }
                
                if ('csv'!==$this->feedFormat && $tplFile=$this->getTplFileName($tplType)){
                    require $tplFile;
                    $fidTxt .= $tplBlock;
                }
                
                $headerAddBlock = $this->getParam('header_add_block','');
                if (!empty($headerAddBlock)) {
                    $fidTxt .= $headerAddBlock; 
                    if ('csv' === $this->feedFormat) $fidTxt .= "\n";
                }    
            }elseif ($tplType==='footer'){
                $footerAddBlock = $this->getParam('footer_add_block','');
                if (!empty($footerAddBlock)) {
                    $fidTxt .= $footerAddBlock; 
                    if ('csv' === $this->feedFormat) $fidTxt .= "\n";
                }
                
                if ('csv'!==$this->feedFormat && $tplFile=$this->getTplFileName($tplType)){
                    require $tplFile;
                    $fidTxt .= $tplBlock;
                }
                
            }elseif (in_array($tplType,array(
                'cat_header','category','cat_footer',
                'vend_header','vendor','vend_footer',
                'prod_header','prod_footer','product',
                'actions_header','action','actions_footer'))){
                
                //JSON будем пока реализовывать через систему шаблонов, чтоб приличный внешний вид был
                $csv_head_cat=$this->getParam('csv_head_cat',false);
                $csv_head_prod=$this->getParam('csv_head_prod',false);
                if ('csv' === $this->feedFormat
                        && ((($tplType==='cat_header' || $tplType==='category') && is_array($csv_head_cat))
                        ||(($tplType==='product' || $tplType==='prod_header') && is_array($csv_head_prod)))){
                    
                    if ($tplType==='cat_header' || $tplType==='category'){
                        $outArr = array();
                        $headCnt = 0;
                        foreach($csv_head_cat as $dataArr){
                            if ($tplType==='cat_header'){
                                if (isset($dataArr['name'])){
                                    $outArr[] = $dataArr['name'];
                                    $headCnt++;
                                }else{
                                    $outArr[] = '';
                                }
                            }else{
                                if(isset($dataArr['value'])) $outArr[] = $dataArr['value'];
                                elseif (!empty($dataArr['alias']) && isset($item[$dataArr['alias']])) $outArr[] = $item[$dataArr['alias']];
                                else $outArr[] = '';
                            }
                        }            
                        $csvDelim = $this->getParam('csv_delim',',');
                        $csvEnclosure = $this->getParam('csv_enclosure','"');
                        if ($tplType!=='cat_header' || $headCnt) $fidTxt .= SysBF::getCSVLine($outArr,$csvDelim,$csvEnclosure);
                    }
                    
                    if ($tplType==='product' || $tplType==='prod_header'){
                        $outArr = array();
                        $headCnt = 0;
                        foreach($csv_head_prod as $dataArr){
                            if ($tplType==='prod_header'){
                                if (isset($dataArr['name'])){
                                    $outArr[] = $dataArr['name'];
                                    $headCnt++;
                                }else{
                                    $outArr[] = '';
                                }
                            }else{
                                if(isset($dataArr['value'])) $outArr[] = $dataArr['value'];
                                elseif (!empty($dataArr['alias']) && isset($item[$dataArr['alias']])) $outArr[] = $item[$dataArr['alias']];
                                else $outArr[] = '';
                            }
                        }            
                        $csvDelim = $this->getParam('csv_delim',',');
                        $csvEnclosure = $this->getParam('csv_enclosure','"');
                        if ($tplType!=='prod_header' || $headCnt) $fidTxt .= SysBF::getCSVLine($outArr,$csvDelim,$csvEnclosure);
                    }
                }elseif ('csv'!==$this->feedFormat && $tplFile = $this->getTplFileName($tplType)){
                    require $tplFile;
                    $fidTxt .= $tplBlock;
                }
                
            }elseif ($tplType==='json'){
                $fidTxt .= json_encode($item);
            }elseif ($tplType==='csvheader'){
                $csvDelim = $this->getParam('csv_delim',',');
                $csvEnclosure = $this->getParam('csv_enclosure','"');
                $itemNames = array();
                foreach ($item as $key => $value) {
                    $itemNames[$key] = $key; 
                }
                $fidTxt .= SysBF::getCSVLine($itemNames,$csvDelim,$csvEnclosure);                
            }elseif ($tplType==='csv'){
                $csvDelim = $this->getParam('csv_delim',',');
                $csvEnclosure = $this->getParam('csv_enclosure','"');
                $fidTxt .= SysBF::getCSVLine($item,$csvDelim,$csvEnclosure);
            }elseif ($tplType==='printr'){
                $fidTxt .= print_r($item);
            }
  
        }
        
        if ($this->charset !== 'utf-8') $fidTxt = SysBF::Utf2Win($fidTxt);
        if ($this->saveFileLink != false) fwrite($this->saveFileLink,$fidTxt);
        else echo $fidTxt;
        
        return $result;
    }
    
    /**
     * Переименовывает файл
     * @param string $fileFrom старое имя файла
     * @param string $fileTo новое имя файла
     * @param string $bakOk если 'bak', то делаем bak файл со старым содержимым
     * @return bool результат операции
     */
    public function renameFile($fileFr,$fileTo,$bakOk=''){
        if (file_exists($fileFr)){
            if ($bakOk==='bak' && file_exists($fileTo)) rename($fileTo,$fileTo.'bak'); //Если такой файл есть, переименуем в bak
            elseif (file_exists($fileTo)) unlink($fileTo); //Если такой файл есть, удалим
            if (rename($fileFr,$fileTo)) return true; //Переименуем
            else return false;
        } else return false;
    }  
    
    /**
     * Устанавливает кеш с заданным временем жизни
     * @param string $key ключ кеширования (строка произвольная, от которой будет браться хеш)
     * @param mixed $item кешируемое значение (любое)
     * @param int $cacheLag время жизни кеша в секундах, если не установлено или 0, то бесконечное
     * @return boolean
     */
    private static function setCache($key,$item,$cacheLag=0){
        //Вы можете переопределить этот метод в трейте, если к примеру захотите использовать key-value базу данных для кеша
        $cacheFileSave = FEEDS_CACHEPATH.'json'.md5($key).'.txt';
        
        $cacheLag = intval($cacheLag);
        $cacheTs = ($cacheLag>0)?(time()+$cacheLag):0;
        $cacheArr = array('key'=>$key, 'cache_dt'=>date("Y-m-d G:i:s",$cacheTs), 'cache_ts'=>$cacheTs, 'item'=>$item);
        $cacheJson = json_encode($cacheArr);
        $res = SysBF::saveFile($cacheFileSave, $cacheJson, "w");

        return $res;
    }

    /**
     * Получает значение кеша, если оно не устарело
     * @param type $key ключ кеширования (строка произвольная, от которой будет браться хеш)
     * @return mixed - содержимое кешаавы
     */
    private static function getCache($key){
        //Вы можете переопределить этот метод в трейте, если к примеру захотите использовать key-value базу данных для кеша
        $cacheFileSave = FEEDS_CACHEPATH.'json'.md5($key).'.txt';
        
        if (!empty(Glob::$vars['no_cache'])) return null;
                
        if(!file_exists($cacheFileSave)) return null;
        $fp = file_get_contents($cacheFileSave);
        if ($fp===false) {
            SysLogs::addError("Feedgen Error: can't open cache file [$cacheFileSave]!");
            return null;
        }else{
            $cacheArr = json_decode($fp,true);
            if ($cacheArr === null) {
                SysLogs::addError("Feedgen Error: Cache json_decode error!");
                return null;
            }
        
            if (is_array($cacheArr) && isset($cacheArr['item']) && (empty($cacheArr['cache_ts']) || $cacheArr['cache_ts']>time())){
                return $cacheArr['item'];
            }else{
                //Старый кеш или ошибка в структуре массива
                return null;
            }
            
            return null;
        }
    }
    
    /**
     * Возвращает timestamp с секундами по строке типа "2018-09-01T12:44:11.278Z"
     * @param type $tmStr
     * @return type
     */
    public static function tmstFrStr($tmStr=''){
        if (empty($tmStr)) return microtime(true);
        $tsArr = date_parse_from_format("Y-m-d?H:i:s.????", $tmStr);
        return mktime ($tsArr['hour'], $tsArr['minute'], $tsArr['second'], $tsArr['month'], $tsArr['day'], $tsArr['year']);
    }

    /**
     * Проверяет входит ли заданная категория в список категорий начиная от рута и ниже
     * @param mixed $rootCat значение идентификатора рутовой категории, либо массив таких значений (строковых)
     * @param mixed $catId значение идентификатора проверяемой категории
     * @return bool true, если текущая категория находится под рутом или false, если не найдена
     */
    public static function checkFrRootCat($rootCat='',$catId=0){
        
        if (empty($catId)) return false;
        if (!is_array($rootCat) && empty($rootCat)) return false;

        if (is_array($rootCat)){
            foreach($rootCat as $curCatId){
                if ($curCatId==$catId) return true;
                if (isset(self::$catArr["$curCatId"]) && is_array(self::$catArr["$curCatId"])
                    && isset(self::$catArr["$curCatId"]['full_list']) && is_array(self::$catArr["$curCatId"]['full_list'])
                    && in_array(strval($catId), self::$catArr["$curCatId"]['full_list'])) {
                        return true;
                }
            }
        }else{
            if ($rootCat==$catId) return true;
            if (isset(self::$catArr["$rootCat"]) && is_array(self::$catArr["$rootCat"])
                && isset(self::$catArr["$rootCat"]['full_list']) && is_array(self::$catArr["$rootCat"]['full_list'])
                && in_array(strval($catId), self::$catArr["$rootCat"]['full_list'])) {
                    return true;
            }
        }
            
        return false;
    }
    
    /**
     * Разбирает значен
     * @param type $param параметр из конфига (массив или значение)
     * @param mixed $catId значение идентификатора проверяемой категории
     * @return mixed найденный результат или null, если ничего не найдено root_cats
     */
    public static function getRootCatVal($param=array(),$catId=0){
        if (!is_array($param)) return $param;
        if (empty($catId)) return false;

        $result = null;
        foreach ($param as $paramArr){
            if (!isset($paramArr["value"])) continue;
            if (!isset($paramArr["root_cats"]) || self::checkFrRootCat($paramArr["root_cats"],$catId)) $result = $paramArr["value"];
        }
        
        return $result;
        
    }

} 
