<?php
    include_once("../backend/session.php");
    include_once("../backend/php_functions.php");
    include_once('../backend/terminal_scripts.php');
    
    $unregistered_drugs_json = [];
    $transaction = [];  // array storage for whole transaction
    $transaction['items'] = [];  // array storage for whole transaction
    $business_type = $_SESSION['business_type'];
    $unregistered_drugs = [];

    /*
    $_SESSION['transaction_from_pos'] = serial_read();
    ?>
    <script>
        alert("Serial has been read: <?php var_dump($_SESSION['transaction_from_pos']);?>" );
    </script>
    <?php
    */
    
    
    if(isset($_SESSION['transaction_from_pos'])){
<<<<<<< HEAD
        ?>
        <script>
            alert("Entered new_transaction.php" );
        </script>
        <?php
        $transaction_from_pos_string = $_SESSION['transaction_from_pos'];
        $transaction_from_pos = json_decode($transaction_from_pos_string, true );
        echo '$transaction_from_pos: <br>';
        var_dump($transaction_from_pos);
        echo "<br><hr>";
=======
        $transaction_from_pos_string = $_SESSION['transaction_from_pos'];
        $transaction_from_pos = json_decode($transaction_from_pos_string, true );
>>>>>>> a9f5761bc5f1e8c543307371c7ece297b3e06aca
        $unregistered_drugs['drugs'] = verify_drugs($transaction_from_pos);
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

        if(count($unregistered_drugs['drugs']) > 0){
            $unregistered_drugs['msg'] = "invalid_drug";
            $unregistered_drugs_json = json_encode($unregistered_drugs);
            write_invalid_drug($unregistered_drugs_json);
        }
    }
<<<<<<< HEAD
?>
=======
?>
>>>>>>> a9f5761bc5f1e8c543307371c7ece297b3e06aca
