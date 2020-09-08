<?php
  include('../backend/session.php');
?>


<!DOCTYPE html>
<html>
    <head>
        <title>OSCA - Home</title>
        <link rel="icon" href="../resources/images/OSCA_square.png">


        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
        <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-T8Gy5hrqNKT+hzMclPo118YTQO6cYprQmhrYwIiQ/3axmI1hQomh7Ud2hPOy8SP1" crossorigin="anonymous">
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>


        <link rel="stylesheet" type="text/css" href="../css/scit.css">
        <link rel="icon" href="resources/images/OSCA_square.png">
    </head>
    <body>  
      <a href="home.php"><button type="button">home</button></a>
      <button type="button" id="read">read</button>
      <button type="button" id="transaction">transaction</button>
      <a href="../backend/logout.php"><button type="button" id="logout">logout</button></a>
      <input type="text" id="nfc_id" name="nfc_id">


        <div class="container">
          <div class="scit">
          <div class="date-time">
            <h5>
                <span class="date" id="clock_tick"></span>
                <span class="time" id="date_tick"></span>
            </h5>
          </div>
