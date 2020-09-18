<?php
    include_once("../backend/php_functions.php");
    // unregistered_drugs are from POS


    $drugs_json_string = '[
        {
        "generic_name": "cetirizinex",
        "brand": "Brand 2",
        "dose": "10",
        "unit": "mg",
        "is_otc": "1",
        "max_monthly": "70",
        "max_weekly": "300"
        },
        {
        "generic_name": "Carbocisteine, Zincx",
        "brand": "Solmux",
        "dose": "500",
        "unit": "mg",
        "is_otc": "1",
        "max_monthly": "7000",
        "max_weekly": "30000"
        },
        {
        "generic_name": "Sodium Ascorbate, Zincx",
        "brand": "ImmunproMAX",
        "dose": "500",
        "unit": "mg",
        "is_otc": "1",
        "max_monthly": "7000",
        "max_weekly": "30000"
        }
    ]';

    $unregistered_drugs[] = "";

    $drugs_json_object = json_decode($drugs_json_string, true);
    $counter= 0;

    foreach ($drugs_json_object as $row => $drug) {
        $counter++;
        $generic_name = $drug['generic_name'];
        $brand = $drug['brand'];
        $dose = $drug['dose'];
        $unit = $drug['unit'];
        $is_otc = $drug['is_otc'];
        $max_monthly = $drug['max_monthly'];
        $max_weekly = $drug['max_weekly'];
        if(create_drug($generic_name, $brand, $dose, $unit, $is_otc, $max_monthly, $max_weekly)) {
            ?>
            <script>
                alert("<?php echo "$counter create_drug($generic_name, $brand, $dose, $unit, $is_otc, $max_monthly, $max_weekly)"?>");
            </script>
            <?php
        }
    }