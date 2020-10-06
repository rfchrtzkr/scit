<?php
    include_once("../backend/terminal_scripts.php");
<<<<<<< HEAD
<<<<<<< HEAD
    include_once("../backend/session.php");
=======
>>>>>>> a9f5761bc5f1e8c543307371c7ece297b3e06aca
=======
>>>>>>> a9f5761bc5f1e8c543307371c7ece297b3e06aca

    $nfc = nfc_read();

    if($nfc != null || $nfc != ""){
        $_SESSION['nfc_data'] = $nfc;
        echo $nfc;
    } else {
        echo "false";
<<<<<<< HEAD
<<<<<<< HEAD
    }
=======
    }
>>>>>>> a9f5761bc5f1e8c543307371c7ece297b3e06aca
=======
    }
>>>>>>> a9f5761bc5f1e8c543307371c7ece297b3e06aca
