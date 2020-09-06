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
                                    `generic_name`, `brand`, `dose`, `max_monthly`, `max_weekly`, `unit`, `quantity`,
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
            <div class="transaction transaction-history scrollbar-black">
        <?php
        
        if($row_count != 0)
        {
            $counter = 0;
            if(!isset($ingredient_dosage)) {$ingredient_dosage = array();}
            if(!isset($compound_dosage)) {$compound_dosage = array();}
            while($row = mysqli_fetch_array($result))
            {
                $counter++;
                $transaction_date = $row['trans_date'];
                $ddd = $row['ddd'];
                $company = ucfirst($row['company']);
                $branch = ucfirst($row['branch']);

                $vat_exempt_price = $row['vat_exempt_price'];
                $discount_price = $row['discount_price'];
                $payable_price = $row['payable_price'];

                
                $formatter = new NumberFormatter("fil-PH", \NumberFormatter::CURRENCY);
                $vat_exempt_price = $formatter->format($vat_exempt_price);
                $discount_price = $formatter->format($discount_price);
                $payable_price = $formatter->format($payable_price);
                
                $business_type = $row['business_type'];
                
                if($business_type == "pharmacy"){
                    $generic_name_collective = $row['generic_name'];
                    $generic_names = explode(',', $generic_name_collective);
                    $generic_name_string = "";
                    $ingredient_dosage_string = "";
                    $compound_dosage_string = "";
                    $brand = ucfirst($row['brand']);
                    $dose = $row['dose'];
                    $unit = $row['unit'];
                    $quantity = $row['quantity'];
                    $total_dosage = $dose * $quantity;

                    $max_monthly = $row['max_monthly'];
                    $max_weekly = $row['max_weekly'];
                    
                    $count_generic_names=count($generic_names);
                    
                    if (array_key_exists($generic_name_collective, $compound_dosage))
                    {
                        $compound_dosage[$generic_name_collective] += $total_dosage;
                    }
                    else
                    {
                        $compound_dosage[$generic_name_collective] = $total_dosage;
                    }
                    $idd = $compound_dosage[$generic_name_collective];

                    $generic_name_collective_string = "$generic_name_collective (Dosage: $idd)";

                    for( $i = 0 ; $i < $count_generic_names ; $i++ )
                    {
                        $generic_name = $generic_names[$i];

                        if (array_key_exists($generic_name,$ingredient_dosage))
                        {
                            $ingredient_dosage[$generic_name] += $total_dosage;
                        }
                        else
                        {
                            $ingredient_dosage[$generic_name] = $total_dosage;
                        }

                        
                        $id1 = $ingredient_dosage[$generic_name];

                        $generic_name_string .= ucfirst($generic_name) . "($id1)";
                        if ($i >= 0 && $count_generic_names > 1 && ($count_generic_names - 1) != $i)
                        {
                            $generic_name_string .= ", ";
                        }

                    }

                }
                if($business_type == "food"){
                    $desc = $row['desc'];
                }
                if($business_type == "transportation"){
                    $desc = $row['desc'];
                }
                if(validate_date_month($ddd, "-1")){
                    $recent = "non-recent";
                } else {
                    $recent = "recent";
                }
                
                ?>

                <div class="row _transaction-record collapse-header <?php echo $recent;?>" data-toggle="collapse" data-target="#collapse_<?php echo $counter?>" aria-expanded="false" aria-controls="collapse_<?php echo $counter?>">
                    <div class="col col-12 d-none d-md-block">
                        <?php echo "$company - $branch" ?>
                    </div>
                    <?php 
                    if($business_type == "pharmacy"){
                        ?>
                        <div class="col col-12">
                            <?php echo $transaction_date ?>
                        </div>
                        
                        <div class="col col-12">
                            [<?php echo $generic_name_string; ?>]
                        </div>
                        
                        <div class="col col-12">
                            [<?php echo $generic_name_collective_string ; ?>]
                        </div>

                        <div class="col col-12 ">
                            <?php echo "$brand "."$dose"."$unit @ $quantity"."pcs" . "(Dosage in this purchase: $total_dosage)"?>
                            <br>
                            [Monthly: <?php echo $max_monthly?>] [Weekly: <?php echo $max_weekly?>]
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
                        <?php 
                    }
                    if($business_type == "food" || $business_type == "transportation" ){
                        ?>
                        
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
                        <?php
                    }
                    ?>
                </div>
                <?php
            }
            if($business_type == "pharmacy"){
                ?>
            
                <div class="row _transaction-record collapse-header flagged" data-toggle="collapse" aria-expanded="true" aria-controls="collapse_zzz" data-target="#collapse_zzz">
                    <div class="col col-12 d-none d-md-block">
                        Watsons - Portal, GMA, Cavite
                    </div>
                    <div class="col col-12">
                        2020-09-02 14:10:45
                    </div>
                        
                    <div class="col col-12">
                        [Ibuprofen, Paracetamol]
                    </div>

                    <div class="col col-12 ">
                        Alaxan 500mg @ 10pcs
                    </div>
                    <div class="col collapse show" style="" id="collapse_zzz" aria-labelledby="headingzzz">
                        <div class="row">
                            <div class="col col-6">
                                VAT Exempt Price:
                            </div>
                            <div class="col col-6 _transaction-record-right">
                                ₱1,800.00
                            </div>
                            <div class="col col-6">
                                Discounted Price:
                            </div>
                            <div class="col col-6 _transaction-record-right">
                                (₱360.00)
                            </div>
                        </div>
                    </div>     
                    <div class="col col-12">
                        <div class="row">
                            <div class="col col-6">
                                Amount to pay:
                            </div>
                            <div class="col col-6 _transaction-record-right">
                                ₱1,440.00
                            </div>
                        </div>
                    </div>
                </div>
                <?php
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