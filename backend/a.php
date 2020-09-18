<div>
include("php_functions.php");
</div>
<div>   
echo simplify_generic_name("aa,  hh, cc    cccc, bb, dd, gggg, bBb, hhhh");
</div>
<br><br>
<div>
<?php
include("php_functions.php");

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


$unregistered_drugs = [];
foreach($transaction_from_pos as $row => $item_from_pos){
    if(isset($item_from_pos['generic_name'])){
        $item = [];
        $item['generic_name'] = simplify_generic_name($item_from_pos['generic_name']);

        echo $item_from_pos['generic_name'] . "<br><hr>";
        echo simplify_generic_name($item_from_pos['generic_name']);

        $item['brand'] = strtolower($item_from_pos['brand']); // from pos
        $item['dose'] = (int)$item_from_pos['dose']; // from pos
        $item['unit'] = $item_from_pos['unit']; // from pos
        $generic_name = $item['generic_name'];
        $brand = $item['brand'];
        $dose = $item['dose'];
        $unit = $item['unit'];
        $drug_id = validate_drug($generic_name, $brand, $dose, $unit);
        if($drug_id == 0) {
            $unregistered_drugs[] = $item;
        }
    }
}