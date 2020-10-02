<?php
    include('../backend/session.php');
    include('../backend/conn_osca.php');
    include('../backend/php_functions.php');
    include('../backend/terminal_scripts.php');
    
    $selected_id = $_SESSION['osca_id'];
    $business_type = $_SESSION['business_type'];
    $company_tin = $_SESSION['company_tin'];
   
    if(isset($_POST['accepted']) && $_POST['accepted'])
    {
        $transaction = json_decode($_POST['transaction'], true);
        $formatter = new NumberFormatter("fil-PH", \NumberFormatter::CURRENCY);

        $query_trans = "CALL `add_transaction`('".$transaction['trans_date']."', '$company_tin', '$selected_id', '".$transaction['clerk']."', @`msg`);";
        $mysqli_2->query($query_trans);
        $result = $mysqli_2->query("SELECT @`msg` as `trans_id`");
        $row = mysqli_fetch_assoc($result);
        $trans_id = $row['trans_id'];
        
        foreach($transaction['items'] as $row => $item){
            ?>
            <script>
            console.log("<?php echo $business_type; ?> Transaction:")
            console.log(<?php echo json_encode($item); ?>);
            </script>
            
            <?php
            $vat_exempt_price = $item['vat_exempt_price'];
            $discount_price = $item['discount_price'];
            $payable_price = $item['payable_price'];
            
            if( $business_type == "pharmacy" &&
            array_key_exists("generic_name",$item) &&
            array_key_exists("brand",$item) &&
            array_key_exists("dose",$item) &&
            array_key_exists("unit",$item) &&
            array_key_exists("generic_name",$item)){ // means item is drug
                $generic_name = $item['generic_name'];
                $brand = $item['brand'];
                $dose = $item['dose'];
                $unit = $item['unit'];
                $unit_price = $item['unit_price'];
                $is_otc = $item['is_otc'];
                $quantity = $item['quantity'];
                $max_monthly = $item['max_monthly'];
                $max_weekly = $item['max_weekly'];
                $is_otc = $item['is_otc'];
                
                $drug_id = validate_drug($generic_name, $brand, $dose, $unit);
                //$string1 = filter_var($value, FILTER_VALIDATE_BOOLEAN);
                if($drug_id != 0) {
                    //exact drug match exists
                } else {
                    
                    //ultimate goal is to set a value to the drug_id
                    $drug_id = "";
                }

                $query = "CALL `add_transaction_pharmacy_drug`('$business_type', '$trans_id','$company_tin', '$drug_id', '$quantity', '$unit_price', '$vat_exempt_price', '$discount_price', '$payable_price', @`msg`)";
            } elseif( $business_type == "pharmacy" && array_key_exists("desc",$item)){
                $desc = $item['desc'];
                $query = "CALL `add_transaction_pharmacy_nondrug`('$business_type', '$trans_id','$company_tin', '$desc', '$vat_exempt_price', '$discount_price', '$payable_price', @`msg`)";
            } elseif( $business_type == "food" && array_key_exists("desc",$item)){ // means item is not drug
                $desc = $item['desc'];
                $query = "CALL `add_transaction_food`('$business_type', '$trans_id','$company_tin', '$desc', '$vat_exempt_price', '$discount_price', '$payable_price', @`msg`)";
            } elseif( $business_type == "transportation" && array_key_exists("desc",$item)){ // means item is not drug
                $desc = $item['desc'];
                $query = "CALL `add_transaction_transportation`('$business_type', '$trans_id','$company_tin', '$desc', '$vat_exempt_price', '$discount_price', '$payable_price', @`msg`)";
            }

            if($mysqli_2->query($query)){
                $query2 = "SELECT @`msg` msg";
                $result2 = $mysqli_2->query($query2);
                $row2 = mysqli_fetch_assoc($result2);
                $msg = $row2['msg'];

                if($msg == "0") {
                    echo "Invalid company and business type $query";
                } else if ($msg == "1") {
                    echo "true";
                    serial_w_success();
                }
            }
            else {
                echo "ERROR: Unable to execute. \r\n $query" . mysqli_error($mysqli_2);
            }
        }
        
    }
?>
