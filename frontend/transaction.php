<?php
    include('../backend/session.php');
    include('../backend/php_functions.php');
    
    $formatter = new NumberFormatter("fil-PH", \NumberFormatter::CURRENCY);
    $total_discount = 0;
    $total_amount_to_pay = 0;
    $transaction_date = "";
    $clerk = "";
    $flagged_items = [];
    
    if(isset($_SESSION['osca_id'])) {
        $business_type = $_SESSION['business_type'];
        // -----------delete--------------
        ?>
        <script> console.log("Before encode:");console.log(<?php echo json_encode($_SESSION); ?>); </script>
        <?php
        // -----------delete--------------
        ?>
        <div class="title">
            TRANSACTION
        </div>
        <div class="title user">
            <?php echo $_SESSION['sr_full_name']; ?>
        </div>
        <div class="transaction scrollbar-black" id="trans123">
            <?php
            {
                $counter = 0;
                include('../backend/new_transaction.php');
                
                // get total of compound dosage in this transaction
                if($business_type == "pharmacy"){
                    $compound_dosage_transaction = [];
                    $max_basis_weekly = [];
                    $max_basis_monthly = [];
                    foreach($transaction as $row => $item){
                        if (!isset($item['desc'])) {
                            $brand = ucwords($item['brand']);
                            $dose = $item['dose'];
                            $quantity = $item['quantity'];
                            $total_dosage = $dose * $quantity;

                            $max_monthly = $item['max_monthly'];
                            $max_weekly = $item['max_weekly'];

                            $generic_name_string = $item['generic_name'];
                            
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
                }

                // Populate transactions list from $transactions array
                foreach($transaction as $row => $item){
                    $counter++;
                    $transaction_date = $item['trans_date'];
                    $clerk = $item['clerk'];
                    $vat_exempt_price = $formatter->format($item['vat_exempt_price']);
                    $discount_price = $formatter->format($item['discount_price']);
                    $payable_price = $formatter->format($item['payable_price']);
                    
                    $total_discount += (int)$item['discount_price'];
                    $total_amount_to_pay += (int)$item['payable_price'];
                    
                    if($business_type == "pharmacy"){
                        if (!isset($item['desc'])) {
                            $brand = ucwords($item['brand']);
                            $dose = $item['dose'];
                            $unit = $item['unit'];
                            $quantity = $item['quantity'];
                            $total_dosage = $dose * $quantity;

                            $is_otc = ($item['is_otc'] == '1') ? true: false;
                            $max_monthly = $item['max_monthly'];
                            $max_weekly = $item['max_weekly'];
                            $generic_name_string = $item['generic_name'];

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
                                $flagged_items[] = $item;
                            } else {
                                $maxed = "";
                            }
                        
                            // end of conditions
                            ?>
                            <div class="row _transaction-record collapse-header<?php echo " $maxed";?>" data-toggle="collapse" data-target="#collapse_<?php echo $counter?>" aria-expanded="true" aria-controls="collapse_<?php echo $counter?>">
                                <!--div class="col col-12">
                                    <?php /*echo "<b>(Dosage on this purchase: $total_dosage)</b>"; ?>
                                    <?php echo "<br><b>($generic_name_string: ".$_SESSION['compound_dosage_recent'][$generic_name_string].")</b>"; ?>
                                    <?php echo "<br><b>(Compound Total: ".$compound_total.")</b>"; ?>
                                    <?php echo "<br><b>(Max: ".$max_basis.")</b>"; */?>
                                </div-->
                                <div class="col col-12">
                                    <b><?php echo "$brand $dose"."$unit @ $quantity pcs"?></b>
                                </div>
                                <div class="col col-12">
                                    [ <?php echo $generic_name_string?> ]<br>
                                </div>
                            <?php 
                        } else {
                            $desc = ucwords($item['desc']);
                            ?>
                            <div class="row _transaction-record collapse-header" data-toggle="collapse" data-target="#collapse_<?php echo $counter?>" aria-expanded="true" aria-controls="collapse_<?php echo $counter?>">
                                <div class="col col-12">
                                    <b><?php echo $desc?></b>
                                </div>
                            <?php
                            
                        }?>

                        
                                <div id="collapse_<?php echo $counter?>" class="col col-12 collapse" aria-labelledby="heading<?php echo $counter?>">
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
                                            Amount to pay
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
                        <div class="row _transaction-record collapse-header" data-toggle="collapse" data-target="#collapse_<?php echo $counter?>" aria-expanded="true" aria-controls="collapse_<?php echo $counter?>">
                                <div class="col col-12">
                                <?php echo $desc ?>
                            </div>
                            <div id="collapse_<?php echo $counter?>" class="col col-12 collapse" aria-labelledby="heading<?php echo $counter?>">
                                <div class="row">
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
                                <div class="row">
                                    <div class="col col-6">
                                        Amount to pay
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
            }
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
                        <b><?php echo $formatter->format($total_amount_to_pay);?></b>
                    </div>
                </div>
                <div class="row">
                    <div class="col col-12">
                        <?php echo $transaction_date ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col col-12">
                        Cashier: <?php echo $clerk ?>
                    </div>
                </div>
        </div>
        </div>
        <?php
            if(count($flagged_items) > 0) {
                $flagged = true;
                $counter = 0;
                ?>
                <div id="myModal" class="modal">
                    <div class="modal-content">
                        <div class="modal-header">
                        </div>
                        <div class="modal-body">
                            <p>Some items are invalid:</p>
                            <ul class="invalid-list scrollbar-black">
                                <?php
                                    foreach($flagged_items as $row => $item){
                                        $counter++;

                                        $brand = ucwords($item['brand']);
                                        $dose = $item['dose'];
                                        $unit = $item['unit'];
                                        $quantity = $item['quantity'];
                                        
                                        echo "<li>Item #$counter: $brand $dose"."$unit @ $quantity pcs</li>";
                                        
                                    }
                                ?>
                            </ul>
                        </div>
                        <div class="modal-footer">
                        </div>
                    </div> 
                </div>
                <?php
            } else {
                $flagged = false;
                
            } ?>
            
            <div class="foot">
                    <button type="button" class="btn btn-block btn-success btn-lg" id="accept" <?php echo ($flagged)? "disabled": "";?>>Accept</button>
                </div>
            <div class="foot">
                <button type="button" class="btn btn-block btn-light btn-lg" id="return">Return</button>
            </div>
            
            
        <script>
            
            var modal = document.getElementById("myModal");
            

            window.onclick = function(event) {
                if (event.target == modal) {
                    modal.style.display = "none";
                }
            } 

            $(document).ready(function(){
                $("#return").click(function(){
                    $('#body').load("../frontend/home.php #home");
                });/*
                $("#2").click(function(){
                    var action = "accepted";

                    alert("accepted");
                    console.log(action);
                    console.log(<?php echo json_encode($transaction); ?>);
                });*/
                $("#accept").click(function(){
                    //alert("accepted");
                    var trans = JSON.stringify(<?php echo json_encode($transaction); ?>);
                    alert(trans);
                    $.post("../backend/create_transaction.php", { accepted: true, transaction: trans}, function(d){
                        alert(d);
                        $('#trans123').append(d);
                        /*
                        if(d == "true") {
                            $('#trans123').load(d);
                            
                        } else {
                            alert(d);
                        }*/
                    });
                });


            });


            
            console.log("After encode:");
            console.log(<?php echo json_encode($_SESSION); ?>);
            console.log("transaction:");
            console.log(<?php echo json_encode($transaction); ?>);
        </script>
        <?php
    } else {
        echo "false";
    }
?>