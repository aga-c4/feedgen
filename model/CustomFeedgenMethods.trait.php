<?php
/**
 * Дополнительные пользовательские методы работы с классом фидогенератора, 
 * вы можете создать свой подобный файл не под гитом и расположить его в папке 
 * app/modules/feedgen/model. Данный файл содержит пример заполнения основных
 * свойств товара, вы можете использовать дополнительные свойства товара, 
 * которые вдальнейшем будете использовать в шаблонах. Данный файл будет работать
 * со старой версией CMS.MNBV, которая в настоящий момент не выложена в общий 
 * доступ. 
 *
 * Created by Konstantin Khachaturyan (aga-c4)
 * @author Konstantin Khachaturyan (AGA-C4)
 * Date: 29.05.20
 * Time: 23:58
 */

trait CustomFeedgenMethods {
    
    /**
     * Формирование массива категорий
     * @param array массив параметров
     * @return boolean
     */
    private static function genCatArr($param='') {
        if (!is_array($param)) $param = array('cache_lag'=>Glob::$vars['feed_conf']['cache_lag']);
        $cacheLag = SysBF::getFrArr($param,'cache_lag',Glob::$vars['feed_conf']['cache_lag'],'intval');
        $useCache = SysBF::getFrArr($param,'use_cache',Glob::$vars['feed_conf']['use_cache']);
        $useParams = SysBF::getFrArr($param,'use_params',false);
        $trueTime = time() - $cacheLag;
        
        //Подгрузим данные из кеша
        if ($useCache) self::$catArr = self::getCache('categoryArr');
        
        if (self::$catArr !== null) {
            SysLogs::addLog('Feedgen: Categories array already exist');
        }else{            
            ##################[ Формирование массива категорий ]################
            self::$catArr = array();            
            $myDb = SysStorage::getLink('main');
            $domainImg = SysBF::getFrArr($param,'domain_img',Glob::$vars['feed_conf']['def_domain_img']);
            $imgPAth = '/data/page_doska_ob/';
            
            $res = $myDb->query("select zapid, razdid, name, visible from page_doska_ob where tip=1 order by pozid,name;");
            while ($catInfo = DbMysql::mysql_fetch_object($res)){
                if (!empty($catInfo->zapid)){
                    if (!isset(self::$catArr["$catInfo->zapid"])) self::$catArr["$catInfo->zapid"] = array();
                    self::$catArr["$catInfo->zapid"]['cat_id'] = strval($catInfo->zapid);
                    self::$catArr["$catInfo->zapid"]['cat_active'] = ($catInfo->visible==='on')?true:false;
                    self::$catArr["$catInfo->zapid"]['cat_parent_id'] = strval($catInfo->razdid);
                    self::$catArr["$catInfo->zapid"]['cat_name'] = $catInfo->name; //Название категории
                    self::$catArr["$catInfo->zapid"]['cat_prod_prifix'] = $catInfo->name; //Префикс товаров категории
                    if (!isset(self::$catArr["$catInfo->razdid"])) self::$catArr["$catInfo->razdid"] = array();
                    if (!isset(self::$catArr["$catInfo->razdid"]['list'])) self::$catArr["$catInfo->razdid"]['list'] = array();
                    self::$catArr["$catInfo->razdid"]['list'][] = "$catInfo->zapid"; //массив вложенных категорий
                    self::$catArr["$catInfo->zapid"]['cat_url'] = "/catalog/$catInfo->zapid/"; //URL категории
                    self::$catArr["$catInfo->zapid"]['cat_url'] = "/catalog/$catInfo->zapid/"; //Изображение категории
                    self::$catArr["$catInfo->zapid"]['picture_arr'] = array(
                        'small' => array(
                            '1' => array('url'=>$domainImg.$imgPAth.'p'.$catInfo->zapid.'_1min.jpg','watermark'=>false),
                        ),
                        'medium' => array(
                            '1' => array('url'=>$domainImg.$imgPAth.'p'.$catInfo->zapid.'_1.jpg','watermark'=>false),
                        ),
                        'big' => array(
                            '1' => array('url'=>$domainImg.$imgPAth.'p'.$catInfo->zapid.'_1big.jpg','watermark'=>false),
                        ),
                    );
                    
                    //Необходимо привязать параметры к категориям для дальнейшего вывода
                    if ($useParams) {
                        
                        //Для теста параметров
                        if (self::$catArr["$catInfo->zapid"]['cat_id'] === '28770'){
                            self::$catArr["$catInfo->zapid"]["attr"] = array("1","2","3");
                        }
                        
                    }
                
                }
            }
            
            ##################[ /Формирование массива категорий ]###############
            if ($useCache) self::setCache('categoryArr',self::$catArr,$cacheLag);
            SysLogs::addLog('Feedgen: Categories array generate Ok!');
        }
        return true;
    }  
    
