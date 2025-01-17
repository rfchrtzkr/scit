

<?php
    function determine_sex($sex_, $mode) {
        $sex_ = strtolower($sex_);
        if($mode == "display_long") {
            switch ($sex_){
                case 0:
                    return "Unknown";
                    break;
                case 1:
                    return "Male";
                    break;
                case 2:
                    return "Female";
                    break;
                case 9:
                    return "Indeterminate";
                    break;
                default:
                    return "NA";
                    break;
            }
        } else if($mode == "display_short") {
            switch ($sex_){
                case 0:
                    return "NA";
                    break;
                case 1:
                    return "M";
                    break;
                case 2:
                    return "F";
                    break;
                case 9:
                    return "NA";
                    break;
                default:
                    return "NA";
                    break;
            }
        } else if($mode == "post") {
            switch ($sex_){
                case "unkown":
                    return "0";
                    break;
                case "male":
                    return "1";
                    break;
                case "female":
                    return "2";
                    break;
                case "indeterminate":
                    return "9";
                    break;
                default:
                    return "0";
                    break;
            }
        }
        else {return false;}
    }


    function read_address2($selected_id, $type="member")
    {
        if(isset($selected_id))
        {
            include('../backend/conn.php');
            if($type=="member"){
                $address_query = " SELECT `a`.`id` `address_id`, `address1`, `address2`, `city`, `province`, `is_active`  
                                FROM `member` m
                                INNER JOIN `address_jt` `ajt` ON `ajt`.`member_id` = m.`id`
                                INNER JOIN `address` `a` ON `ajt`.`address_id` = a.`id`
                                WHERE m.`id` = '$selected_id'";
            } else if($type == "company") {
                $address_query = " SELECT `a`.`id` `address_id`, `address1`, `address2`, `city`, `province`, `is_active`  
                                FROM `company` c
                                INNER JOIN `address_jt` `ajt` ON `ajt`.`company_id` = c.`id`
                                INNER JOIN `address` `a` ON `ajt`.`address_id` = a.`id`
                                WHERE c.`company_tin` = '$selected_id'";
            } else if($type == "guardian") {
                $address_query = " SELECT `a`.`id` `address_id`, `address1`, `address2`, `city`, `province`, `is_active`  
                                FROM `guardian` g
                                INNER JOIN `address_jt` `ajt` ON `ajt`.`guardian_id` = g.`id`
                                INNER JOIN `address` `a` ON `ajt`.`address_id` = a.`id`
                                WHERE g.`id` = '$selected_id'";
            }
            
            $result = $mysqli->query($address_query);
            $row_count = mysqli_num_rows($result);
            if($row_count > 0){
                return mysqli_fetch_array($result);
            } else {
                // id does not have address
                return false;
            }
            mysqli_close($mysqli);
        } else {
            // no id given
            return false;
        }
    }

    function read_guardian($osca_id, $edit=true){
        if(isset($osca_id))
        {
            include('../backend/conn.php');
            $query = "SELECT * 
            FROM member m
            INNER JOIN guardian g ON g.member_id = m.id
            WHERE m.`osca_id` = '$osca_id';";

            $result = $mysqli->query($query);
            $row_count = mysqli_num_rows($result);
            if($row_count = 1) {
                if($edit) {
                    $query = "SELECT g.id `g_id`, g.`first_name` `g_first_name`, g.`middle_name` `g_middle_name`, g.`last_name` `g_last_name`, 
                                g.`sex` `g_sex`, g.`contact_number` `g_contact_number`, g.`email` `g_email`, g.`relationship` `g_relationship`
                                FROM `guardian` g
                                INNER JOIN `member` m on g.`member_id` = m.`id`        
                                WHERE m.`osca_id` = '$osca_id';";

                    $result = $mysqli->query($query);
                    $row_count = mysqli_num_rows($result);
                    if($row_count == 0) { echo "<p class='disp_guardian'>No guardian on record
                        <button class='btn btn-link edit add_guardian'><i class='fa fa-edit'></i></button>
                        </p> ";} else
                    {
                        while($row = mysqli_fetch_array($result))
                        {
                            $g_id = $row['g_id'];
                            $g_first_name = $row['g_first_name'];
                            $g_middle_name = $row['g_middle_name'];
                            $g_last_name =  $row['g_last_name'];
                            $g_sex2 =  $row['g_sex'];
                            $g_contact_number =  $row['g_contact_number'];
                            $g_email =  $row['g_email'];
                            $g_relationship =  $row['g_relationship'];
                            ?>
                            <div class="card disp_guardian" id="gid<?php echo $g_id ?>" >
                                <p>Full Name: <?php echo "$g_first_name $g_middle_name $g_last_name"; ?></p>
                                <p>Relationship: <?php echo "$g_relationship"; ?></p>
                                <p>Sex: <?php echo determine_sex($g_sex2, "display_long"); ?> </p>
                                <p>Contact Number: <?php echo "$g_contact_number"; ?></p>
                                <p>Email: <?php echo "$g_email"; ?></p>
                                
                                <button class="btn btn-link edit edit_guardian"><i class="fa fa-edit"></i></button>
                            </div>
                            <?php
                        }
                    }
                }
            }

            mysqli_close($mysqli);
        } else {echo "Member does not exist";}
    }

    function validate_date($date_to_validate = "", $year_offset = 0){
        if($date_to_validate != "" && (preg_match("/^((18|19|20)[0-9]{2}[\-.](0[13578]|1[02])[\-.](0[1-9]|[12][0-9]|3[01]))|(18|19|20)[0-9]{2}[\-.](0[469]|11)[\-.](0[1-9]|[12][0-9]|30)|(18|19|20)[0-9]{2}[\-.](02)[\-.](0[1-9]|1[0-9]|2[0-8])|(((18|19|20)(04|08|[2468][048]|[13579][26]))|2000)[\-.](02)[\-.]29$/",$date_to_validate)))
        {
            // Year offset is the valid date from the CURRENT DATE.
            // e.g. Year offset : "-60" means must be 60 years prior the date of input


            $test_arr  = explode('-', $date_to_validate);
            $timestamp = mktime(0, 0, 0, $test_arr[1], $test_arr[2], $test_arr[0]);



            //if($is_birthdate){
            //    $valid_date = strtotime(date("Y-m-d").' -18 year');
            //} else
            if($year_offset != 0){
                $valid_date = strtotime(date("Y-m-d").' '. $year_offset .' year');
            } else {
                $valid_date = strtotime(date("Y-m-d").' +1 day');
            }

            if (count($test_arr) == 3) {
                if (checkdate($test_arr[1], $test_arr[2], $test_arr[0]) && $timestamp <= $valid_date) {
                    return true;
                 } else {
                    return false;
                }
            } else {
                return false;
            }
        //} else {
        //    array_push($errors, "Invalid date");
        }
    }

    function formatCurrency($value)
    {
        $format = "$" . number_format(sprintf('%0.2f', preg_replace("/[^0-9.]/", "", $value)), 2);
        return $value < 0 ? "({$format})" : "{$format}";
    }
    

    

    function validate_date_month($date_to_validate = "", $month_offset = 0){
        if($date_to_validate != "" && (preg_match("/^((18|19|20)[0-9]{2}[\-.](0[13578]|1[02])[\-.](0[1-9]|[12][0-9]|3[01]))|(18|19|20)[0-9]{2}[\-.](0[469]|11)[\-.](0[1-9]|[12][0-9]|30)|(18|19|20)[0-9]{2}[\-.](02)[\-.](0[1-9]|1[0-9]|2[0-8])|(((18|19|20)(04|08|[2468][048]|[13579][26]))|2000)[\-.](02)[\-.]29$/",$date_to_validate)))
        {
            // Year offset is the valid date from the CURRENT DATE.
            // e.g. Year offset : "-60" means must be 60 years prior the $date_to_validate


            $test_arr  = explode('-', $date_to_validate);
            $timestamp = mktime(0, 0, 0, $test_arr[1], $test_arr[2], $test_arr[0]);



            //if($is_birthdate){
            //    $valid_date = strtotime(date("Y-m-d").' -18 year');
            //} else
            if($month_offset != 0){
                $valid_date = strtotime(date("Y-m-d").' '. $month_offset .' month');
            } else {
                $valid_date = strtotime(date("Y-m-d").' +1 day');
            }

            if (count($test_arr) == 3) {
                if (checkdate($test_arr[1], $test_arr[2], $test_arr[0]) && $timestamp <= $valid_date) {
                    return true;
                 } else {
                    return false;
                }
            } else {
                return false;
            }
        //} else {
        //    array_push($errors, "Invalid date");
        }
    }

    function arrange_generic_name($generic_name_in)
    {   
        try {
            $generic_name_collective = str_replace('  ', ' ', $generic_name_in);
            $generic_name_collective = str_replace(', ', ',', $generic_name_collective);
            $generic_names = explode(',', $generic_name_collective);
            $generic_name_string = "";
            $count_generic_names=count($generic_names);
            sort($generic_names);

            for( $i = 0 ; $i < $count_generic_names ; $i++ ){
                $generic_name = $generic_names[$i];
                $generic_name_string .= ucwords($generic_name);
                if ($i >= 0 && $count_generic_names > 1 && ($count_generic_names - 1) != $i) {
                    $generic_name_string .= ", ";
                }
            }
            return $generic_name_string;
        }
        catch(Exception $e) {
            echo 'Message: ' .$e->getMessage();
        }
    }
    
    function simplify_generic_name($generic_name_in)
    {   
        try {
            $generic_name_collective = trim(str_replace('  ', ' ', $generic_name_in));
            $generic_name_collective = str_replace(', ', ',', $generic_name_collective);
            $generic_names = explode(',', $generic_name_collective);
            $generic_name_string = "";
            $count_generic_names=count($generic_names);
            sort($generic_names);

            for( $i = 0 ; $i < $count_generic_names ; $i++ ){
                $generic_name = $generic_names[$i];
                $generic_name_string .= strtolower($generic_name);
                if ($i >= 0 && $count_generic_names > 1 && ($count_generic_names - 1) != $i) {
                    $generic_name_string .= ",";
                }
            }
            return $generic_name_string;
        }
        catch(Exception $e) {
            echo 'Message: ' .$e->getMessage();
        }
    }

    function validate_drug($generic_name, $brand, $dose, $unit)
    {   
        include('../backend/conn.php');
        $mysqli_function = new mysqli($host,$user,$pass,$schema) or die($mysqli_function->error);


        $generic_name = simplify_generic_name($generic_name);

        $query_function = "SELECT `id` FROM `view_drugs` WHERE `generic_name` = '$generic_name' AND `brand` = '$brand' AND  `dose` = '$dose' AND `unit` =  '$unit';";
        if($result = $mysqli_function->query($query_function)){
            $row_count = mysqli_num_rows($result);
            if($row_count == 1) {
                $row = mysqli_fetch_assoc($result);
                $id = $row['id'];
                return $id;
            } else{
                return 0;
            }
        }
        else {
            return false;
        }
        mysqli_close($mysqli_function);
    }

    function verify_drugs($drugs)
    {
        $unregistered_drugs = [];
        foreach($drugs as $row => $item_from_pos){
            if(isset($item_from_pos['generic_name'])){
                $item = [];
                $item['generic_name'] = simplify_generic_name($item_from_pos['generic_name']);
                $item['brand'] = $item_from_pos['brand']; // from pos
                $item['dose'] = $item_from_pos['dose']; // from pos
                $item['unit'] = $item_from_pos['unit']; // from pos
                $generic_name = strtolower($item['generic_name']);
                $brand = strtolower($item['brand']);
                $dose = $item['dose'];
                $unit = $item['unit'];
                $drug_id = validate_drug($generic_name, $brand, $dose, $unit);
                if($drug_id == 0) {
                    $unregistered_drugs[] = $item;
                }
            }
        }
        return $unregistered_drugs;
    }

    function create_drug($generic_name, $brand, $dose, $unit, $is_otc, $max_monthly, $max_weekly)
    {   
        include('../backend/conn_osca.php');
        $mysqli_function = new mysqli($host_2,$user_2,$pass_2,$schema_2) or die($mysqli_function->error);

        $input_array = array("generic_name" => $generic_name,
        "brand" => $brand,
        "dose" => $dose,
        "unit" => $unit,
        "is_otc" => $is_otc,
        "max_monthly" => $max_monthly,
        "max_weekly" => $max_weekly);
        
        $generic_name = simplify_generic_name($generic_name);
        $brand = strtolower($brand);
        $query_function = "CALL add_drug('$generic_name', '$brand', '$dose', '$unit', '$is_otc', '$max_monthly', '$max_weekly')";
        $query_function2 = "SELECT * FROM `view_drugs` WHERE `generic_name` = '$generic_name' AND `brand` = '$brand' AND  `dose` = '$dose' AND `unit` =  '$unit';";
        if($result = $mysqli_function->query($query_function2)){
            $row_count = mysqli_num_rows($result);
            if($row_count == 0) {
                $result = $mysqli_function->query($query_function);
                return "created";
            } else{
                // The input drug already exists
                $input_array["qry1"] = $query_function;
                $input_array["qry2"] = $query_function2;
                $input_array["error"] = "existing";
                return $input_array;
            }
        }
        else {
            // The query has an error
                $input_array["error"] = "invalid inputs";
                $input_array["qry1"] = $query_function;
                $input_array["qry2"] = $query_function2;
            return $input_array;
        }
        mysqli_close($mysqli_function);
    }

    function get_drug_details($drug_id, $generic_name = false, $brand = false, $dose = false, $unit = false)
    {   
        include('../backend/conn.php');
        $mysqli_function = new mysqli($host,$user,$pass,$schema) or die($mysqli_function->error);
        if($generic_name && $brand && $dose && $unit){
            $generic_name = simplify_generic_name($generic_name);
            $query_function = "SELECT `is_otc`, `max_monthly`, `max_weekly` FROM `view_drugs` WHERE `generic_name` = '$generic_name' AND `brand` = '$brand' AND  `dose` = '$dose' AND `unit` =  '$unit';";
        } else {
            $query_function = "SELECT `is_otc`, `max_monthly`, `max_weekly` FROM `view_drugs` WHERE `id` = '$drug_id';";
        }
        if($result = $mysqli_function->query($query_function)){
            $row_count = mysqli_num_rows($result);
            if($row_count == 1) {
                return mysqli_fetch_assoc($result);
            } else{
                return false;
            }
        }
        else {
            return false;
        }
        mysqli_close($mysqli_function);
    }

    function read_qr_code($qr_code = "false")
    {   
        if($qr_code != "false"){
            include('../backend/conn.php');
            $mysqli_function = new mysqli($host,$user,$pass,$schema) or die($mysqli_function->error);

            $query_function = " SELECT `first_name`, `last_name`, `desc`, `trans_date`, 
                                        `city`, `province`, `nfc_serial`
                                FROM `view_qr_request` qr
                                INNER JOIN `view_members_with_guardian` m on qr.member_id = m.member_id
                                WHERE qr.`token` = '$qr_code'
                                ORDER BY `a_is_active` desc LIMIT 1;";
            
            if($result = $mysqli_function->query($query_function)){
                $row_count = mysqli_num_rows($result);
                if($row_count == 1) {
                    return mysqli_fetch_assoc($result);
                } else{
                    return false;
                }
            }

            else {
                return false;
            }
            mysqli_close($mysqli_function);
        }
    }

    function read_from_serial($business_type = "")
    {
        // read from serial
        // invoked in: new transaction
        if($business_type != "")
        {
            $read_file_dest = "/var/www/html/rpiserial/$business_type.json";
    
            $json_file = fopen($read_file_dest, "r");
            return fread($json_file,filesize($read_file_dest));
        } else {
            return false;
        }

    }

    function read_from_serial_createDrug($status = false)
    {
        // read from serial drug creation
        // invoked in: create_drug
        if($status)
        {
            $read_file_dest = "/var/www/html/rpiserial/new_drug.json";
            $json_file = fopen($read_file_dest, "r");
            return fread($json_file,filesize($read_file_dest));
        } else {
            return false;
        }

    }


?>