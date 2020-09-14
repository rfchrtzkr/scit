<?php
    // validation if item is drug, 
    // then proceed



    // steps left:
    // 1. validation if company exists
    // 2. accept transaction info from POS: In JSON format 
    // Items in $item[<#key>] here is  simulation only  as well as $item_from_pos
    // $item
    include_once("../backend/session.php");

    
    $transaction = [];
    $item = [];

    $item_from_pos = 0; // sample count of items from pos. each item will be added
    for($i = 0; $i < $item_from_pos; $i++){
        $item['trans_date'] = "2020-09-10 21:12:45"; // from pos
        $item['clerk'] = "AB Garcia"; // from pos
        $item['vat_exempt_price'] = "100"; // from pos
        $item['discount_price'] = "20"; // from pos
        $item['payable_price'] = "80"; // from pos
    
        if($_SESSION['business_type'] == "food" || $_SESSION['business_type'] == "transportation") {
            $item['desc'] = "Purchase drescription";
        } elseif($_SESSION['business_type'] == "pharmacy") {
            // validation of 
            //  generic name && brand && dose && unit exists in `drug` table
            // if exists, proceed transaction
            if($is_drug = true && $drug_exists = true){ // WARNING: temporarily set to `TRUE`. $is_drug - 
                $item['generic_name'] = "paracetamol";
                $item['brand'] = "Biogesic"; // from pos
                $item['dose'] = "500"; // from pos
                $item['unit'] = "mg"; // from pos
                $item['is_otc'] = "1"; // from db
                $item['quantity'] = "10"; // from db
                $item['max_monthly'] = "30000"; // from db
                $item['max_weekly'] = "7000"; // from db
            }
            // elseif not exists, add drug to database
            // remember to keep log 
        }

        // store the `item`[] to `transaction`[]
        $transaction[] = $item;
    }

    // just duplicate for simulation
    // import item from pos in form of array/json
    $item_from_pos = 80; // sample count of items from pos. each item will be added
    for($i = 0; $i < $item_from_pos; $i++){
        $item['trans_date'] = "2020-09-10 21:12:45"; // from pos
        $item['clerk'] = "AB Garcia"; // from pos
        $item['vat_exempt_price'] = "100"; // from pos
        $item['discount_price'] = "20"; // from pos
        $item['payable_price'] = "80"; // from pos
    
        if($_SESSION['business_type'] == "food" || $_SESSION['business_type'] == "transportation") {
            $item['desc'] = "Purchase drescription";
        } elseif($_SESSION['business_type'] == "pharmacy") {
            // validation of 
            //  generic name && brand && dose && unit exists in `drug` table
            // if exists, proceed transaction
            if($drug_exists = true){ // WARNING: temporarily set to `TRUE`
                $item['generic_name'] = "ibuprofen,paracetamol";
                $item['brand'] = "Alaxan"; // from pos
                $item['dose'] = "100"; // from pos
                $item['unit'] = "mg"; // from pos
                $item['is_otc'] = "1"; // from db
                $item['quantity'] = "7"; // from db
                $item['max_monthly'] = "30000"; // from db
                $item['max_weekly'] = "7000"; // from db
            }
            // elseif not exists, add drug to database
            // remember to keep log of who added, and when
        }

        // store the array: item to array: transaction
        $transaction[] = $item;
    }
?>