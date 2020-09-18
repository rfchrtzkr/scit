<?php
include('backend/conn.php');
    session_start();
    $error='';
    if (isset($_POST['submit'])) {
        if (empty($_POST['username']) || empty($_POST['password'])) {
        $error = "Username or Password must not be blank";
        } else {
            $username = strtolower($mysqli->escape_string($_POST['username']));
            $password = $mysqli->escape_string($_POST['password']);

            $query1 = "SELECT * FROM `view_companies` WHERE `user_name`='$username'";
            $query2 = "SELECT * FROM `view_companies` WHERE `user_name`='$username' AND `password`=MD5('$password')";
            $query3 = "SELECT * FROM `view_companies` WHERE `user_name`='$username' AND `password`=MD5('$password') AND `is_enabled`=1";
            $result1 = $mysqli->query($query1);
            $rows1 = mysqli_num_rows($result1);
            $result2 = $mysqli->query($query2);
            $rows2 = mysqli_num_rows($result2);
            $result3 = $mysqli->query($query3);
            $rows3 = mysqli_num_rows($result3);
            if ($rows1 == 1) {
                if($rows2 == 1) {
                    if($rows3 == 1) {
                        $_SESSION['login_user'] = $username;
                        $row = mysqli_fetch_array($result3);
                        $_SESSION['user_name'] = $row['user_name'];
                        $_SESSION['company_name'] = $row['company_name'];
                        $_SESSION['branch'] = $row['branch'];
                        $_SESSION['business_type'] = $row['business_type'];
                        $_SESSION['company_tin'] = $row['company_tin'];
                        $_SESSION['logo'] = $row['logo'];
                        header("location: ../frontend/home.php");
                    }
                    else {
                        $error = "User is deactivated";
                    }
                }
                else {
                    $query_invalidPW = "CALL `invalid_login`('".$username."')";
                    $result_invalidPW = $mysqli->query($query_invalidPW);
                    $error = "Invalid password";
                }
            }
            else {
                    $error = "Username does not exist";
            }
            mysqli_close($mysqli);
        }
    }
?>