    /**
     * Формирование массивов атрибутов товаров
     * Массивы могут быть сформированы в рамках формирования категорий, если это удобно.
     * @param array массив параметров
     * @return boolean
     */
    private static function genAttrArrs($param='') {
        if (!is_array($param)) $param = array('cache_lag'=>Glob::$vars['feed_conf']['cache_lag']);
        $cacheLag = SysBF::getFrArr($param,'cache_lag',Glob::$vars['feed_conf']['cache_lag'],'intval');
        $useCache = SysBF::getFrArr($param,'use_cache',Glob::$vars['feed_conf']['use_cache']);
        $trueTime = time() - $cacheLag;
        
        //Подгрузим данные из кеша
        if ($useCache) {
            self::$prodAttr = self::getCache('prodAttr');
            self::$prodAttrType = self::getCache('attrTypeArr');
        }
        
        if (self::$prodAttrType !== null) {
            SysLogs::addLog('Feedgen: Attr types array already exist');
        }else{            
            ##################[ Формирование массива атрибутов ]################
            self::$prodAttrType = array();
            
            //Для теста жестко установим значения ------------------------------
            
            //Типы параметров (реальные или списки)
            //Формат {"{attrid}"=>{"id"=>"1","sort"=>100,"sort"=>100,"name"=>"","alias"=>"","type"=>"(value/list)","short"=>false,
            //"vals_list"=>array("id"=>123,"alias"=>"","vals_list"=>...)}}
            self::$prodAttrType = array(
                "1" => array("id"=>"1","sort"=>1,"name"=>"Параметр1","alias"=>"param1","type"=>"value","filter"=>false,"short"=>true),
                "2" => array("id"=>"2","sort"=>100,"name"=>"Параметр2","alias"=>"param2","type"=>"value","filter"=>false,"short"=>false),
                "3" => array("id"=>"3","sort"=>100,"name"=>"Параметр3","alias"=>"param3","type"=>"list","filter"=>true,"short"=>true, "values"=>array(
                    "1" => array("id"=>1,"alias"=>"Val1","value"=>"Value1"),
                    "2" => array("id"=>2,"alias"=>"Val2","value"=>"Value2"),
                    "3" => array("id"=>3,"alias"=>"Val3","value"=>"Value3"),
                )),
            );
            
            if ($useCache) {
                self::setCache('attrTypeArr',self::$prodAttrType,$cacheLag);
            }
            SysLogs::addLog('Feedgen: Attr type array generate Ok!');
        }
         
        
        if (self::$prodAttr !== null) {
            SysLogs::addLog('Feedgen: Attr array already exist');
        }else{
            self::$prodAttr = array();
            
            //Параметры товаров в привязкам к фильтрамФормат {"prodid"=>{"typeid"=>123, "value"=>""}}
            self::$prodAttr = array(   
                "28603" => array( //категория 28770
                    "1" => 111,
                    "2" => "Str1",
                    "3" => "1",
                ),
                "23982" => array( //категория 28770
                    "1" => 222,
                    "2" => "Str2",
                    "3" => "2",
                )
            );
            //------------------------------------------------------------------
            
            if ($useCache) {
                self::setCache('prodAttr',self::$prodAttr,$cacheLag);
            }
            SysLogs::addLog('Feedgen: Attr array generate Ok!');
        }
        return true;
    }
    
