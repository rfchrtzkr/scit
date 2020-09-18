<?php
    include('backend/login.php');

    if (isset($_SESSION['login_user'])){
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
        <div class="container">
            <div class="content">
                <div class="first-column">
                    <img class="img-logo" src="resources/images/osca_logo_new.png" class="icon" alt="User Icon">
                    <h2 class="title title-primary">OFFICE FOR THE SENIOR CITIZEN AFFAIRS</h2>
                    <p class="description description-primary">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
                    <!-- <p class="description description-primary">please login with your personal info</p> -->
                </div>
                <div class="second-column">
                    <form class="form" method="post" enctype="multipart/form-data" autocomplete="off" id="">
                        <h2 class="title title-secondary">Sign into your Account</h2>
                        <label class="label-input" for="">
                            <i class="far fa-user icon-modify"></i>
                            <input type="text" name="username" placeholder="<?php /*shell_exec("hostname -I");*/ echo 'Username' ?>" required="">
                        </label>
                        <label class="label-input" for="">
                            <i class="fas fa-lock icon-modify"></i>
                            <input type="password" name="password" placeholder="Pasword" required="">
                        </label>
                        <p><a class="forgot-password" href="frontend/forgot_password_2.php">Forgot Password?</a></p>
                        <p class="message"><?php echo $error; ?></p>
                        <button class="btn btn-second" type="submit" name="submit" value=" Login ">Login</button>
                        <!-- <p class=footer>Copyright © 2020</p> -->
                    </form>
                </div>
            </div>
        </div>
    </body>
</html>