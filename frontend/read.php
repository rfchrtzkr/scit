<?php 


    include('../backend/conn_members.php');
    include('../backend/php_functions.php');
    
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

    if(isset($_POST['input_nfc']) && $_POST['input_nfc'] != "" && isset($_POST['business_type']))
    {
        include('../backend/read.php');
        ?>
        <div class="member-picture">
            <img src="<?php echo $picture; ?>" class="avatar">
        </div>
        <div class="member-details">
            <div class="row">
                <div class="col col-md-12 d-none d-md-block _label">
                    Lastname
                </div>
                <div class="col col-md-12">
                    <?php echo $last_name;?>
                </div>
            </div>
            <div class="row">
                <div class="col col-md-12 d-none d-md-block _label">
                    Firstname
                </div>
                <div class="col col-md-12">
                    <?php echo $first_name;?>
                </div>
            </div>
            <div class="row">
                <div class="col col-md-12 d-none d-md-block _label">
                    Middlename
                </div>
                <div class="col col-md-12">
                    <?php echo $middle_name;?>
                </div>
            </div>
            <div class="row">
                <div class="col col-md-12 d-none d-md-block _label">
                    Birthdate
                </div>
                <div class="col col-md-12">
                    <?php echo $bdate;?>
                </div>
            </div>
            <div class="row">
                <div class="col col-md-12 d-none d-md-block _label">
                    Gender
                </div>
                
                <div class="col col-md-12">
                    <?php echo determine_sex($sex2, "display_long"); ?>
                </div>
            </div>
            <div class="row">
                <div class="col col-md-12 d-none d-md-block _label">
                    Contact Number
                </div>
                <div class="col col-md-12">
                    <?php echo $contact_number;?>
                </div>
            </div>
            <div class="row">
                <div class="col col-md-12 d-none d-md-block _label">
                    City
                </div>
                <div class="col col-md-12">
                </div>
            </div>

        </div>
        <div class="foot">
            <button type="button" class="btn btn-block btn-light btn-lg" id="trans_history">Transactions History</button>
        </div>
        
        <script>
            $(document).ready(function(){

                $("#trans_history").click(function(){            
                    var osca_id = "<?php echo $osca_id;?>";
                    
                    $.post("../frontend/transaction_history.php", {osca_id: osca_id }, function(d){
                        if(d != "false"){
                            $('#body').load("../frontend/transaction_history.php", { osca_id: osca_id  });
                        } else {
                            //$('#body').load("../frontend/home.php #home");
                            alert(d);
                        }
                    });
                });

            });
        </script>
        <?php
    } else {
        echo 'false'; 
    }
?>