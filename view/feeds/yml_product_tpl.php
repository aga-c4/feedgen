<?php
################################################################################
# Шаблон товара
################################################################################
$tplBlock = '<offer'; 
if (isset($item['offer_id'])) $tplBlock .=' id="'.$item['offer_id'] .'" '; 
$tplBlock .= ' available="'. ((!empty($item['offer_available']))?'true':'false') .'"';
if (isset($item['offer_bid'])) $tplBlock .= ' bid="'.$item['offer_bid'] .'"';
if (isset($item['offer_credit'])) $tplBlock .= ' credit="'.$item['offer_credit'] .'"';
$tplBlock .= ' type="vendor.model">' . "\n";
if (isset($item['instock_status_str'])) $tplBlock .= '    <instock_status>'.$item['instock_status_str'] .'</instock_status>'."\n";
if (isset($item['name'])) $tplBlock .= '    <name>'.$item['name'] .'</name>'."\n";
if (isset($item['shop_sku'])) $tplBlock .= '    <shop-sku>'.$item['shop_sku'] .'</shop-sku>'."\n";
if (isset($item['type_prefix'])) $tplBlock .= '    <typePrefix>'.$item['type_prefix'] .'</typePrefix>'."\n";
if (isset($item['vendor'])) $tplBlock .= '    <vendor>'.$item['vendor'] .'</vendor>'."\n";
if (isset($item['model'])) $tplBlock .= '    <model>'.$item['model'] .'</model>'."\n";
if (isset($item['vendor_code'])) $tplBlock .= '    <vendorCode>'.$item['vendor_code'] .'</vendorCode>'."\n";
if (isset($item['manufacturer'])) $tplBlock .= '    <manufacturer>'.$item['manufacturer'] .'</manufacturer>'."\n";
 
if (isset($item['url'])) $tplBlock .= '    <url>'.$item['url'] .'</url>'."\n";
if (isset($item['price'])) $tplBlock .= '    <price>'.Feedgen::formatPrice($item['price'],$item['price_type'],$item['currency']) .'</price>'."\n";
if (isset($item['oldprice'])) $tplBlock .= '    <oldprice>'.Feedgen::formatPrice($item['oldprice'],$item['price_type'],$item['currency']) .'</oldprice>'."\n";
if (isset($item['profit'])) $tplBlock .= '    <profit>'.Feedgen::formatPrice($item['profit'],$item['price_type'],$item['currency']) .'</profit>'."\n";
if (isset($item['profit_pr'])) $tplBlock .= '    <profit_pr>'.$item['profit_pr'] .'</profit_pr>'."\n";
if (isset($item['profit_lvl'])) $tplBlock .= '    <profit_lvl>'.$item['profit_lvl'] .'</profit_lvl>'."\n";
if (isset($item['instock_qty'])) $tplBlock .= '    <qty>'.$item['instock_qty'] .'</qty>'."\n";
if (isset($item['enable_auto_discounts'])) $tplBlock .= '    <enable_auto_discounts>'.$item['enable_auto_discounts'] .'false</enable_auto_discounts>'."\n";
if (isset($item['currency'])) $tplBlock .= '    <currencyId>'.$item['currency'].'</currencyId>'."\n";
if (isset($item['cat_id'])) $tplBlock .= '    <categoryId>'.$item['cat_id'] .'</categoryId>'."\n";

if (isset($item['picture']) && is_array($item['picture'])) {
    foreach ($item['picture'] as $inItem) $tplBlock .= '    <picture>'.$inItem['url'] .'</picture>'."\n";
}

if (isset($item['delivery'])) $tplBlock .= '    <delivery>'.((!empty($item['delivery']))?'true':'false') .'</delivery>'."\n";
if (isset($item['delivery_cat'])) $tplBlock .= '    <delivery-cat>'.$item['delivery_cat'] .'</delivery-cat>'."\n";
if (isset($item['pickup'])) $tplBlock .= '    <pickup>'.((!empty($item['pickup']))?'true':'false') .'</pickup>'."\n";
if (isset($item['store'])) $tplBlock .= '    <store>'.((!empty($item['store']))?'true':'false') .'</store>'."\n";
if (isset($item['sales_notes'])) $tplBlock .= '    <sales_notes>'.$item['sales_notes'] .'</sales_notes>'."\n";
if (isset($item['warranty'])) $tplBlock .= '    <manufacturer_warranty>'.$item['warranty'] .'</manufacturer_warranty>'."\n";
if (isset($item['country'])) $tplBlock .= '    <country_of_origin>'.$item['country'] .'</country_of_origin>'."\n";
if (isset($item['barcode'])) $tplBlock .= '    <barcode>'.$item['barcode'] .'</barcode>'."\n";  
if (isset($item['description'])) $tplBlock .= '    <description>' . $item['description'] . '</description>'."\n";

