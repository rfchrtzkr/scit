<?php 
    include_once('../backend/conn.php');
    include_once('../backend/session.php');
    include_once('../backend/terminal_scripts.php');

    if(isset($_POST['input_nfc'])){
        $input_nfc = $mysqli->real_escape_string($_POST['input_nfc']);
        $business_type = $_SESSION['business_type'];
        $format_memdate = "concat(day(`memship_date`), ' ', monthname(`memship_date`), ' ', year(`memship_date`))";
        $format_bdate = "CONCAT(LEFT(bdate,length(bdate)-4), ' 19**')";

        $query = "SELECT `member_id`,	`osca_id`,	`nfc_serial`,	`nfc_active`, `password`,	`first_name`,	`middle_name`,	`last_name`,
                    $format_bdate  `bdate`,	`sex`,	`contact_number`,	 $format_memdate `memship_date`, `picture`, `city`, `province`
                    FROM `view_members_with_guardian` WHERE `nfc_serial` = '$input_nfc' ORDER BY `a_is_active` DESC LIMIT 1";
        $result = $mysqli->query($query);
        $row_count = mysqli_num_rows($result);
        $row = mysqli_fetch_assoc($result);
        
        if($row_count == 1)
        {
            if(isset($_SESSION['qr_data']) || !empty($_SESSION['qr_data'])){
                $input_from_qr = true;
            } else {
                $nfc_active = ($row['nfc_active'] == "1") ? true: false;
            }
            if($nfc_active || $input_from_qr){
                $osca_id = $row['osca_id'];
                $selected_id = $row['member_id'];
                $first_name = $row['first_name'];
                $middle_name =  $row['middle_name'];
                $last_name =  $row['last_name'];
                $sex2 =  $row['sex'];
                $bdate =  $row['bdate'];
                $city =  $row['city'];
                $province =  $row['province'];
                $memship_date =  $row['memship_date'];
                $fullname = strtoupper("$first_name $middle_name $last_name");
                
                $_SESSION['osca_id'] = $row['osca_id'];
                $_SESSION['sr_full_name'] = $fullname;
    
                if($_SESSION['business_type'] == 'pharmacy') {
                    include('../backend/read_pharmacy_transactions.php');
                }
                
                $picture =  "../resources/members/".$row["picture"]; 
    
                if (file_exists($picture) && $row["picture"] != null) {
                    $picture =  "../resources/members/".$row["picture"]; 
                } else {
                    $picture = "../resources/images/unknown_m_f.png";
                }
<<<<<<< HEAD
                $member_valid = true;
            } else {
                $member_valid = false;
=======
>>>>>>> a9f5761bc5f1e8c543307371c7ece297b3e06aca
            }
            $member_exists = true;
        } else {
            $member_exists = false;
            $member_valid = false;
        }
        mysqli_close($mysqli);// Closing Connection
    } else {
        $member_exists = false;
        $member_valid = false;
    }
    senior_isValid($member_valid);
?>
