<?php
    include('head.php');
    
    $logo =  "../resources/logo/".$_SESSION["logo"]; 

    if (true) {
        $logo =  "../resources/logo/".$_SESSION["logo"]; 
    } else {
        $logo = "../resources/images/unknown_m_f.png";
    }
?>

    <div class="col col-sm-10 contents" id="body">
        <div id="home">
            <div class="logo">
                <img src="<?php echo $logo; ?>" alt="<?php echo $_SESSION['company_name'];?>" class="logo" >
            </div>
            <div class="establishment-details mb-5">
                <h2 class="mt-5 text-center"><?php echo $_SESSION['company_name'];?></h2>
                <h4 class="mt-3 text-center"><?php echo $_SESSION['branch'];?></h4>
            </div>
            <div class="mt-5 pt-5position-static text-center" style="padding-top: 100px;">
                <img src="/scit/resources/images/OSCA_square.png" class="logo mx-auto" style="width: 150px; height: 150px;">
                <p>Office of the Senior Citizens Affairs</p>
                </div>
            </div>
        </div>
    </div>
<?php
    include('foot.php');
?>

