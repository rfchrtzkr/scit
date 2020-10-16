        <?php include_once("../backend/session.php"); ?>
                <div id="qr_content"></div>
            </div>
        </div>
        <div id="response"></div>
    </body>
</html>

<script>
    var idle_interval = 5 * 1000;
    function reload_home(){
        $('#body').load("../frontend/home.php #home");
    }
    
    $(document).ready(function(){
        $("body").on('click', "#exit", function () {
            reload_home();
        });
        
        $("body").on('click', "#new_trans", function () {
            $.post("../backend/read_serial.php", function(read_serial_response){
                //alert(read_serial_response);
                if(read_serial_response.trim() != "false"){
                    $('#body').load("../frontend/transaction.php", function(d){
                        if(d.trim() == "false"){
                            reload_home();
                        }
                    });
                } else {
                    alert("No transaction received");
                }
            });
        });
        
        $("body").on('click', "#nfc_read", function () {
            $.post("../backend/read_nfc.php", function(read_nfc_response){
                //alert(read_nfc_response);
                if(read_nfc_response.trim() != "false"){
                    $('#body').load("../frontend/read.php", { input_nfc: read_nfc_response.trim()}, function(d){
                        //alert(d);
                        if(d.trim() == "false"){
                            $('#databody').load("../frontend/home.php #home");
                        } else if (d.trim() == "inactive"){
                            MsgBox_Invalid("Member's tag is inactive!", "Invalid INFC");
                            $('#body').load("../frontend/home.php #home");
                        }
                        //setTimeout(function() { reload_home(); }, idle_interval);
                    });
                }
            });
        });
        
        $("body").on('click', "#qr_read", function () {
            $.post("../backend/read_qr.php", function(read_qr_response){
                if(read_qr_response.trim() != "false"){
                    $('#qr_content').load("../frontend/read_qr.php", { qr_code: read_qr_response.trim()}, function(d){
                        if(d.trim() == "invalid"){
                            MsgBox_Invalid("The QR does not exist.", "Invalid QR Code");
                            $('#body').load("../frontend/home.php #home");
                        } else if(d.trim() == "expired"){
                            MsgBox_Invalid("This request has already expired. Please request a new QR code.", "QR Code Expired");
                            $('#body').load("../frontend/home.php #home");
                        } else if (d.trim() == "used"){
                            MsgBox_Invalid("This request has already been used.", "QR Code Used");
                            $('#body').load("../frontend/home.php #home");
                        }/* else {
                            MsgBox_Invalid("There is something wrong with your request.", "QR Code Invalid");
                            $('#body').load("../frontend/home.php #home");
                        }*/
                    });
                }
            });
        });
        
        $("body").on('click', "#cardless", function () {
            $.post("../backend/read_cardless.php", function(cardless_response){
                //alert(cardless_response);
                cardless = cardless_response.trim();
                if(cardless == "no_received"){
                    MsgBox_Invalid("No data received from POS!", "No data received");
                    $('#body').load("../frontend/home.php #home");
                } else if(cardless == "invalid_details"){
                    MsgBox_Invalid("Senior Citizen's data does not have a match in our record!", "No data fouund");
                    $('#body').load("../frontend/home.php #home");
                } else if(cardless != "invalid_details"){
                    $('#body').load("../frontend/read.php?cardless=true", { input_nfc: cardless}, function(d){
                        //alert(d);
                        if(d.trim() == "false"){
                            $('#body').load("../frontend/home.php #home");
                        } else if (d.trim() == "inactive"){
                            MsgBox_Invalid("Member's tag is inactive!", "Invalid INFC");
                            $('#body').load("../frontend/home.php #home");
                        }
                        //setTimeout(function() { reload_home(); }, idle_interval);
                    });
                } else {
                    MsgBox_Invalid("invalid!", "Invalid INFC");
                }
            });
        });





        $("#read").click(function(){
            var input_nfc = $("#nfc_id").val();
            $('#body').load("../frontend/read.php", { input_nfc: input_nfc}, function(d){
                if(d.trim() == "false"){
                    $('#body').load("../frontend/home.php #home");
                } else if (d.trim() == "inactive"){
                    MsgBox_Invalid("Member's tag is inactive!", "Invalid INFC");
                    $('#body').load("../frontend/home.php #home");
                }
                //setTimeout(function() { reload_home(); }, idle_interval);
            });
        });

        /*
        $("#sendtoJSON").click(function(){
            < ?php $myJSON = json_encode($_SESSION); ?>
            console.log(< ?php echo $myJSON; ?>);
        });
        */

        $("#read_qr").click(function(){
            var input_nfc = $("#nfc_id").val();
            $('#qr_content').load("../frontend/read_qr.php", { qr_code: input_nfc}, function(d){
                //alert(d);
                if(d.trim() == "invalid"){
                    MsgBox_Invalid("The QR does not exist.", "Invalid QR Code");
                    $('#body').load("../frontend/home.php #home");
                } else if(d.trim() == "expired"){
                    MsgBox_Invalid("This request has already expired. Please request a new QR code.", "QR Code Expired");
                    $('#body').load("../frontend/home.php #home");
                } else if (d.trim() == "used"){
                    MsgBox_Invalid("This request has already been used.", "QR Code Used");
                    $('#body').load("../frontend/home.php #home");
                } /*else {
                    MsgBox_Invalid("There is something wrong with your request.", "QR Code Invalid");
                    $('#body').load("../frontend/home.php #home");
                }*/
            });
        });

        $("#transaction").click(function(){
            $('#body').load("../frontend/transaction.php", function(d){
                if(d.trim() == "false"){
                    $('#body').load("../frontend/home.php #home");
                }
            });
        });

        $("#serial_read").click(function(){
            $('#response').load("../backend/read_serial.php", function(read_serial_response){
                alert(read_serial_response);
                if(read_serial_response.trim() != "false"){
                    $('#body').load("../frontend/transaction.php", function(d){
                        if(d.trim() == "false"){
                            $('#body').load("../frontend/home.php #home");
                        }
                    });
                }
            });
        });
        
    });
    function MsgBox_Invalid(message, title) {
        $('<div></div>').appendTo('body')
            .html('<div><h6>' + message + '</h6></div>')
            .dialog({
                modal: true,
                title: title,
                zIndex: 10000,
                autoOpen: true,
                width: '300px',
                resizable: false,
                buttons: {
                    OK: function() {
                        reload_home();
                        $(this).remove();
                    }
                },
                close: function(event, ui) {
                    reload_home();
                    $(this).remove();
                }
            });
    };
</script>

    
<script type="text/javascript">
    var clockElement = document.getElementById('clock');
    var span_clock = document.getElementById('clock_tick');
    var span_date = document.getElementById('date_tick');
    var monthNames = ["January", "February", "March", "April", 
                        "May", "June", "July", "August", "September", 
                        "October", "November", "December"];
    
    function time()
    {
        var d = new Date();
        var ttt = new Date().toLocaleTimeString().replace(/([\d]+:[\d]{2})(:[\d]{2})(.*)/, "$1$3");
        span_clock.textContent = ttt;
        var month_ = monthNames[d.getMonth()];
        var day_ = d.getDate();
        var year_ = d.getFullYear();
        span_date.textContent = day_ + " " + month_ + " " + year_;
    }
    setInterval(time, 1000);
</script>
