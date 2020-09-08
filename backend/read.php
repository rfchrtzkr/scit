<?php 
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
    if($row_count == 1)
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
        
    } else {
        echo "false";
    }
    mysqli_close($mysqli_2);// Closing Connection
?>