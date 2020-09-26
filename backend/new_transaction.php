<?php
    include_once("../backend/session.php");
    include_once("../backend/php_functions.php");

    // -- Below: simulation only
    {
        // Toggle comment $json_string for simulation of data for: PHARMACY
        /*
        $json_string = '[
            {
            "clerk": "AL Manalon",
            "generic_name": "cetirizine",
            "brand": "Watsons",
            "dose": "10",
            "unit": "mg",
            "unit_price": "6.25",
            "quantity": "7",
            "vat_exempt_price": "39.06",
            "discount_price": "7.81",
            "payable_price": "31.25",
            "trans_date": "2020-09-17 21:11:11"
            },
            {
            "clerk": "AL Manalon",
            "generic_name": "Carbocisteine, Zinc",
            "brand": "Solmux",
            "dose": "500",
            "unit": "mg",
            "unit_price": "8.00",
            "quantity": "10",
            "vat_exempt_price": "50",
            "discount_price": "10",
            "payable_price": "40",
            "trans_date": "2020-09-17 21:11:11"
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
            "trans_date": "2020-09-17 21:11:11"
            },
            {
            "clerk": "AL Manalon",
            "generic_name": " sodium ascorbate, Zincx",
            "brand": "immunpro",
            "dose": "500",
            "unit": "mg",
            "unit_price": "5.20",
            "quantity": "1",
            "vat_exempt_price": "65",
            "discount_price": "13",
            "payable_price": "52",
            "trans_date": "2020-09-17 21:11:11"
            }
        ]';
        */
        
        // Toggle comment $json_string for simulation of data for: FOOD
        /*
        $json_string = '[
            {
            "clerk": "Baxia Master",
            "desc": "Frozen Siomai Pack 25s Pack",
            "vat_exempt_price": "249.75",
            "discount_price": "44.60",
            "payable_price": "205.15",
            "trans_date": "2020-09-17 11:12:48"
            }
        ]';
        */
        
        // Toggle comment $json_string for simulation of data for: TRANSPO
        
        $json_string = '[
            {
            "clerk": "AB Ignacio",
            "desc": "Pasay to Guadalupe | Senior - SJT",
            "vat_exempt_price": "22.32",
            "discount_price": "4.46",
            "payable_price": "17.86",
            "trans_date": "2020-09-20 11:11:11"
            }
        ]';
        
        
    }

    // -- Below: ACTUAL WAY OF GETTING THE stringified transaction JSON FROM POS 
    // $json_string = $_POST['transaction_from_pos'];

    // -- ACTUAL PROCESS

    $business_type = $_SESSION['business_type'];

    // read from serial
    // Uncomment below if for use in raspi

    //$json_string = read_from_serial($business_type);

    $transaction_from_pos = json_decode($json_string, true );
    $unregistered_drugs = verify_drugs($transaction_from_pos);
    $unregistered_drugs_json = [];

    $transaction = [];  // array storage for whole transaction
    $transaction['items'] = [];  // array storage for whole transaction
    foreach($transaction_from_pos as $row => $item_from_pos){
        $item = [];
        $trans_date = filter_var($item_from_pos['trans_date'], FILTER_SANITIZE_STRING);
        $clerk = filter_var($item_from_pos['clerk'], FILTER_SANITIZE_STRING);
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
        array_push($transaction['items'], $item);
    }

    $transaction['trans_date'] = $trans_date;
    $transaction['clerk'] = $clerk;

    if(count($unregistered_drugs) > 0){
        $unregistered_drugs_json = json_encode($unregistered_drugs);
    }
    
?>