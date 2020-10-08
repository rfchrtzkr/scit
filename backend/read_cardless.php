<?php
    include_once("../backend/terminal_scripts.php");
    include_once("../backend/session.php");
    include_once("../backend/conn.php");
    
    $cardless = serial_read();

    {
        //$data['osca_id'] = "0421-2000004";
        //$data['first_name'] = "Stephine";
        //$data['last_name'] = "Lamagna";
        //$cardless = true;
    }

    if($cardless != null || $cardless != ""){
        $data = json_decode($cardless, true);

        $osca_id = $data['osca_id'];
        $first_name = strtolower($data['first_name']);
        $last_name = strtolower($data['last_name']);
        $query = "SELECT `nfc_serial`
            FROM `view_members_with_guardian` m
            WHERE `osca_id` = '$osca_id'
            AND LOWER(`first_name`) = '$first_name'
            AND LOWER(`last_name`) = '$last_name'
            GROUP BY `osca_id`;";
        
        $result = $mysqli->query($query);
        $row_count = mysqli_num_rows($result);
        if($row_count > 0) 
        {
            $row = mysqli_fetch_array($result);
            echo $row['nfc_serial'];
        } else {
            echo "invalid_details";
        }
        mysqli_close($mysqli);

    } else {
        echo "no_received";
    }