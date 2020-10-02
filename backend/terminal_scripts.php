<?php

$serial_location = "/var/www/html/rpiserial";
$command = "python"; //$command = "sudo python";


// invoked in frontend/read.php[93], transaction_history[208]
function serial_read()
{
    $json_string = shell_exec("$command $serial_location/serial_wr.py");
    return $json_string;
}

// invoked in BE/read.php[52]
function senior_isValid($senior_isValid = false)
{
    if($senior_isValid){
        shell_exec("$command $serial_location/serial_valid_senior.py");
    } else {
        shell_exec("$command $serial_location/serial_invalid_senior.py");
    }
}

// invoked in BE/create_transaction.php[36]
function serial_w_success()
{
    shell_exec("$command $serial_location/serial_w_success.py");
}

// invoked in FE/transaction.php[36]
function serial_invalid_drug()
{
    shell_exec("$command $serial_location/serial_invalid_drug.py");
}

// invoked in FE/transaction.php[310]
function serial_invalid_dosage()
{
    shell_exec("$command $serial_location/serial_invalid_dosage.py");
}

// write JSON variables to JSON file to be forwarded in Serial port


// invoked in BE/new_transaction.php[154]
function write_invalid_drug($data = "")
{
    $json_file = fopen("$serial_location/serial_invalid_drug.json", "w") or die("Unable to open file!");
    fwrite($json_file, $data);
    fclose($json_file);
}


/*
function write_invalid_dosage($data = "")
{
    $json_file = fopen("$serial_location/serial_invalid_dosage.json", "w") or die("Unable to open file!");
    fwrite($json_file, $data);
    fclose($json_file);
}
*/