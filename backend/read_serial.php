<?php
    include_once("../backend/terminal_scripts.php");
<<<<<<< HEAD
<<<<<<< HEAD
    include_once("../backend/session.php");
=======
>>>>>>> a9f5761bc5f1e8c543307371c7ece297b3e06aca
=======
>>>>>>> a9f5761bc5f1e8c543307371c7ece297b3e06aca

    $a = serial_read();
    $b = json_decode($a, true);
    $_SESSION['transaction_from_pos'] = $a;

    if($b != null || $b != ""){
        $_SESSION['transaction_objects'] = $b;
        echo "true";
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
