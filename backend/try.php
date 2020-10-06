<?php
$a = shell_exec("python /var/www/html/rpiserial/serial_read.py");
//$a
echo "raw string:<br>";
var_dump($a);
echo "<br>decoded json:<br>";
$b = json_decode($a, true);
var_dump($b);
?>
<br>
<br>
<?php
if($b != null || $b != ""){
	echo "data received";
	var_dump(json_decode($a, true));
} else {
	echo "blank";
}
