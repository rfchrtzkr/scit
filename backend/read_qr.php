<?php
    include_once("../backend/terminal_scripts.php");
    include_once("../backend/session.php");

    $qr = qr_read();

    if($qr != null || $qr != ""){
        $_SESSION['qr_code'] = $qr;
        echo $qr;
    } else {
        echo "false";
    }
