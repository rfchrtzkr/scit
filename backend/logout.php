<?php
  session_start();
  $logs = fopen("../resources/text/actions.txt", "w") or die("Unable to open file!");
  fwrite($logs, "");
  fclose($logs);
  if(session_destroy()) // Destroying All Sessions
    {
    header("Location: ../index.php"); // Redirecting To Home Page
    }
?>
