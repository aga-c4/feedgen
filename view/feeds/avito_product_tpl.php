<?php
################################################################################
# Шаблон товара
################################################################################
$tplBlock = '<Ad>'; 

if (isset($item['offer_id'])) $tplBlock .='<Id>'.$item['offer_id'] ."</Id>\n"; 

$tplBlock .= '<DateBegin>'.date("Y-m-d",mktime(0,0,0,intval(date('n')), intval(date('j'))-1,intval(date('Y')))).'T00:00:00+03:00</DateBegin>'."\n";
$tplBlock .= '<DateEnd>'.date("Y-m-d",mktime(0,0,0,intval(date('n')), intval(date('j'))+30,intval(date('Y')))).'T23:59:59+03:00</DateEnd>'."\n";
$tplBlock .= '<AllowEmail>'.$this->getParam('allow_email','Нет').'</AllowEmail>'."\n";

if ($this->getParam('manager_name',false)) $tplBlock .= '<ManagerName>'.$this->getParam('manager_name','').'</ManagerName>'."\n";

if ($this->getParam('contact_phone',false)) {//Можно задать просто телефон, либо привязать его к корневым категориям
    $curVal = Feedgen::getRootCatVal($this->getParam('contact_phone',null),strval($item['cat_id']));
    if (null!==$curVal) {
        $tplBlock .= '<ContactPhone>'.$curVal.'</ContactPhone>'."\n";
    }else{ //Нет телефона
        $tplBlock .= '<ContactMethod>В сообщениях</ContactMethod>'."\n";
    }
}else{ //Нет телефона
    $tplBlock .= '<ContactMethod>В сообщениях</ContactMethod>'."\n";
}

$tplBlock .= '<Address>' . $this->getParam('address','') . '</Address>'."\n";

if ($this->getParam('condition',false)) {//Можно задать просто телефон, либо привязать его к корневым категориям
    $curVal = Feedgen::getRootCatVal($this->getParam('condition',null),strval($item['cat_id']));
    if (null!==$curVal) $tplBlock .= '<Condition>'.$curVal.'</Condition>'."\n";
}

if ($this->getParam('avito_goods_type',false)) {//Можно задать просто телефон, либо привязать его к корневым категориям
    $curVal = Feedgen::getRootCatVal($this->getParam('avito_goods_type',null),strval($item['cat_id']));
    if (is_array($curVal) && isset($curVal["avito_cat"])) {
        $tplBlock .= '<Category>' . $curVal['avito_cat'] .'</Category>' . "\n";
        if (!empty($curVal["avito_goods_type"])) $tplBlock .= '<GoodsType>' . $curVal['avito_goods_type'] . '</GoodsType>'."\n";
        if (!empty($curVal["type_id"])) $tplBlock .= '<TypeId>' . $curVal['type_id'] . '</TypeId>'."\n";
    }
}

if (isset($item['name'])) $tplBlock .= '    <Title>'.$item['name'] .'</Title>'."\n";
if (isset($item['price'])) $tplBlock .= '    <price>'.Feedgen::formatPrice($item['price'],$item['price_type'],$item['currency']) .'</price>'."\n";
if (isset($item['count'])) $tplBlock .= '    <stock>'.$item['count'] .'</stock>'."\n";
if (isset($item['picture']) && is_array($item['picture'])) {
    $pctCounter = 0;
    foreach ($item['picture'] as $inItem) {
        if (empty($pctCounter)) $tplBlock .= '    <Images>'."\n";
        $tplBlock .= '        <Image url="'.$inItem['url'] .'"/>'."\n";
        $pctCounter++;
    }
    if (!empty($pctCounter)) $tplBlock .= '    </Images>'."\n";
}
if (isset($item['description'])) $tplBlock .= '    <description>' . $item['description'] . '</description>'."\n";

$tplBlock .= '</Ad>
';  
