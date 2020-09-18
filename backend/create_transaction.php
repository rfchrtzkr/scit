<?php
    include('../backend/session.php');
    include('../backend/conn_osca.php');
    include('../backend/php_functions.php');
    
    $selected_id = $_SESSION['osca_id'];
    $business_type = $_SESSION['business_type'];
    $company_tin = $_SESSION['company_tin'];
   
    if(isset($_POST['accepted']) && $_POST['accepted'])
    {
        $transaction = json_decode($_POST['transaction'], true);
        echo "direct post:<br>". $_POST['transaction'] . "<br><hr><br>json_decode:<br>";
        var_dump($transaction);
        $formatter = new NumberFormatter("fil-PH", \NumberFormatter::CURRENCY);
        
        foreach($transaction as $row => $item){
            ?>
            <script>
            console.log("<?php echo $business_type; ?> Transaction:")
            console.log(<?php echo json_encode($item); ?>);
            </script>
            
            <?php
            $transaction_date = $item['trans_date'];
            $clerk = $item['clerk'];
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

                $query = "CALL `add_transaction_pharmacy_drug`('$business_type', '$company_tin', '$transaction_date', '$selected_id', '$clerk', '$drug_id', '$quantity', '$unit_price', '$vat_exempt_price', '$discount_price', '$payable_price', @`msg`)";
            } elseif( $business_type == "pharmacy" && array_key_exists("desc",$item)){
                $desc = $item['desc'];
                $query = "CALL `add_transaction_pharmacy_nondrug`('$business_type', '$company_tin', '$transaction_date', '$selected_id', '$clerk', '$desc', '$vat_exempt_price', '$discount_price', '$payable_price', @`msg`)";
            } elseif( $business_type == "food" && array_key_exists("desc",$item)){ // means item is not drug
                $desc = $item['desc'];
                $query = "CALL `add_transaction_food`('$business_type', '$company_tin', '$transaction_date', '$selected_id', '$clerk', '$desc', '$vat_exempt_price', '$discount_price', '$payable_price', @`msg`)";
            } elseif( $business_type == "transportation" && array_key_exists("desc",$item)){ // means item is not drug
                $desc = $item['desc'];
                $query = "CALL `add_transaction_transportation`('$business_type', '$company_tin', '$transaction_date', '$selected_id', '$clerk', '$desc', '$vat_exempt_price', '$discount_price', '$payable_price', @`msg`)";
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
                }
            }
            else {
                echo "ERROR: Unable to execute. \r\n $query" . mysqli_error($mysqli_2);
            }
        }
        
    }
?>
