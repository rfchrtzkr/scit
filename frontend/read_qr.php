<?php
    include('../backend/session.php');
    include_once("../backend/conn.php");
    
    if(isset($_POST['qr_code']) && $_POST['qr_code'] != "") {
        $qr_code = $mysqli->real_escape_string($_POST['qr_code']);
        $qr_code = strtolower($qr_code);

        $qry = " SELECT `first_name`, `last_name`, `desc`, `trans_date`, 
                                        `city`, `province`, `nfc_serial`
                                FROM `view_qr_request` qr
                                INNER JOIN `view_members_with_guardian` m on qr.member_id = m.member_id
                                WHERE qr.`token` = '$qr_code'
                                ORDER BY `a_is_active` desc LIMIT 1;";
            
        if($result = $mysqli->query($qry)){
            $row_count = mysqli_num_rows($result);
            if($row_count == 1) {
                unset($date);
                unset($time);
                $qr_request = mysqli_fetch_assoc($result);
                $nfc_serial = $qr_request['nfc_serial'];
                $first_name = $qr_request['first_name'];
                $last_name = $qr_request['last_name'];
                $desc = $qr_request['desc'];
                $trans_date = date_create($qr_request['trans_date']);
                $date = date_format($trans_date,"d M Y");
                $time = date_format($trans_date,"h:i A") ;
                $city = $qr_request['city'];
                $province = $qr_request['province'];
                ?>
                <div id="qr_modal" class="modal">
                    <div class="modal-content">
                        <div class="modal-header">
                        </div>
                        <div class="modal-body">
                            <?php
                                echo "<p>$first_name $last_name</p>";
                                echo "<p>$city, $province</p>";
                                echo "<p>$time</p>";
                                echo "<p>$date</p>";

                                echo "<p>$desc</p>";
                            ?>
                            <button class="btn btn-lg btn-dark" id="accept">Accept</button>
                            <button class="btn btn-lg btn-danger" id="cancel">Cancel</button>
                        </div>
                        <div class="modal-footer">
                        </div>
                    </div> 
                </div>
                <script>
                    var modal = document.getElementById("qr_modal");
                    window.onclick = function(event) {
                        if (event.target == modal) {
                            modal.style.display = "none";
                        }
                    }

                    $("#accept").click(function(){
                        modal.style.display = "none";
                        var input_nfc = '<?php echo $nfc_serial;?>';
                        
                        $('#body').load("../frontend/read.php", { input_nfc: input_nfc }, function(d){
                            if(d == "false"){
                                alert("Invalid user " + input_nfc);
                                $('#body').load("../frontend/home.php #home");
                            }
                        });
                    });
                </script> 
                <?php
            } else{
                // no record
            }
        } else {
            // query didnt work
        }
        mysqli_close($mysqli);
    } else {
        //qr_code not posted
    }
?>