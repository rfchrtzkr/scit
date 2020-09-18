<?php
    // unregistered_drugs are from POS    


    if(count($unregistered_drugs) > 0){
        $unregistered_drugs = json_encode($unregistered_drugs, true);
        echo $unregistered_drugs;
    }
    
    if(!create_drug($generic_name, $brand, $dose, $unit, $is_otc, $max_monthly, $max_weekly)){
    } else {

    }



    $unregistered_drugs = json_decode($_POST['unregistered_drugs'], true);
    $drug_index = 0;

    foreach ($unregistered_drugs as $row => $drug) {
        $generic_name = $drug['generic_name'];
        $brand = $drug['brand'];
        $dose = $drug['dose'];
        $unit = $drug['unit'];
        $is_otc = $drug['is_otc'];
        $max_monthly = $drug['max_monthly'];
        $max_weekly = $drug['max_weekly'];

        $drug_index++;
    }