<div>



</div> <!-- codes simulation ng logic/variables/functions -->

<br><br>

<div>
<?php

include('conn.php');


$query1 = "SELECT company_name,	branch FROM `view_companies`";
$result1 = $mysqli->query($query1);

$emparray = [];
while($row =mysqli_fetch_assoc($result1))
{
    $emparray[] = $row;
}

echo json_encode($emparray);




$a = true;
$b = false;
if($a) { echo "hello1<br>";}
if($b == "true") { echo "hello2<br>";}
    include('../backend/session.php');
    include('../backend/conn_osca.php');
    
    $selected_id = $_SESSION['osca_id'];
    $business_type = $_SESSION['business_type'];
    $company_tin = $_SESSION['company_tin'];
include("php_functions.php");
include("new_transaction.php");

var_dump($transaction);
echo json_encode($transaction);

$query_trans = "CALL `add_transaction`('".$transaction['trans_date']."', '$company_tin', '$selected_id', '".$transaction['clerk']."', @`msg`);";

echo $query_trans;

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
echo "<BR><BR><HR><BR>";
var_dump(read_qr_code("8fok93kd2u09j8dk"));
