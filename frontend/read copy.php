<?php 
    include_once('../backend/session.php');
    include_once('../backend/terminal_scripts.php');
    if(isset($_POST['input_nfc']) && $_POST['input_nfc'] != "")
    {
        $picture = "../resources/images/unknown_m_f.png";
        $osca_id = "";
        $selected_id = "";
        $first_name = "";
        $middle_name = "";
        $last_name = "";
        $sex2 = "";
        $bdate = "";
        $memship_date = "";
        $contact_number = "";
        $json_string= null;
        include('../backend/read.php');
        
        if($member_exists)
        {
            include_once('../backend/php_functions.php');
            ?>
             <!-- TEMPORARY!!! -->
                <?php $myJSON = json_encode($_SESSION); ?>
                <script> console.log(<?php echo $myJSON; ?>); </script>
             <!-- TEMPORARY!!! -->
            <div class="member-picture">
                <img src="<?php echo $picture; ?>" class="avatar">
            </div>
            <div class="member-details">
                <div class="row">
                    <div class="col col-md-12">
                        <?php echo $last_name;?>,
                        <?php echo $first_name;?> 
                        <?php echo $middle_name;?>
                    </div>
                    <div class="col col-md-12 d-none d-md-block _label">
                        (Lastname, Firstname, Middlename)
                    </div>
                </div>
                <div class="row">
                    <div class="col col-md-12">
                        <?php echo $bdate;?>
                    </div>
                    <div class="col col-md-12 d-none d-md-block _label">
                        Birthdate
                    </div>
                </div>
                <div class="row">
                    <div class="col col-md-12">
                        <?php echo determine_sex($sex2, "display_long"); ?>
                    </div>
                    <div class="col col-md-12 d-none d-md-block _label">
                        Gender
                    </div>
                </div>
                <div class="row">
                    <div class="col col-md-12">
                        <?php echo $contact_number;?>
                    </div>
                    <div class="col col-md-12 d-none d-md-block _label">
                        Contact Number
                    </div>
                </div>
                <div class="row">
                    <div class="col col-md-12">
                    </div>
                    <div class="col col-md-12 d-none d-md-block _label">
                        City
                    </div>
                </div>

            </div>
            <div class="foot">
                <button type="button" class="btn btn-block btn-light btn-lg" id="trans_history">History</button>
                <button type="button" class="btn btn-block btn-light btn-lg" id="new_trans">New Transaction</button>
                <button type="button" class="btn btn-block btn-danger btn-lg" id="home">Exit</button>
            </div>
            
            <script>

            $(document).ready(function(){
                $("body").on('click', "#trans_history", function () {
                    var osca_id = "<?php echo $osca_id;?>";
                    $('#body').load("../frontend/transaction_history.php", {osca_id: osca_id }, function(d){
                        if(d.trim() == "false"){
                            reload_home();
                        }
                    });
                });
            });
            </script>
            <?php
        } else {
            echo "false";
        }
    } else {
        echo "false"; 
    }
?>