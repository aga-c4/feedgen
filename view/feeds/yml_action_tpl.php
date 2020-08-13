<?php
################################################################################
# Шаблон вывода блоков Яндекс.Акций
################################################################################
$tplBlock = '
  <promo id="'.$item["id"].'" type="'.$item["type"].'">
    <start-date>'.$item["start"].'</start-date>
    <end-date>'.$item["end"].'</end-date>
    <description>'.$item["description"].'</description>
    '.((!empty($item["url"]))?('<url>'.$item["url"].'</url>'):'')."\n";
    
    if (isset($item['promo_code'])) $tplBlock .= '    <promo-code>'.$item['promo_code'] .'</promo-code>'."\n";
    if (isset($item["discount"])){
        if ($item["discount"]["unit"]==='currency'){
            $tplBlock .= '    <discount unit="currency" currency="'.$item["discount"]["currency"].'">'.$item["discount"]["val"].'</discount>'."\n";
        }elseif($item["discount"]["unit"]=='percent'){
            $tplBlock .= '    <discount unit="percent">'.$item["discount"]["val"].'</discount>'."\n";
        }
    }

    if (isset($item["products"]) && is_array($item["products"])) {
        $tplBlock .= '    <purchase>'."\n";
        if (!empty($item['required_quantity'])) $tplBlock .= '      <required-quantity>'.$item['required_quantity'].'</required-quantity>'."\n";
        if (!empty($item['free_quantity'])) $tplBlock .= '      <free-quantity>'.$item['free_quantity'].'</free-quantity>'."\n";
        foreach($item["products"] as $prId=>$prPrice){
            if (empty($prPrice)) continue; 
            
            if ($item["type"] === "flash discount") {
                $tplBlock .= '        <product offer-id="'.$prId.'">
          <discount-price currency="RUR">'.$prPrice.'</discount-price>
        </product>'."\n";
            }else{
                $tplBlock .= '        <product offer-id="'.$prId.'"/>'."\n";
            }
        }
        
        if (isset($item["categories"]) && is_array($item["categories"])) {
            foreach($item["categories"] as $catId) $tplBlock .= '        <product category-id="'.$catId.'"/>'."\n";
        }
        
        $tplBlock .= '    </purchase>'."\n";
    }
    
    if (isset($item["gifts"]) && is_array($item["gifts"])) {
        $tplBlock .= '    <promo-gifts>'."\n";
        foreach($item["gifts"] as $prId=>$prPrice){
            if (empty($prPrice)) continue;
            $tplBlock .= '        <promo-gift offer-id="'.$prId.'"/>'."\n";
        }
        $tplBlock .= '    </promo-gifts>'."\n";
    }
    
    
    
    $tplBlock .= '    </promo>'."\n";
