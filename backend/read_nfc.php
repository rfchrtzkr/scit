<?php
    include_once("../backend/terminal_scripts.php");
    include_once("../backend/session.php");
    
    $nfc = nfc_read();

    if($nfc != null || $nfc != ""){
        $_SESSION['nfc_data'] = $nfc;
        echo $nfc;
    } else {
        echo "false";
    }