if (isset($item['outlets']) && is_array($item['outlets'])){ 
    $tplBlock .= '    <outlets>'."\n";
    foreach ($item['outlets'] as $inItem) $tplBlock .= '        <outlet id="'.$inItem['id'] .'" instock="'.$inItem['qty'] .'"/>'."\n";      
    $tplBlock .= '    </outlets>'."\n";
}
if (isset($item['delivery_options']) && is_array($item['delivery_options'])){ 
    $tplBlock .= '    <delivery-options>'."\n";
    foreach ($item['delivery_options'] as $inItem) $tplBlock .= '        <option cost="'.$inItem['cost'] .'" days="'.$inItem['days'] .'" order-before="'.$inItem['order_before'] .'"/>'."\n";      
    $tplBlock .= '    </delivery-options>'."\n";
}
if (isset($item['pickup_options']) && is_array($item['pickup_options'])){ 
    $tplBlock .= '    <pickup-options>'."\n";
    foreach ($item['pickup_options'] as $inItem) $tplBlock .= '        <option cost="'.$inItem['cost'] .'" days="'.$inItem['days'] .'" order-before="'.$inItem['order_before'] .'"/>'."\n";      
    $tplBlock .= '    </pickup-options>'."\n";
}
if (isset($item['params']) && is_array($item['params'])){ 
    foreach ($item['params'] as $inItem) {
        $tplBlock .= '    <param';
        //if (isset($inItem['id'])) $tplBlock .= ' id="'.$inItem['id'] .'"';
        if (isset($inItem['code'])) $tplBlock .= ' code="'.$inItem['code'] .'"';
        if (isset($inItem['name'])) $tplBlock .= ' name="'.$inItem['name'] .'"';
        //if (isset($inItem['val_id'])) $tplBlock .= ' val_id="'.$inItem['val_id'] .'"';
        $tplBlock .= '>';
        if (isset($inItem['value'])) $tplBlock .= $inItem['value'];
        $tplBlock .= '</param>'."\n";    
    }
}
if (isset($item['prod_custom'])) $tplBlock .= $item['prod_custom']."\n";

//Вывод размера и веса БРУТТО и НЕТТО
if (isset($item['gross_params']) && is_array($item['gross_params'])){ 
    foreach ($item['gross_params'] as $inItem) {
        $tplBlock .= '    <param';
        if (isset($inItem['id'])) $tplBlock .= ' id="'.$inItem['id'] .'"';
        if (isset($inItem['code'])) $tplBlock .= ' code="'.$inItem['code'] .'"';
        if (isset($inItem['name'])) $tplBlock .= ' name="'.$inItem['name'] .'"';
        $tplBlock .= '>'.$inItem['value'].'</param>'."\n";    
    }
}

if (isset($item['netto_params']) && is_array($item['netto_params'])){ 
    foreach ($item['netto_params'] as $inItem) {
        $tplBlock .= '    <param';
        if (isset($inItem['id'])) $tplBlock .= ' id="'.$inItem['id'] .'"';
        if (isset($inItem['code'])) $tplBlock .= ' code="'.$inItem['code'] .'"';
        if (isset($inItem['name'])) $tplBlock .= ' name="'.$inItem['name'] .'"';
        $tplBlock .= '>'.$inItem['value'].'</param>'."\n";    
    }
}

if (isset($item['gross_dimensions'])) $tplBlock .= '    <dimensions>' . $item['gross_dimensions'] . '</dimensions>'."\n";
if (isset($item['netto_dimensions'])) $tplBlock .= '    <netto-dimensions>' . $item['netto_dimensions'] . '</netto-dimensions>'."\n";
if (isset($item['gross_weight'])) $tplBlock .= '    <weight>' . $item['gross_weight'] . '</weight>'."\n";
if (isset($item['netto_weight'])) $tplBlock .= '    <netto-weight>' . $item['netto_weight'] . '</netto-weight>'."\n";
if (isset($item['gross_split'])) {
    $tplBlock .= '    <height>' . $item['gross_split']['height'] . '</height>'."\n";
    $tplBlock .= '    <width>' . $item['gross_split']['width'] . '</width>'."\n";
    $tplBlock .= '    <length>' . $item['gross_split']['length'] . '</length>'."\n";
}
if (isset($item['netto_split'])) {
    $tplBlock .= '    <netto_height>' . $item['netto_split']['height'] . '</netto_height>'."\n";
    $tplBlock .= '    <netto_width>' . $item['netto_split']['width'] . '</netto_width>'."\n";
    $tplBlock .= '    <netto_length>' . $item['netto_split']['length'] . '</netto_length>'."\n";
}
//Конец вывода размеров и веса

if (isset($item['marker'])) $tplBlock .= '    <marker>'.((!empty($item['marker']))?'true':'false') .'</marker>'."\n";
if (isset($item['date_create'])) $tplBlock .= '    <date_create ts="' . $item['ts_create'] . '">' . $item['date_create'] .'</date_create>'."\n";
if (isset($item['date_upd'])) $tplBlock .= '    <date_upd ts="' . $item['ts_upd'] . '">' . $item['date_upd'] .'</date_upd>'."\n";
if (isset($item['date_upd_price'])) $tplBlock .= '    <date_upd_price ts="' . $item['ts_upd_price'] . '">' . $item['date_upd_price'] .'</date_upd_price>'."\n";
if (isset($item['date_upd_photo'])) $tplBlock .= '    <date_upd_photo ts="' . $item['ts_upd_photo'] . '">' . $item['date_upd_photo'] .'</date_upd_photo>'."\n";

$tplBlock .= '</offer>
';  
