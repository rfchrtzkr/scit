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
        
<<<<<<< HEAD
        if($member_exists && ($nfc_active || $input_from_qr))
=======
        if($member_exists && $nfc_active)
>>>>>>> a9f5761bc5f1e8c543307371c7ece297b3e06aca
        {
            include_once('../backend/php_functions.php');
            ?>
            <div class="member-picture">
                <img src="<?php echo $picture; ?>" class="avatar">
            </div>
            
            <div>
                <ul class="member-details">
                    <li class="profile-item">
                        <div class="content"><?php echo $fullname; ?></div>
                        <div class="subtitle">Fullname</div> 
                    </li>
                    <li class="profile-item">
                        <div class="content"><?php echo determine_sex($sex2, "display_long"); ?></div>
                        <div class="subtitle">Sex</div> 
                    </li>
                    <li class="profile-item">
                        <div class="content"><?php echo $bdate; ?></div>
                        <div class="subtitle">Birthdate</div> 
                    </li>   
                    <li class='profile-item'>
                        <div class="content"><?php echo "$city, $province";?></div>
                        <div class="subtitle">Address</div>
                    </li>
                </ul>
            </div>
            <div class="foot">
                <button type="button" class="btn btn-block btn-light btn-lg" id="trans_history">History</button>
                <button type="button" class="btn btn-block btn-light btn-lg" id="new_trans">New Transaction</button>
                <button type="button" class="btn btn-block btn-exit btn-lg" id="exit">Exit</button>
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
        } elseif($member_exists && !$nfc_active) {
            echo "inactive";
<<<<<<< HEAD
        } elseif($member_exists && $input_from_qr) {
            echo "inactive";
        } else {
            echo "false";
=======
>>>>>>> a9f5761bc5f1e8c543307371c7ece297b3e06aca
        }
    } else {
        echo "false"; 
    }
<<<<<<< HEAD
?>
=======
?>
>>>>>>> a9f5761bc5f1e8c543307371c7ece297b3e06aca
