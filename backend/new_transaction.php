<?php
    // steps left:
    // 2. accept transaction info from POS: In JSON format 
    // Items in $item[<#key>] here is  simulation only  as well as $item_from_pos
    include_once("../backend/session.php");
    include_once("../backend/php_functions.php");
{///*// simulation only
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
        // */
    }
    {
        $item = [];
        $item['trans_date'] = "2020-09-16 09:56:37"; // from pos
        $item['clerk'] = "JB Meneses"; // from pos
        $item['vat_exempt_price'] = "55.36"; // from pos
        $item['discount_price'] = "11.07"; // from pos
        $item['payable_price'] = "44.29"; // from pos
        if($_SESSION['business_type'] == "food" || $_SESSION['business_type'] == "transportation") {
            $item['desc'] = $item['desc'];
        } elseif($_SESSION['business_type'] == "pharmacy") {
            // if exists, proceed transaction
            if(!array_key_exists("desc", $item)){ // WARNING: temporarily set to `TRUE`. $is_drug -
                $item['generic_name'] = arrange_generic_name("cetirizine");
                $item['brand'] = "watsons"; // from pos
                $item['dose'] = (int)10; // from pos
                $item['unit'] = "mg"; // from pos
                $item['unit_price'] = (double)6.20; // from pos
                $item['quantity'] = (int)10; // from pos
                $generic_name = $item['generic_name'];
                $brand = $item['brand'];
                $dose = $item['dose'];
                $unit = $item['unit'];
                $drug = get_drug_details("", "$generic_name", "$brand", "$dose", "$unit");
                $item['is_otc'] = $drug['is_otc']; // from db
                $item['max_monthly'] = $drug['max_monthly']; // from db
                $item['max_weekly'] = $drug['max_weekly']; // from db
            } else {
                $item['desc'] = $item['desc'];
            }
        }
        $transaction_from_pos[] = $item;
        // */
    }
    {
        $item = [];
        $item['trans_date'] = "2020-09-16 09:56:37"; // from pos
        $item['clerk'] = "JB Meneses"; // from pos
        $item['vat_exempt_price'] = "100"; // from pos
        $item['discount_price'] = "20"; // from pos
        $item['payable_price'] = "80"; // from pos
        $item['desc'] = "Quai-ker Oathmill";
    
        if($_SESSION['business_type'] == "food" || $_SESSION['business_type'] == "transportation") {
            $item['desc'] = $item['desc'];
        } elseif($_SESSION['business_type'] == "pharmacy") {
            if(!array_key_exists("desc", $item)){
                // drug import here if desc is empty
            } else {
                $item['desc'] = $item['desc'];
            }
        }
        $transaction_from_pos[] = $item;
        // */
    }
    {
        $item = [];
        $item['trans_date'] = "2020-09-16 09:56:37"; // from pos
        $item['clerk'] = "JB Meneses"; // from pos
        $item['vat_exempt_price'] = "97.32"; // from pos
        $item['discount_price'] = "19.46"; // from pos
        $item['payable_price'] = "77.86"; // from pos
        $item['desc'] = "Pudgey Vars 12s";
        if($_SESSION['business_type'] == "food" || $_SESSION['business_type'] == "transportation") {
            $item['desc'] = $item['desc'];
        } elseif($_SESSION['business_type'] == "pharmacy") {
            if(!array_key_exists("desc", $item)){
                // drug import here if desc is empty
            } else {
                $item['desc'] = $item['desc'];
            }
        }
        $transaction_from_pos[] = $item;
        // */
    }
//*/
}
{// -- ACTUAL WAY OF GETTING THE transaction JSON FROM POS 
    //$transaction_from_pos = json_decode($_POST['transaction_from_pos'], true);
    $unregistered_drugs = verify_drugs($transaction_from_pos);
    $unregistered_drugs_json = [];
    
    if(count($unregistered_drugs) > 0){
        $unregistered_drugs_json = json_encode($unregistered_drugs);
        ?>
        <script>
        console.log("unregistered_drugs");
        console.log(<?php echo $unregistered_drugs_json;?>);
        </script>
        <?php
    }
    
    $transaction = [];  // array storage for whole transaction
    foreach($transaction_from_pos as $row => $item_from_pos){
        $item = [];
        $item['trans_date'] = $item_from_pos['trans_date'];
        $item['clerk'] = $item_from_pos['clerk'];
        $item['vat_exempt_price'] = $item_from_pos['vat_exempt_price'];
        $item['discount_price'] = $item_from_pos['discount_price'];
        $item['payable_price'] = $item_from_pos['payable_price'];
        if($_SESSION['business_type'] == "food" || $_SESSION['business_type'] == "transportation") {
            $item['desc'] = $item_from_pos['desc'];
        } elseif($_SESSION['business_type'] == "pharmacy") {
            if(!array_key_exists("desc", $item_from_pos)){ // WARNING: temporarily set to `TRUE`. $is_drug -
                $item['generic_name'] = arrange_generic_name($item_from_pos['generic_name']);
                $item['brand'] = $item_from_pos['brand']; // from pos
                $item['dose'] = (int)$item_from_pos['dose']; // from pos
                $item['unit'] = $item_from_pos['unit']; // from pos
                $item['unit_price'] = (double)$item_from_pos['unit_price']; // from pos
                $item['quantity'] = (double)$item_from_pos['quantity']; // from pos
                $generic_name = $item['generic_name'];
                $brand = $item['brand'];
                $dose = $item['dose'];
                $unit = $item['unit'];
                $drug = get_drug_details("", "$generic_name", "$brand", "$dose", "$unit");
                $item['is_otc'] = $drug['is_otc']; // from pos
                $item['max_monthly'] = $drug['max_monthly']; // from pos
                $item['max_weekly'] = $drug['max_weekly']; // from pos
            } else {
                $item['desc'] = $item_from_pos['desc']; // from pos
            }
        }
        $transaction[] = $item;
    }
}
?>
