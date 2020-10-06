<?php


// invoked in BE/read_qr.php[4]
function qr_read()
{
    $serial_location = "/var/www/html/qrscan";
    $command = "sudo python3"; //$command = "sudo python";
    $data = shell_exec("$command $serial_location/qrscan.py");
    return $data;
}

// invoked in BE/read_nfc.php[4]
function nfc_read()
{
    $serial_location = "/var/www/html/nfcread";
    $command = "sudo python"; //$command = "sudo python";
    $data = shell_exec("$command $serial_location/nfcread.py");
    return $data;
}

// invoked in BE/create_drugs.php[24]
function serial_read_nowait()
{
    $serial_location = "/var/www/html/rpiserial";
    $command = "python"; //$command = "sudo python";
    $json_string = shell_exec("$command $serial_location/serial_read_nowait.py");
    return $json_string;
}

// invoked in BE/read_serial.php[4]
function serial_read()
{
    $serial_location = "/var/www/html/rpiserial";
    $command = "python"; //$command = "sudo python";
    $json_string = shell_exec("$command $serial_location/serial_read.py");
    return $json_string;
}

// invoked in BE/read.php[52]
function senior_isValid($senior_isValid = false)
{
    $serial_location = "/var/www/html/rpiserial";
    $command = "python"; //$command = "sudo python";
    if($senior_isValid){
        $location = "$command $serial_location/serial_valid_senior.py";
        shell_exec($location);
    } else {
        $location = "$command $serial_location/serial_invalid_senior.py";
        shell_exec($location);
    }
    return $location;
}

// invoked in BE/create_transaction.php[36]
function serial_w_success()
{
    $serial_location = "/var/www/html/rpiserial";
    $command = "python"; //$command = "sudo python";
    shell_exec("$command $serial_location/serial_w_success.py");
}

// invoked in FE/transaction.php[36]
function serial_invalid_drug()
{
    $serial_location = "/var/www/html/rpiserial";
    $command = "python"; //$command = "sudo python";
    shell_exec("$command $serial_location/serial_invalid_drug.py");
}

// invoked in FE/transaction.php[310]
function serial_invalid_dosage()
{
    $serial_location = "/var/www/html/rpiserial";
    $command = "python"; //$command = "sudo python";
    shell_exec("$command $serial_location/serial_invalid_dosage.py");
}

// write JSON variables to JSON file to be forwarded in Serial port


// invoked in BE/new_transaction.php[154]
function write_invalid_drug($data = "")
{
    $serial_location = "/var/www/html/rpiserial";
    $command = "python"; //$command = "sudo python";
    $json_file = fopen("$serial_location/serial_invalid_drug.json", "w") or die("Unable to open file!");
    //$json_file = fopen("../script/serial_invalid_drug.json", "w") or die("Unable to open file!");
    fwrite($json_file, $data);
    fclose($json_file);
}
