<?php
    include('head.php');
    
?>
    <div class="col col-sm-10 contents" id="body">
    
        <div id="home">
        
            <?php
                include('../backend/reset_member_session.php');
                
                $logo = "../resources/logo/".$_SESSION["logo"]; 

                if (true) {
                    $logo = "../resources/logo/".$_SESSION["logo"]; 
                } else {
                    $logo = "../resources/images/unknown_m_f.png";
                }
                
            ?>
            <div class="logo">
                <img src="<?php echo $logo; ?>" alt="<?php echo $_SESSION['company_name'];?>" class="logo" >
            </div>
            <div class="establishment-details">
                <h2 class="establishment-name"><?php echo $_SESSION['company_name'];?></h2>
                <h4 class="establishment-branch"><?php echo $_SESSION['branch'];?></h4>
            </div>
            <div class="home-buttons">
                <button type="button" class="btn btn-block btn-light btn-lg" id="nfc_read">Senior Tap</button>
                <button type="button" class="btn btn-block btn-light btn-lg" id="qr_read">QR Read</button>
                <button type="button" class="btn btn-block btn-light btn-lg" id="cardless">Cardless Transaction</button>
            </div>
            <div class="home-footer" id="logout">
                <img src="/scit/resources/images/OSCA_square.png" class="logo mx-auto" style="width: 150px; height: 150px;">
                <p>Office of the Senior Citizens Affairs</p>
                </div>
            </div>
            
        </div>
    </div>
<?php
    include('foot.php');
?>
<?php $myJSON = json_encode($_SESSION); ?>
<script> console.log(<?php echo $myJSON; ?>); </script>

