<?php
    /*  This file is for storing the user's previous transactions 
     *  in the arrays: 
    *   compound_dosage_recent, max_basis_weekly, and max_basis_monthly
     */
    include('../backend/conn.php');
    $mysqli_pharma_trans = new mysqli($host,$user,$pass,$schema) or die($mysqli_pharma_trans->error);

    $selected_id = $_SESSION['osca_id'];

    $compound_dosage_recent = array();
    $max_basis_weekly = array();
    $max_basis_monthly = array();

    $qry = "SELECT `member_id`, `trans_date`, date(trans_date) `ddd`, `company_name` `company`, `branch`, `business_type`, `company_tin`,
        `generic_name`, `brand`, `dose`, `is_otc`, `max_monthly`, `max_weekly`, `unit`, `quantity`,
        `vat_exempt_price`, `discount_price`, `payable_price`
        FROM `view_pharma_transactions`
        WHERE `osca_id` = '$selected_id' AND date(trans_date) >= (LEFT(NOW() - INTERVAL 3 MONTH,10))
        ORDER BY `trans_date`;";
        
    $result_pharma_trans = $mysqli_pharma_trans->query($qry);
    $row_pharma_trans_count = mysqli_num_rows($result_pharma_trans);
    
    if($row_pharma_trans_count != 0){
        while($row_pharma_trans = mysqli_fetch_array($result_pharma_trans)) { // compute total transacted past month 
            if($business_type == "pharmacy"){
                $dose = (int)$row_pharma_trans['dose'];
                $quantity = (int)$row_pharma_trans['quantity'];
                $is_otc = ($row_pharma_trans['is_otc'] == '1') ? true: false;
                $total_dosage = $dose * $quantity;
                $max_monthly = (int)$row_pharma_trans['max_monthly'];
                $max_weekly = (int)$row_pharma_trans['max_weekly'];
                $ddd = $row_pharma_trans['ddd'];

                if(validate_date_month($ddd, "-1")) {
                    $recent = "non-recent";
                } else {

                    $recent = "recent";
                }
                
                $generic_name_string = arrange_generic_name($row_pharma_trans['generic_name']);
                

                if($recent == "recent") {
                    if (array_key_exists($generic_name_string, $compound_dosage_recent)){
                        $compound_dosage_recent[$generic_name_string] += $total_dosage;
                    } else {
                        $compound_dosage_recent[$generic_name_string] = $total_dosage;
                    }
                } else {
                    $compound_dosage_recent[$generic_name_string] = 0;
                }
                
                if(isset($max_basis_weekly[$generic_name_string])) {
                    if($max_basis_weekly[$generic_name_string] > $max_weekly){
                        $max_basis_weekly[$generic_name_string] = $max_weekly;
                    }
                } else {
                    $max_basis_weekly[$generic_name_string] = $max_weekly;
                }

                if(isset($max_basis_monthly[$generic_name_string])) {
                    if($max_basis_monthly[$generic_name_string] > $max_monthly){
                        $max_basis_monthly[$generic_name_string] = $max_monthly;
                    }
                } else {
                    $max_basis_monthly[$generic_name_string] = $max_monthly;
                }
            }
        }
    }
    
    $_SESSION['compound_dosage_recent'] = $compound_dosage_recent;
    $_SESSION['max_basis_weekly'] = $max_basis_weekly;
    $_SESSION['max_basis_monthly'] = $max_basis_monthly;
    mysqli_close($mysqli_pharma_trans);