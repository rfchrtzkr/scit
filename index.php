<?php
    include('backend/login.php');

    if(isset($_SESSION['login_user'])){
        header("location: frontend/home.php");
    }
?>


<!DOCTYPE html>
<html>
    <head>
        <title>OSCA - Home</title>
        <link rel="stylesheet" type="text/css" href="css/login.css">
        <link rel="icon" href="resources/images/OSCA_square.png">
    </head>
    <body>
        <div class="box">
            <img src="resources/images/OSCA_square_with_text.png" class="icon" alt="User Icon">
            <form method="post" enctype="multipart/form-data" autocomplete="off" id="">
                <input type="text" name="username" placeholder="<?php /*shell_exec("hostname -I");*/ echo 'Username' ?>" required="">
                <input type="password" name="password" placeholder="Pasword"  required="">
                <p class ="message"><?php echo $error; ?></p>
                <p><a href="frontend/forgot_password.php">Forgot Password</a></p>
                <input type="submit" name="submit" value=" Login ">
            </form>
        </div>
    </body>
</html>
