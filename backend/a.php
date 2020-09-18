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
