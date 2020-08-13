<?php
################################################################################
# Шаблон хедера фида
################################################################################
$tplBlock = '<yml_catalog date="'.date("Y-m-d H:i",$item['feed_ts']).'">
<shop>
<name>'.$item['feed_name'].'</name>
<company>'.$item['feed_company_name'].'</company>
<url>'.$item['feed_base_url'].'</url>
<currencies>
  <currency id="'.$item['feed_currency'].'" rate="1"/>
</currencies>
';

if (isset($item['delivery_options']) && is_array($item['delivery_options'])){ 
    $tplBlock .= '<delivery-options>'."\n";
    $inItem = $item['delivery_options']; $tplBlock .= '    <option cost="'.$inItem['cost'] .'" days="'.$inItem['days'] .'" order-before="'.$inItem['order_before'] .'"/>'."\n";      
    $tplBlock .= '</delivery-options>'."\n";
}

if (isset($item['pickup_options']) && is_array($item['pickup_options'])){ 
    $tplBlock .= '<pickup-options>'."\n";
    $inItem = $item['pickup_options']; $tplBlock .= '    <option cost="'.$inItem['cost'] .'" days="'.$inItem['days'] .'" order-before="'.$inItem['order_before'] .'"/>'."\n";      
    $tplBlock .= '</pickup-options>'."\n";
}


