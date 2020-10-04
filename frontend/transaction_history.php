<?php


    include('../backend/session.php');
    include('../backend/php_functions.php');
    include_once('../backend/terminal_scripts.php');
    if(isset($_POST['osca_id']))
    {
        include('../backend/conn.php');
        $selected_id = $_POST['osca_id'];
        $business_type = $_SESSION['business_type'];
        $company_tin = $_SESSION['company_tin'];
        $show_drugs_button = "";

        if($business_type == "pharmacy"){
            $show_drugs_button = '<div><button id="show_only_drugs"><span class="ui-button-text">Show Drugs Only</span></button></div>';
            $transaction_query = "SELECT `member_id`, `trans_date`, date(trans_date) `ddd`, `clerk`, `company_name` `company`, `branch`, `business_type`, `company_tin`,
                                    `desc_nondrug`, `generic_name`, `brand`, `dose`, `is_otc`, `max_monthly`, `max_weekly`, `unit`, `quantity`,
                                    `vat_exempt_price`, `discount_price`, `payable_price`
                                    FROM `view_pharma_transactions_all`
                                    WHERE `osca_id` = '$selected_id' AND date(trans_date) >= (LEFT(NOW() - INTERVAL 3 MONTH,10))
                                    ORDER BY `trans_date`;";
        }
        if($business_type == "food"){
            $transaction_query = "SELECT `member_id`, `trans_date`, date(trans_date) `ddd`, `clerk`, `company_name` `company`, `branch`, `business_type`, `company_tin`,
                                    `desc`,
                                    `vat_exempt_price`, `discount_price`, `payable_price`
                                    FROM `view_food_transactions` 
                                    WHERE `osca_id` = '$selected_id' AND `company_tin` = '$company_tin'AND date(trans_date) >= (LEFT(NOW() - INTERVAL 3 MONTH,10))
                                    ORDER BY `trans_date`;";
        }
        if($business_type == "transportation"){
            $transaction_query = "SELECT `member_id`, `trans_date`, date(trans_date) `ddd`, `clerk`, `company_name` `company`, `branch`, `business_type`, `company_tin`,
                                    `desc`,
                                    `vat_exempt_price`, `discount_price`, `payable_price`
                                    FROM `view_transportation_transactions`
                                    WHERE `osca_id` = '$selected_id' AND date(trans_date) >= (LEFT(NOW() - INTERVAL 3 MONTH,10))
                                    ORDER BY `trans_date`;";
        }
        $result = $mysqli->query($transaction_query);
        $row_count = mysqli_num_rows($result);
        ?>
            <div class="title">
                TRANSACTIONS HISTORY
            </div>
            <?php  echo $show_drugs_button; ?>
            <div class="transaction-history scrollbar-black">
        <?php
        
        if($row_count != 0)
        {
            $counter = 0;
            $result = $mysqli->query($transaction_query);
            while($row = mysqli_fetch_array($result))
            {
                $counter++;
                $transaction_date = $row['trans_date'];
                $clerk = ($row['clerk'] == "")? "N/A" : substr($row['clerk'], 0,4) . "***";
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
                    $desc_nondrug = $row['desc_nondrug'];
                    if ($desc_nondrug == "") {
                        $brand = ucwords($row['brand']);
                        $dose = $row['dose'];
                        $unit = $row['unit'];
                        $is_otc = ($row['is_otc'] == '1') ? true: false;
                        $quantity = $row['quantity'];
                        $total_dosage = $dose * $quantity;
                        $max_monthly = $row['max_monthly'];
                        $max_weekly = $row['max_weekly'];
                        $generic_name_string = arrange_generic_name($row['generic_name']);
                        $max_basis = ($is_otc)? $_SESSION['max_basis_weekly'][$generic_name_string]: $_SESSION['max_basis_monthly'][$generic_name_string];
                        // Validate if this generic_name is maxed for the month
                        if($_SESSION['compound_dosage_recent'][$generic_name_string] >= $max_basis && $recent == "recent"){
                            $maxed = "flagged";
                        } else {
                            $maxed = "";
                        }
                        ?>
                        <div class="row _transaction-record collapse-header drug <?php echo "$recent $maxed";?>" data-toggle="collapse" data-target="#collapse_<?php echo $counter?>" aria-expanded="false" aria-controls="collapse_<?php echo $counter?>">
                            <div class="col col-12 d-md-block">
                                <b><?php echo "$company - $branch"?></b>
                            </div>
                            <div class="col col-12">
                                <?php echo "$transaction_date <i>(By:  $clerk)</i>"?>
                            </div>
                            <div class="col col-12">
                                <?php echo "[ $generic_name_string ] <br>"?>
                            </div>
                            <div class="col col-12">
                                <b><?php echo "$brand "."$dose"."$unit @ $quantity"."pcs"?></b>
                            </div>
                        <?php
                    } else {
                        ?>
                        <div class="row _transaction-record collapse-header non-drug <?php echo "$recent";?>" data-toggle="collapse" data-target="#collapse_<?php echo $counter?>" aria-expanded="false" aria-controls="collapse_<?php echo $counter?>">
                            <div class="col col-12 d-md-block">
                                <b><?php echo "$company - $branch" ?></b>
                            </div>
                            <div class="col col-12">
                                <?php echo "$transaction_date <i>(By:  $clerk)</i>"?>
                            </div>
                            <div class="col col-12">
                                <?php echo $desc_nondrug?>
                            </div>
                        <?php
                        
                    }
                }
                if($business_type == "food" || $business_type == "transportation" ){
                    $desc = $row['desc'];
                    ?>
                    <div class="row _transaction-record collapse-header <?php echo "$recent";?>" data-toggle="collapse" data-target="#collapse_<?php echo $counter?>" aria-expanded="false" aria-controls="collapse_<?php echo $counter?>">
                        <div class="col col-12 d-md-block">
                            <b><?php echo "$company - $branch" ?></b>
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
                    <?php
                }
                ?>
                    <div id="collapse_<?php echo $counter?>" class="col collapse" aria-labelledby="heading<?php echo $counter?>">
                        <div class="row pl-3">
                            <div class="col col-6">
                                VAT Exempt Price
                            </div>
                            <div class="col col-6 _transaction-record-right">
                                <?php echo $vat_exempt_price?>
                            </div>
                            <div class="col col-6">
                                Discounted Price
                            </div>
                            <div class="col col-6 _transaction-record-right">
                                (<?php echo $discount_price ?>)
                            </div>
                        </div>
                    </div>
                    <div class="col col-12">
                        <div class="row pl-3">
                            <div class="col col-6">
                                Net Total
                            </div>
                            <div class="col col-6 _transaction-record-right">
                                <?php echo $payable_price ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            }
        } else {
            echo "<div class='col col-md-8 text-center mx-auto mt-5'>No $business_type discount(s) <br> recorded yet for this user</div>";
        }
        mysqli_close($mysqli);
        ?>
            </div>
        <div class="foot">
            <button type="button" class="btn btn-block btn-light btn-lg" id="return">Return</button>
        </div>
            
        <script>
        var business_type = '<?php echo $business_type;?>';

        $(document).ready(function(){
            var toggle_on = true;

            $("#show_only_drugs").click(function(){
                $(".non-drug").toggle(50, "linear");
                if(toggle_on){
                    $("span", this).text("Show All");
                    toggle_on = false;
                } else {
                    $("span", this).text("Show Drugs Only");
                    toggle_on = true;
                }
            });

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