<?php
################################################################################
# Шаблон товара
################################################################################
$tplBlock = '<Ad>'; 
if (isset($item['offer_id'])) $tplBlock .='<Id>'.$item['offer_id'] ."</Id>\n"; 
$tplBlock .= '<DateBegin>'.date("Y-m-d",mktime(0,0,0,intval(date('n')), intval(date('j'))-1,intval(date('Y')))).'T00:00:00+03:00</DateBegin>'."\n";
$tplBlock .= '<DateEnd>'.date("Y-m-d",mktime(0,0,0,intval(date('n')), intval(date('j'))+30,intval(date('Y')))).'T23:59:59+03:00</DateEnd>'."\n";
$tplBlock .= '<AllowEmail>'.$this->getParam('AllowEmail','Нет').'</AllowEmail>'."\n";
if ($this->getParam('ManagerName',false)) $tplBlock .= '<ManagerName>'.$this->getParam('ManagerName','').'</ManagerName>'."\n";
if ($this->getParam('ContactPhone',false)) $tplBlock .= '<ContactPhone>'.$this->getParam('ContactPhone','').'</ContactPhone>'."\n";
$tplBlock .= '<Address>' . $this->getParam('Address','') . '</Address>'."\n";
$tplBlock .= '<Condition>' . $this->getParam('Condition','') . '</Condition>'."\n";
$tplBlock .= '<Category>' . $item['avito_cat'] .'</Category>' . "\n";
$tplBlock .= '<GoodsType>' . $item['avito_goods_type'] . '</GoodsType>'."\n";

if (isset($item['name'])) $tplBlock .= '    <Title>'.$item['name'] .'</Title>'."\n";
if (isset($item['price'])) $tplBlock .= '    <price>'.Feedgen::formatPrice($item['price'],$item['price_type'],$item['currency']) .'</price>'."\n";
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
