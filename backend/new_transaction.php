<?php
    include_once("../backend/session.php");
    include_once("../backend/php_functions.php");
    include_once('../backend/terminal_scripts.php');
    
    $unregistered_drugs_json = [];
    $transaction = [];  // array storage for whole transaction
    $transaction['items'] = [];  // array storage for whole transaction
    $business_type = $_SESSION['business_type'];
    
    $transaction['clerk_override'] = "";
    $unregistered_drugs = [];

    {
        // Toggle comment $json_string for simulation of data for: PHARMACY
        
        $json_string = '{
            "items" : [{
                "clerk": "AL Manalon",
                "generic_name": "cetirizinexz",
                "brand": "Watsons",
                "dose": "10",
                "unit": "mg",
                "unit_price": "6.25",
                "quantity": "7000",
                "vat_exempt_price": "39.06",
                "discount_price": "7.81",
                "payable_price": "31.25",
                "trans_date": "2020-09-17 21:11:11",
                "override" : 1
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
                }],
            "drugs" : [{
                "generic_name" : "aaa"
                },
                {
                "generic_name" : "aaa"
                }]
            }';
        //$_SESSION['serial_received'] = $json_string;
    }

    // INSTRUCTIONS FOR POS ADJUSTMENT
    // put all items from DataTable to a json field name: "items", before writing to serial port
    // in the case of pharmacy -> frmNewDrugs , the frmNewDrug.json must be put in json field name: "drugs"
    //                          \-> this happens after frmNewDrugs.ShowDialog(json);

    // Fixed: 
    // + cardless
    // + override
    // + guardians list (on nfc/qr/cardless read)
    // + transaction history modal on Transactions page
    // [] dosage computation
    
    
    if(isset($_SESSION['serial_received'])){
        $clerk_override = false;
        $serial_received = $_SESSION['serial_received'];
        $serial_received = json_decode($serial_received, true );
        $transaction_from_pos = $serial_received['items'];
        $unregistered_drugs['drugs'] = verify_drugs($transaction_from_pos);
        foreach($transaction_from_pos as $row => $item_from_pos){
            $item = [];
            $trans_date = filter_var($item_from_pos['trans_date'], FILTER_SANITIZE_STRING);
            $clerk = filter_var($item_from_pos['clerk'], FILTER_SANITIZE_STRING);
            if(isset($item_from_pos['override'])){
                $clerk_override = filter_var($item_from_pos['override'], FILTER_VALIDATE_INT);
                $clerk_override = ($clerk_override == 1 || $clerk_override == "true") ? true : false;
            }
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
        
        $transaction['clerk_override'] = $clerk_override;
        $transaction['trans_date'] = $trans_date;
        $transaction['clerk'] = $clerk;
        if(count($unregistered_drugs['drugs']) > 0){
            $_SESSION['invalid_drug'] = true;
            $unregistered_drugs['msg'] = "invalid_drug";
            $unregistered_drugs_json = json_encode($unregistered_drugs);
            write_invalid_drug($unregistered_drugs_json);
        } else {
            $_SESSION['invalid_drug'] = false;
        }
    }
?>
