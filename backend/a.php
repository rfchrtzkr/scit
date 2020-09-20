<div>



</div> <!-- codes -->

<br><br>

<div>
<?php
include("php_functions.php");
/*
echo "<br>output: ". simplify_generic_name("Paracetamol,   Ibuprofen");
echo "<br>output: ". simplify_generic_name("Ibuprofen, Paracetamol");
$drug = get_drug_details("3");


$item['generic_name'] = arrange_generic_name("calcium carbonate,famotidine,magnesium hydroxide");
$item['brand'] = "kremil-s advance"; // from pos
$item['dose'] = (int)500; // from pos
$item['unit'] = "mg"; // from pos

$generic_name = $item['generic_name'];
$brand = $item['brand'];
$dose = $item['dose'];
$unit = $item['unit'];
$drug2 = get_drug_details("", "$generic_name", "$brand", "$dose", "$unit");

var_dump($drug);
var_dump($drug2);
echo "<br><br><hr><br>";
$int1 = (double)filter_var("100");
$int2 = (float)filter_var("100");
$int3 = (int)filter_var("100", FILTER_VALIDATE_INT);
$int4 = filter_var("100323", FILTER_VALIDATE_INT);

var_dump($int1);
var_dump($int2);
var_dump($int3);
var_dump($int4);

$json_string = '[
    {
    "clerk": "AL Manalon",
    "generic_name": "calcium carbonate,famotidine,magnesium hydroxide",
    "brand": "biogesic",
    "dose": "500",
    "unit": "mg",
    "unit_price": "5.20",
    "quantity": "1",
    "vat_exempt_price": "65",
    "discount_price": "13",
    "payable_price": "52",
    "trans_date": "2020-09-16 11:15:10"
    }
]';


$transaction_from_pos = json_decode($json_string, true );
$unregistered_drugs = verify_drugs($transaction_from_pos);

var_dump($transaction_from_pos);
var_dump($unregistered_drugs);

*/

$item['generic_name'] = arrange_generic_name("Sodium Ascorbate, aa");
$item['brand'] = "Immunpro"; // from pos
$item['dose'] = (int)500; // from pos
$item['unit'] = "mg"; // from pos

$generic_name = $item['generic_name'];
$brand = $item['brand'];
$dose = $item['dose'];
$unit = $item['unit'];
$is_otc = 1;
$max_monthly = 30000;
$max_weekly = 7000;

$generic_name = simplify_generic_name($generic_name);
$brand = strtolower($item['brand']);
$query_function2 = "SELECT * FROM `view_drugs` WHERE `generic_name` = '$generic_name' AND `brand` = '$brand' AND  `dose` = '$dose' AND `unit` =  '$unit';";
var_dump($query_function2);

$invalid_inputs = array();

$return = create_drug($generic_name, $brand, $dose, $unit, $is_otc, $max_monthly, $max_weekly);
if($return != "created"){
    // send msg to POS, msg
    $invalid_inputs["msg"] = "invalid_inputs";
    if(array_key_exists("inputs", $invalid_inputs)){
        $invalid_inputs["inputs"] .= $return;
    } else {
        $invalid_inputs["inputs"] = $return;
    }
} else {
    $invalid_inputs["msg"] = "inserted";
}

$invalid_inputs = json_encode($invalid_inputs);
?>
<script>
    console.log(<?php echo (isset($invalid_inputs)) ? $invalid_inputs: "0";?>);
</script>
<?php


$input_array = array("generic_name" => $generic_name,
"brand" => $brand,
"dose" => $dose,
"unit" => $unit,
"is_otc" => $is_otc,
"max_monthly" => $max_monthly,
"max_weekly" => $max_weekly);


var_dump($input_array);

