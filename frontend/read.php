<?php 


    include('../backend/conn_members.php');
    include('../backend/php_functions.php');
    
    $picture = "../resources/images/unknown_m_f.png";

    if(isset($_POST['input_nfc']) && $_POST['input_nfc'] != "" && isset($_POST['business_type']))
    {
        $input_nfc = $_POST['input_nfc'];
        $business_type = $_POST['business_type'];
        $format_memdate = "concat(day(`membership_date`), ' ', monthname(`membership_date`), ' ', year(`membership_date`))";
        $format_bdate = "concat(day(`birth_date`), ' ', monthname(`birth_date`), ' ', year(`birth_date`))";
        $query = "SELECT `id`,	`osca_id`,	`nfc_serial`,	`password`,	`first_name`,	`middle_name`,	`last_name`,
                    $format_bdate  `bdate`,	`sex`,	`contact_number`,	 $format_memdate `memship_date`, `picture` 
                    FROM `member` WHERE `nfc_serial` = '$input_nfc'";
        $result = $mysqli_2->query($query);
        $row_count = mysqli_num_rows($result);
        $row = mysqli_fetch_assoc($result);
        if($row_count == 0) { echo 'false';} else
        {
            $osca_id = $row['osca_id'];
            $selected_id = $row['id'];
            $first_name = $row['first_name'];
            $middle_name =  $row['middle_name'];
            $last_name =  $row['last_name'];
            $sex2 =  $row['sex'];
            $bdate =  $row['bdate'];
            $memship_date =  $row['memship_date'];
            $contact_number =  $row['contact_number'];
            
            
            $picture =  "../resources/members/".$row["picture"]; 

            if (file_exists($picture) && $row["picture"] != null) {
                $picture =  "../resources/members/".$row["picture"]; 
            } else {
                $picture = "../resources/images/unknown_m_f.png";
            }

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
        }
        mysqli_close($mysqli_2);// Closing Connection
    } else {
        echo 'false'; 
    }
?>