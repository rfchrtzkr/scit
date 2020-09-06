<?php
  include('conn.php');
  session_start();

  // Storing Session
  if(!isset($_SESSION['login_user'])){
    mysqli_close($mysqli);
    header('Location: ../index.php');
    exit();
  }

  $user_name=$_SESSION['login_user'];
  // SQL Query To Fetch Complete Information Of User
  $ses_sql = $mysqli->query("SELECT * FROM `view_companies` WHERE `user_name`='$user_name'");
  $row = mysqli_fetch_assoc($ses_sql);
  $_SESSION['business_type'] = $row['business_type'];

?>
