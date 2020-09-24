<?php
    include_once("../backend/php_functions.php");

    // CODE BLOCK BELOW: simulation only
    {
        $unregistered_drugs_1 = array();
        foreach ($unregistered_drugs as $row => $drug){
            $drug['is_otc'] = "1";
            $drug['max_monthly'] = "7000";
            $drug['max_weekly'] = "30000";
            $unregistered_drugs_1[] = $drug;
        }
    }

    // $unregistered_drugs = json_decode($_POST['unregisted_drugs']);
    // $drugs_json_object = $unregistered_drugs;

    $drugs_json_object = $unregistered_drugs_1;
    $invalid_inputs = array();

    echo "<div>";
    foreach ($drugs_json_object as $row => $drug) {
        $generic_name = $drug['generic_name'];
        $brand = $drug['brand'];
        $dose = $drug['dose'];
        $unit = $drug['unit'];
        $is_otc = $drug['is_otc'];
        $max_monthly = $drug['max_monthly'];
        $max_weekly = $drug['max_weekly'];
        $return = create_drug($generic_name, $brand, $dose, $unit, $is_otc, $max_monthly, $max_weekly);
        if($return != "created"){
            // send msg to POS, msg
            $invalid_inputs["msg"] = "invalid_inputs";
            if(array_key_exists("inputs", $invalid_inputs)){
                array_push($invalid_inputs["inputs"] , $return);
            } else {
                $invalid_inputs["inputs"] = array($return);
            }
        }
    }

    if(count($invalid_inputs) > 0) {
        $invalid_inputs = json_encode($invalid_inputs);
    } else {
        unset($invalid_inputs);
    }

    if(isset($invalid_inputs)) {
        // WILL ONLY APPEAR when drug inputs were rejected by: php_functions.php => function create_drug()
        ?>
        <script>
            // create implementation to send $invalid_inputs to POS using Serial comms
            console.log("create_drugs > invalid_inputs:");
            console.log(<?php echo $invalid_inputs;?>);
        </script>
        <?php
    }

    echo "</div>";
    ?>