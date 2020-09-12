<?php
    include('../backend/session.php');
    include('../backend/conn_members.php');
    include('../backend/php_functions.php');
    
    if(isset($_SESSION['osca_id'])) {
        // -----------delete--------------
        ?>
        <script> console.log("Before encode:");console.log(<?php echo json_encode($_SESSION); ?>); </script>
        <?php
        // -----------delete--------------
        $selected_id = $_SESSION['osca_id'];
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
        }
        if($business_type == "transportation"){
        }
        include('../backend/conn_members.php');
        $result = $mysqli_2->query($transaction_query);
        $row_count = mysqli_num_rows($result);
        ?>
        <div class="title">
            TRANSACTION
        </div>
        <div class="title user">
            <?php echo $_SESSION['sr_full_name']; ?>
        </div>
        <div class="transaction scrollbar-black">
            <?php
            if($row_count != 0)
            {
                $counter = 0;
                $total_discount = 0;
                $total_amount_to_pay = 0;
                $transaction_date = "";

                include('../backend/new_transaction.php');

                $compound_dosage_transaction = [];
                $max_basis_weekly = [];
                $max_basis_monthly = [];
                
                // get total of compound dosage in this transaction
                if($business_type == "pharmacy"){
                    foreach($transaction as $row => $item){
                        $brand = ucwords($item['brand']);
                        $dose = $item['dose'];
                        $quantity = $item['quantity'];
                        $generic_name_string = "";

                        $max_monthly = $item['max_monthly'];
                        $max_weekly = $item['max_weekly'];
                        $total_dosage = $dose * $quantity;

                        $generic_name_collective = str_replace('  ', ' ', $item['generic_name']);
                        $generic_name_collective = str_replace(', ', ',', $generic_name_collective);
                        $generic_names = explode(',', $generic_name_collective);
                        $count_generic_names=count($generic_names);
                        sort($generic_names);

                        for( $i = 0 ; $i < $count_generic_names ; $i++ ){
                            $generic_name = $generic_names[$i];
                            $generic_name_string .= ucwords($generic_name);
                            if ($i >= 0 && $count_generic_names > 1 && ($count_generic_names - 1) != $i)
                            {
                                $generic_name_string .= ", ";
                            }
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
                        
                        if (array_key_exists($generic_name_string, $compound_dosage_transaction)){
                            $compound_dosage_transaction[$generic_name_string] += $total_dosage;
                        } else {
                            $compound_dosage_transaction[$generic_name_string] = $total_dosage;
                        }
                    }
                }

                foreach($transaction as $row => $item){
                    $counter++;
                    $formatter = new NumberFormatter("fil-PH", \NumberFormatter::CURRENCY);
                    $transaction_date = $item['trans_date'];
                    $vat_exempt_price = $formatter->format($item['vat_exempt_price']);
                    $discount_price = $formatter->format($item['discount_price']);
                    $payable_price = $formatter->format($item['payable_price']);
                    
                    $total_discount += (int)$item['discount_price'];
                    $total_amount_to_pay += (int)$item['payable_price'];
                    
                    if($business_type == "pharmacy"){
                        $brand = ucwords($item['brand']);
                        $dose = $item['dose'];
                        $unit = $item['unit'];
                        $is_otc = ($item['is_otc'] == '1') ? true: false;
                        $quantity = $item['quantity'];

                        $max_monthly = $item['max_monthly'];
                        $max_weekly = $item['max_weekly'];
                        
                        $generic_name_string = "";
                        $total_dosage = $dose * $quantity;
                        $generic_name_collective = str_replace('  ', ' ', $item['generic_name']);
                        $generic_name_collective = str_replace(', ', ',', $generic_name_collective);
                        $generic_names = explode(',', $generic_name_collective);
                        
                        $count_generic_names=count($generic_names);
                        sort($generic_names);

                        for( $i = 0 ; $i < $count_generic_names ; $i++ ){
                            $generic_name = $generic_names[$i];
                            $generic_name_string .= ucwords($generic_name);
                            if ($i >= 0 && $count_generic_names > 1 && ($count_generic_names - 1) != $i)
                            {
                                $generic_name_string .= ", ";
                            }
                        }

                        $compound_total = 0;


                         // check if item[compound] exists in session[compounds_recent]
                        if (array_key_exists($generic_name_string, $_SESSION['compound_dosage_recent'])){
                            $compound_total = $compound_dosage_transaction[$generic_name_string] + $_SESSION['compound_dosage_recent'][$generic_name_string];
                            $max_basis = ($is_otc)? $_SESSION['max_basis_weekly'][$generic_name_string]: $_SESSION['max_basis_monthly'][$generic_name_string];
                        } else {
                            $compound_total = $compound_dosage_transaction[$generic_name_string];
                            $max_basis = ($is_otc)? $max_basis_weekly[$generic_name_string]: $max_basis_monthly[$generic_name_string];
                        }
                        
                        if($compound_total >= $max_basis){
                            $maxed = "flagged";
                        } else {
                            $maxed = "";
                        }

                        ?>
                        <div class="row _transaction-record <?php echo $maxed;?>">
                            <div class="col col-12">
                                <?php echo "<b>(Dosage on this purchase: $total_dosage)</b>"; ?>
                                <?php echo "<br><b>($generic_name_string: ".$_SESSION['compound_dosage_recent'][$generic_name_string].")</b>"; ?>
                                <?php echo "<br><b>(Compound Total: ".$compound_total.")</b>"; ?>
                                <?php echo "<br><b>(Max: ".$max_basis.")</b>"; ?>
                            </div>
                            <div class="col col-12">
                                <b><?php echo "$brand $dose"."$unit @ $quantity pcs"?></b>
                            </div>
                            <div class="col col-12">
                                [ <?php echo $generic_name_string?> ]<br>
                            </div>
                            <div class="col col-12">
                                <div class="row">
                                    <div class="col col-6">
                                        VAT Exempt Price:
                                    </div>
                                    <div class="col col-6 _transaction-record-right">
                                        <?php echo $vat_exempt_price?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col col-6">
                                        Discounted Price:
                                    </div>
                                    <div class="col col-6 _transaction-record-right">
                                        (<?php echo $discount_price ?>)
                                    </div>
                                </div>
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
                        $desc = $item['desc'];
                        ?>
                        <div class="row _transaction-record">
                            <div class="col col-12">
                                <?php echo $desc ?>
                            </div>
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
            } else {echo "<div class='col col-md-8 text-center mx-auto mt-5'> No active transaction</div>";}
            mysqli_close($mysqli_2);
            ?>
        </div>
        <div class="transaction-summary">
            <div class="col col-12">
                <div class="row">
                    <div class="col col-md-8">
                    Total Discount <small>(After VAT Exempt)</small>
                    </div>
                    <div class="col col-md-4 _transaction-record-right">
                        <?php echo $formatter->format($total_discount);?>
                    </div>
                </div>
                <div class="row">
                    <div class="col col-md-8">
                    Amount to Pay
                    </div>
                    <div class="col col-md-4 _transaction-record-right">
                        <?php echo $formatter->format($total_amount_to_pay);?>
                    </div>
                </div>
                <div class="row">
                    <div class="col col-6">
                        <?php echo $transaction_date ?>
                    </div>
                    <div class="col col-6 _transaction-record-right">
                    </div>  
                </div>  
        </div>
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
        echo "false2";
    }

?>

<!-- verify if this is correct, most probably yes-->
<script>
    console.log("After encode:");
    console.log(<?php echo json_encode($_SESSION); ?>);
    console.log(<?php echo json_encode($compound_dosage_transaction); ?>);
    console.log("compound_total:");
    console.log(<?php echo $compound_total; ?>);
</script>