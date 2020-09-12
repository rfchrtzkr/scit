<?php


    include('../backend/session.php');
    include('../backend/php_functions.php');
    if(isset($_POST['osca_id']))
    {
        include('../backend/conn_members.php');
        $selected_id = $_POST['osca_id'];
        $business_type = $_SESSION['business_type'];
        $company_tin = $_SESSION['company_tin'];

        if($business_type == "pharmacy"){
            $transaction_query = "SELECT `member_id`, `trans_date`, date(trans_date) `ddd`, `company_name` `company`, `branch`, `business_type`, `company_tin`,
                                    `generic_name`, `brand`, `dose`, `is_otc`, `max_monthly`, `max_weekly`, `unit`, `quantity`,
                                    `vat_exempt_price`, `discount_price`, `payable_price`
                                    FROM `view_pharma_transactions`
                                    WHERE `osca_id` = '$selected_id' AND date(trans_date) >= (LEFT(NOW() - INTERVAL 3 MONTH,10))
                                    ORDER BY `trans_date`;";
        }
        if($business_type == "food"){
            $transaction_query = "SELECT `member_id`, `trans_date`, date(trans_date) `ddd`, `company_name` `company`, `branch`, `business_type`, `company_tin`,
                                    `desc`,
                                    `vat_exempt_price`, `discount_price`, `payable_price`
                                    FROM `view_food_transactions` 
                                    WHERE `osca_id` = '$selected_id' AND `company_tin` = '$company_tin'AND date(trans_date) >= (LEFT(NOW() - INTERVAL 3 MONTH,10))
                                    ORDER BY `trans_date`;";
        }
        if($business_type == "transportation"){
            $transaction_query = "SELECT `member_id`, `trans_date`, date(trans_date) `ddd`, `company_name` `company`, `branch`, `business_type`, `company_tin`,
                                    `desc`,
                                    `vat_exempt_price`, `discount_price`, `payable_price`
                                    FROM `view_transportation_transactions`
                                    WHERE `osca_id` = '$selected_id' AND date(trans_date) >= (LEFT(NOW() - INTERVAL 3 MONTH,10))
                                    ORDER BY `trans_date`;";
        }
        $result = $mysqli_2->query($transaction_query);
        $row_count = mysqli_num_rows($result);
        ?>
            <div class="title">
                TRANSACTIONS HISTORY
            </div>
            <div class="transaction-history scrollbar-black">
        <?php
        
        if($row_count != 0)
        {
            $counter = 0;
            $result = $mysqli_2->query($transaction_query);
            while($row = mysqli_fetch_array($result))
            {
                $counter++;
                $transaction_date = $row['trans_date'];
                $ddd = $row['ddd'];
                
                $company = ucfirst($row['company']);
                $branch = ucfirst($row['branch']);
                $business_type = $row['business_type'];

                $formatter = new NumberFormatter("fil-PH", \NumberFormatter::CURRENCY);

                $vat_exempt_price = $formatter->format($row['vat_exempt_price']);
                $discount_price = $formatter->format($row['discount_price']);
                $payable_price = $formatter->format($row['payable_price']);
                
                
                if(validate_date_month($ddd, "-1")){
                    $recent = "non-recent";
                } else {

                    $recent = "recent";
                }
                
                if($business_type == "pharmacy"){
                    
                    $generic_name_collective = str_replace('  ', ' ', $row['generic_name']);
                    $generic_name_collective = str_replace(', ', ',', $generic_name_collective);
                    $generic_names = explode(',', $generic_name_collective);
                    $generic_name_string = "";
                    $brand = ucwords($row['brand']);
                    $dose = $row['dose'];
                    $unit = $row['unit'];
                    $is_otc = ($row['is_otc'] == '1') ? true: false;
                    $quantity = $row['quantity'];
                    $total_dosage = $dose * $quantity;

                    $max_monthly = $row['max_monthly'];
                    $max_weekly = $row['max_weekly'];
                    
                    $count_generic_names=count($generic_names);
                    sort($generic_names);

                    for( $i = 0 ; $i < $count_generic_names ; $i++ )
                    {
                        $generic_name = $generic_names[$i];
                        $generic_name_string .= ucwords($generic_name);
                        if ($i >= 0 && $count_generic_names > 1 && ($count_generic_names - 1) != $i)
                        {
                            $generic_name_string .= ", ";
                        }
                    }

                    
                    $max_basis = ($is_otc)? $_SESSION['max_basis_weekly'][$generic_name_string]: $_SESSION['max_basis_monthly'][$generic_name_string];

                    // Validate if this generic_name is maxed for the month
                    if($_SESSION['compound_dosage_recent'][$generic_name_string] >= $max_basis && $recent == "recent"){
                        $maxed = "flagged";
                    } else {
                        $maxed = "";
                    }

                    ?>
                    <div class="row _transaction-record collapse-header <?php echo "$recent $maxed";?>" data-toggle="collapse" data-target="#collapse_<?php echo $counter?>" aria-expanded="false" aria-controls="collapse_<?php echo $counter?>">
                        <div class="col col-12 d-none d-md-block">
                            <?php echo "$company - $branch" ?>
                        </div>
                        <div class="col col-12">
                            <?php echo $transaction_date ?>
                        </div>
                        <!-- <> -->
                        <div class="col col-12">
                            <?php echo "<b>(Dosage on this purchase: $total_dosage)</b>"; ?>
                            <?php echo "<br><b>(Accumulated: ".$_SESSION['compound_dosage_recent'][$generic_name_string].")</b>"; ?>
                            <?php echo "<br><b>(Max: ".$max_basis.")</b>"; ?>
                        </div>
                        <!-- </> -->
                        
                        <div class="col col-12">
                            <?php echo "[ $generic_name_string ] <br>"?>
                        </div>

                        <div class="col col-12">
                            <b><?php echo "$brand "."$dose"."$unit @ $quantity"."pcs"?></b>
                        </div>
                        <div id="collapse_<?php echo $counter?>" class="col collapse" aria-labelledby="heading<?php echo $counter?>">
                            <div class="row">
                                <div class="col col-6">
                                    VAT Exempt Price:
                                </div>
                                <div class="col col-6 _transaction-record-right">
                                    <?php echo $vat_exempt_price?>
                                </div>
                                <div class="col col-6">
                                    Discounted Price:
                                </div>
                                <div class="col col-6 _transaction-record-right">
                                    (<?php echo $discount_price ?>)
                                </div>
                            </div>
                        </div>
                        
                                
                        <div class="col col-12">
                            <div class="row">
                                <div class="col col-6">
                                    Amount to pay:
                                </div>
                                <div class="col col-6 _transaction-record-right">
                                    <?php echo $payable_price ?>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                    <?php 
                }
                if($business_type == "food" || $business_type == "transportation" ){
                    $desc = $row['desc'];
                    ?>
                    <div class="row _transaction-record collapse-header <?php echo "$recent";?>" data-toggle="collapse" data-target="#collapse_<?php echo $counter?>" aria-expanded="false" aria-controls="collapse_<?php echo $counter?>">
                        <div class="col col-12 d-none d-md-block">
                            <?php echo "$company - $branch" ?>
                        </div>
                        <div class="col col-12">
                            <?php echo $desc ?>
                        </div>
                        <div class="col col-6">
                            Date:
                        </div>
                        <div class="col col-6 _transaction-record-right">
                            <?php echo $transaction_date ?>
                        </div>
                        
                        <div id="collapse_<?php echo $counter?>" class="col collapse" aria-labelledby="heading<?php echo $counter?>">
                            <div class="row">
                                <div class="col col-6">
                                    VAT Exempt Price:
                                </div>
                                <div class="col col-6 _transaction-record-right">
                                    <?php echo $vat_exempt_price?>
                                </div>
                                <div class="col col-6">
                                    Discounted Price:
                                </div>
                                <div class="col col-6 _transaction-record-right">
                                    (<?php echo $discount_price ?>)
                                </div>
                            </div>
                        </div>
                        
                                
                        <div class="col col-12">
                            <div class="row">
                                <div class="col col-6">
                                    Amount to pay:
                                </div>
                                <div class="col col-6 _transaction-record-right">
                                    <?php echo $payable_price ?>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                    <?php
                }
            }
        } else {echo "<div class='col col-md-8 text-center mx-auto mt-5'>No $business_type discount(s) <br> recorded yet for this user</div>";}
        mysqli_close($mysqli_2);
        ?>
            </div>
        <div class="foot">
            <button type="button" class="btn btn-block btn-light btn-lg" id="return">Return</button>
        </div>
            
        <script>
            $(document).ready(function(){

                $("#return").click(function(){
                    $('#body').load("../frontend/home.php #home");
                });

            });
        </script>
        <?php 
    } else {
        echo "false";
    }

?>