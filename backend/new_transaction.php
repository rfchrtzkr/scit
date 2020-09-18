<?php
    include_once("../backend/session.php");
    include_once("../backend/php_functions.php");
    // -- Below: simulation only
{/*
    $item = [];
    $transaction_from_pos = [];
    $transaction_static = array(
        'trans_date' => "2020-09-16 09:56:37",
        'clerk' => "JB Meneses"
        );
    {
        $item = [];
        $item['trans_date'] = "2020-09-16 09:56:37"; // from pos
        $item['clerk'] = "JB Meneses"; // from pos
        $item['vat_exempt_price'] = "100"; // from pos
        $item['discount_price'] = "20"; // from pos
        $item['payable_price'] = "80"; // from pos
        if($_SESSION['business_type'] == "food" || $_SESSION['business_type'] == "transportation") {
            $item['desc'] = "Purchase drescription";
        } elseif($_SESSION['business_type'] == "pharmacy") {
            // if exists, proceed transaction
            if(!array_key_exists("desc", $item)){ // WARNING: temporarily set to `TRUE`. $is_drug -
                $item['generic_name'] = arrange_generic_name("calcium carbonate,famotidine,magnesium hydroxide");
                $item['brand'] = "kremil-s advance"; // from pos
                $item['dose'] = (int)500; // from pos
                $item['unit'] = "mg"; // from pos
                $item['unit_price'] = (double)5.00; // from pos
                $item['quantity'] = "10"; // from db
                $generic_name = $item['generic_name'];
                $brand = $item['brand'];
                $dose = $item['dose'];
                $unit = $item['unit'];
                $drug = get_drug_details("", "$generic_name", "$brand", "$dose", "$unit");
                $item['is_otc'] = $drug['is_otc']; // from db
                $item['max_monthly'] = $drug['max_monthly']; // from db
                $item['max_weekly'] = $drug['max_weekly']; // from db
            } else {
                $item['desc'] = ""; // from pos
            }
        }
        $transaction_from_pos[] = $item;
        // * /
    }
*/
}
    // -- Below: simulation only
    {
        // Pharmacy
        $json_string = '[
            {
            "clerk": "AL Manalon",
            "generic_name": "cetirizine",
            "brand": "Brand 2",
            "dose": "10",
            "unit": "mg",
            "unit_price": "6.25",
            "quantity": "7",
            "vat_exempt_price": "39.06",
            "discount_price": "7.81",
            "payable_price": "31.25",
            "trans_date": "2020-09-17 18:40:11"
            },
            {
            "clerk": "AL Manalon",
            "generic_name": "Carbocisteine, Zinc",
            "brand": "Solmux",
            "dose": "500",
            "unit": "mg",
            "unit_price": "8.00",
            "quantity": "7",
            "vat_exempt_price": "50",
            "discount_price": "10",
            "payable_price": "40",
            "trans_date": "2020-09-17 18:40:11"
            },
            {
            "clerk": "AL Manalon",
            "generic_name": "paracetamol",
            "brand": "BIOGESIC",
            "dose": "500",
            "unit": "mg",
            "unit_price": "5.20",
            "quantity": "1",
            "vat_exempt_price": "65",
            "discount_price": "13",
            "payable_price": "52",
            "trans_date": "2020-09-17 18:40:11"
            },
            {
            "clerk": "AL Manalon",
            "generic_name": "Sodium Ascorbate, Zincx",
            "brand": "Immunpro",
            "dose": "500",
            "unit": "mg",
            "unit_price": "5.20",
            "quantity": "1",
            "vat_exempt_price": "65",
            "discount_price": "13",
            "payable_price": "52",
            "trans_date": "2020-09-17 18:40:11"
            }
        ]';
    }// hard coded yung last item para maging mali. "Sodium Ascorbate, Zincx", meaning unique yung drug

    // -- Below: ACTUAL WAY OF GETTING THE stringified transaction JSON FROM POS 
    // $json_string = $_POST['transaction_from_pos'];

    $transaction_from_pos = json_decode($json_string, true );
    $unregistered_drugs = verify_drugs($transaction_from_pos);
    $unregistered_drugs_json = [];

    $transaction = [];  // array storage for whole transaction
    foreach($transaction_from_pos as $row => $item_from_pos){
        $item = [];
        $item['trans_date'] = filter_var($item_from_pos['trans_date'], FILTER_SANITIZE_STRING);
        $item['clerk'] = filter_var($item_from_pos['clerk'], FILTER_SANITIZE_STRING);
        $item['vat_exempt_price'] = filter_var($item_from_pos['vat_exempt_price'], FILTER_VALIDATE_FLOAT);
        $item['discount_price'] = filter_var($item_from_pos['discount_price'], FILTER_VALIDATE_FLOAT);
        $item['payable_price'] = filter_var($item_from_pos['payable_price'], FILTER_VALIDATE_FLOAT);
        if($_SESSION['business_type'] == "food" || $_SESSION['business_type'] == "transportation") {
            $item['desc'] = filter_var($item_from_pos['desc'], FILTER_SANITIZE_STRING); // from pos
        } elseif($_SESSION['business_type'] == "pharmacy") {
            if(!array_key_exists("desc", $item_from_pos)){ // WARNING: temporarily set to `TRUE`. $is_drug -
                $generic_name = filter_var($item_from_pos['generic_name'], FILTER_SANITIZE_STRING);
                $item['generic_name'] = strtolower(arrange_generic_name($generic_name));
                $item['brand'] = strtolower(filter_var($item_from_pos['brand'], FILTER_SANITIZE_STRING)); // from pos
                $item['dose'] = filter_var($item_from_pos['dose'], FILTER_VALIDATE_INT); // from pos
                $item['unit'] = filter_var($item_from_pos['unit'], FILTER_SANITIZE_STRING); // from pos
                $item['unit_price'] = filter_var($item_from_pos['unit_price'], FILTER_VALIDATE_FLOAT); // from pos
                $item['quantity'] = filter_var($item_from_pos['quantity'], FILTER_VALIDATE_INT); // from pos
                $generic_name = $item['generic_name'];
                $brand = $item['brand'];
                $dose = $item['dose'];
                $unit = $item['unit'];
                $drug = get_drug_details("", "$generic_name", "$brand", "$dose", "$unit");
                $item['is_otc'] = $drug['is_otc']; // from pos
                $item['max_monthly'] = $drug['max_monthly']; // from pos
                $item['max_weekly'] = $drug['max_weekly']; // from pos
            } else {
                $item['desc'] = filter_var($item_from_pos['desc'], FILTER_SANITIZE_STRING); // from pos
            }
        }
        $transaction[] = $item;
    }

    if(count($unregistered_drugs) > 0){
        $unregistered_drugs_json = json_encode($unregistered_drugs);
    }
    
?>