    /**
     * Формирование массива вендоров
     * @param array массив параметров
     * @return boolean
     */
    private static function genVendArr($param='') {
                if (!is_array($param)) $param = array('cache_lag'=>Glob::$vars['feed_conf']['cache_lag']);
        $cacheLag = SysBF::getFrArr($param,'cache_lag',Glob::$vars['feed_conf']['cache_lag'],'intval');
        $useCache = SysBF::getFrArr($param,'use_cache',Glob::$vars['feed_conf']['use_cache']);
        $trueTime = time() - $cacheLag;
        
        //Подгрузим данные из кеша
        if ($useCache) self::$vendArr = self::getCache('vendorsArr');
        
        if (self::$vendArr !== null) {
            SysLogs::addLog('Feedgen: Vendors array already exist');
        }else{            
            ##################[ Формирование массива вендоров ]################
            
            /*
            self::$vendArr = array();

            $myDb = SysStorage::getLink('main');
            $query = "select vendorname from page_doska_ob where tip=0 and visible='on' group by vendorname order by vendorname;";
            $res = $myDb->query($query);
            while ($prod = DbMysql::mysql_fetch_object($res)){
            
                $item = array();
                $prod->vendorname = trim($prod->vendorname);
                $item['vend_alias'] = $item['vend_id'] = SysBF::updTranslitStr(strtolower($prod->vendorname));
                $item['vend_name'] = $prod->vendorname;
                if (!empty($item['vend_alias'])) self::$vendArr[$item['vend_alias']] = $item;
                
            }
            */
            
            ##################[ /Формирование массива вендоров ]###############
            if ($useCache) self::setCache('vendorsArr',self::$vendArr,$cacheLag);
            SysLogs::addLog('Feedgen: Vendors array generate Ok!');
        }
        return true;
    }
    
