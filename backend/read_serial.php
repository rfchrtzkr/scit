<?php
    include_once("../backend/terminal_scripts.php");

    $a = serial_read();
    $b = json_decode($a, true);
    $_SESSION['transaction_from_pos'] = $a;

    if($b != null || $b != ""){
        $_SESSION['transaction_objects'] = $b;
        echo "true";
    } else {
        echo "false";
    }