    /**
     * Формирование массива товаров
     * @param array массив параметров
     * @return array массив с товарами, либо false, если больше товаров нет
     */
    private static function getProdArr($param='') {
        static $counter = 0; 
        $result = false;
        if (!is_array($param)) $param = array('max_size'=>0, 'attr_view'=>false);
        $maxSize = SysBF::getFrArr($param,'max_size',0,'intval');
        $attrView = SysBF::getFrArr($param,'attr_view',false);
        $use_netto_sw = SysBF::getFrArr($param,'use_netto_sw',false);
        $use_gross_sw = SysBF::getFrArr($param,'use_gross_sw',false);
        $sort_by = SysBF::getFrArr($param,'sort_by',false);
        $domainImg = SysBF::getFrArr($param,'domain_img',Glob::$vars['feed_conf']['def_domain_img']);
        $imgPAth = '/data/page_doska_ob/';
        
        ##################[ Формирование массива товаров ]################
        $myDb = SysStorage::getLink('main');
        
        $sortStr = '';
        if (!empty($sort_by)){
            $sort_by = strtolower($sort_by);
            if (false!==strpos($sort_by, 'prodid')) $sortStr = ' order by zapid';
            elseif (false!==strpos($sort_by, 'catid')) $sortStr = ' order by razdid';
            elseif (false!==strpos($sort_by, 'price')) $sortStr = ' order by price';
            elseif (false!==strpos($sort_by, 'cost')) $sortStr = ' order by cost';
            //elseif ($sort_by === 'profit') $sortStr = ' order by razdid';
            //elseif ($sort_by === 'sales_rate') $sortStr = ' order by razdid';
            //elseif ($sort_by === 'gross_profit') $sortStr = ' order by razdid';
            if (!empty($sortStr) && false!==strpos($sort_by, 'desc')) $sortStr .= ' desc';
        }

        $query = "select * from page_doska_ob where tip=0$sortStr;";
        $res = $myDb->query($query);
        while ($prod = DbMysql::mysql_fetch_object($res)){
            if (!empty($prod->zapid)){
                if ($result === false) $result = array();                                
                $item = array();
                
                $item['prod_active'] = ($prod->visible==='on')?true:false;
                
                $item['cat_id'] = strval($prod->razdid);
                $item['prod_id'] = $prodId = strval($prod->zapid);
                $item['1c_id'] = $prodId = strval($prod->zapid);
                $item['shop_sku'] = strval($prod->zapid);
                
                //$item['offer_available'] = true;
                
                $item['offer_bid'] = null;
                $item['offer_credit'] = null;
                
                $item['type_prefix'] = null;
                
                $item['vendor_id'] = SysBF::updTranslitStr(strtolower(trim($prod->vendorname))); //$prod->id_vendor;
                $item['vendor'] = $prod->vendorname;
                $item['vendor_code'] = null;
                
                $item['vendor_arr'] = array();
                $item['vendor_arr']['vend_id'] = SysBF::updTranslitStr(strtolower(trim($prod->vendorname)));
                $item['vendor_arr']['vend_alias'] = $item['vendor_arr']['vend_id'];
                $item['vendor_arr']['vend_name'] = $prod->vendorname;
                $item['vendor_arr']['vendor_code'] = '';
                $item['manufacturer'] = $prod->vendorname;
                
                $item['model'] = $prod->model;
                
                $item['full_name'] = $prod->name;
                $item['short_name'] = $item['vendor'] . ' ' . $item['model'];
                $item['name'] = $item['full_name'];
                
                $item['url'] = "/tov_$prod->zapid/";
                
                $ftov_nds = $prod->nds;
                $ftov_nds = str_replace(',','.',$ftov_nds); //Поменяем , на .
                $ftov_nds = floatval($ftov_nds);
                $ftov_price = $prod->price;
                $real_price = $prod->price*(100+$ftov_nds)/100; 
                        
                $item['price'] = round(floatval($real_price));
                $item['cost'] = round($item['price']*0.7);
                $item['oldprice'] = round($item['price']*1.1);
                $item['sales_rate'] = 0.3; //Среднее количество продаж в сутки
                $item['mrc'] = 0; //Минимальная розничная цена + жесткая, - мягкая (можно скидку), 0 или false - нет
                $item['enable_auto_discounts'] = null;
                $item['currency'] = null;
                $item['delivery'] = true;
                $item['delivery_cat'] = '1 категория доставки';
                //$item['delivery_cost'] = 600;
                //$item['delivery_days'] = 22;
                //$item['pickup_cost'] = 400;
                //$item['pickup_days'] = 21;
                $item['delivery_options'] = null; //array('cost'=>0,'days'=>'21','order_before'=>'13');
                $item['pickup_options'] = null; //array('cost'=>0,'days'=>'0','order_before'=>'13');
                $item['pickup'] = false;
                $item['store'] = false;
                $item['sales_notes'] = null;
                $item['warranty'] = null;
                $item['country'] = $prod->country;
                $item['barcode'] = null;  
                if ($prod->br==='on') $prod->text1 = str_replace("\n", "<br>", $prod->text1);
                if ($prod->abr==='on') $prod->about = str_replace("\n", "<br>", $prod->about);
                $item['description_min'] = $prod->about;
                $item['description_full'] = $prod->text1;
                if (empty($item['description_full'])) $item['description_full'] = $item['description_min'];
                if (empty($item['description_full'])) $item['description_full'] = $item['name'];
                $item['description'] = $item['description_full'];
                
                $item['description_min'] .= "<br>---<br>Артикул: " . ((!empty($prod->partnumber))?$prod->partnumber:'') . "<br>---<br>\n";
                $item['description_full'] .= "<br>---<br>Артикул: " . ((!empty($prod->partnumber))?$prod->partnumber:'') . "<br>---<br>\n";
                
                $item['descr_cdata']= true;
                $item['outlets'] = null;          
                
                //Подключим картинки (к сожалению придется посмотреть в папках)
                $item['picture_arr'] = array(
                    'small' => array(
                        '1' => array('url'=>$domainImg.$imgPAth.'p'.$prodId.'_1min.jpg','watermark'=>true,'width'=>640,'height'=>480),
                    ),
                    'medium' => array(
                        '1' => array('url'=>$domainImg.$imgPAth.'p'.$prodId.'_1.jpg','watermark'=>true,'width'=>1024,'height'=>768),
                    ),
                    'big' => array(
                        '1' => array('url'=>$domainImg.$imgPAth.'p'.$prodId.'_1big.jpg','watermark'=>true,'width'=>1280,'height'=>1024),
                    ),
                    'gpic' => array('url'=>$domainImg.$imgPAth.'p'.$prodId.'_1big.jpg','watermark'=>false,'width'=>600,'height'=>600),
                    'gpicw' => null, //Если есть гуглевое изображение с водяным знаком. то сюда его.
                );
                
                //Установка статусов наличия
                $inStock = InStock_Dealers;
                if (!empty($prod->n1)) {
                    if ($prod->n1==1) $inStock = ($inStock | InStock_Regional_1);
                    elseif ($prod->n1==2) $inStock = ($inStock | InStock_Regional_2);
                    else $inStock = ($inStock | InStock_Regional_3);
                } 
                $item['instock_status'] = $inStock;
                $item['instock_outlets_status'] = array('main_store'=>$inStock,'spb_store'=>InStock_Empty);
                $item['instock_qty'] = $prod->n1; //Если не задано. то будет расчитываться по маске. Если не можем установить, устанавливаем null
                $item['instock_outlets_qty'] = array('main_store'=>$prod->n1,'spb_store'=>0); //Если не задано. то будет расчитываться по маске
                
                if ($use_netto_sw){//Забираем по требованию, может быть ресурсоемкой операцией
                    $item['netto'] = array (//Вес в Кг и габариты в см НЕТТО
                        'weight_unit' => 'кг',
                        'size_unit' => 'см',
                        'weight' => 10,
                        'length' => 20,
                        'width' => 30,
                        'height' => 40,
                    );
                }
                   
                if ($use_gross_sw){//Забираем по требованию, может быть ресурсоемкой операцией
                    $item['gross'] = array (//Вес в Кг и габариты в см БРУТТО
                        'weight_unit' => 'кг',
                        'size_unit' => 'см',
                        'weight' => 60,
                        'length' => 70,
                        'width' => 80,
                        'height' => 90,
                    );  
                }
                
                $d_date = Feedgen::tmstFrStr($prod->d_date);
                $lastedit = Feedgen::tmstFrStr($prod->lastedit);
                $item['ts_create'] = $d_date;
                $item['ts_upd'] = $lastedit;
                $item['ts_upd_price'] = $lastedit;
                $item['ts_upd_photo'] = $d_date;
                
                $result[] = $item;
            }
        }

        ##################[ /Формирование массива товаров ]###############
        SysLogs::addLog('Feedgen: Products array generate Ok!');
        
        //$result = array();
        return $result;
    }
    
    /**
     * Дорабатывает данные о товаре в соответствии с настройками фида, выполняется после updateProdInfo()
     * работает, если определен после основной обработки данных товара.
     * @param type $prodInfo массив данных о товаре
     * @return mixed - либо массив данных о товаре, либо false, если нет возможности его сформировать
     */
    /*
    private function updateProdInfoCustom($prodInfo){
        if (!is_array($prodInfo)) return false;
        return $prodInfo;
    }
    */
    
    /**
     * Генерация кастомных блоков фида, здесь вы можете сформировать свои блоки по аналогии с тем,
     * как это делается в основном классе. Можно с рендером (шаблоны можно задать из конфига), можно без него.
     * В идеале соблюдать приемственность схемы работы с конфигом.
     * @return boolean результат операции
     */
    private function genCustomBlocks() {
        

        SysLogs::addLog('Feedgen: Custom blocks generate Ok!');
        return true;
    }

} 
