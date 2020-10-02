        
                <div id="qr_content"></div>
            </div>
        </div>
    </body>
</html>

<script>
    var idle_interval = 5 * 1000;
    function reload_home(){
        $('#body').load("../frontend/home.php #home");
    }
    
    $(document).ready(function(){
        $("#read").click(function(){
            var input_nfc = $("#nfc_id").val();
            $('#body').load("../frontend/read.php", { input_nfc: input_nfc}, function(d){
                if(d.trim() == "false"){
                    $('#body').load("../frontend/home.php #home");
                }
                //setTimeout(function() { reload_home(); }, idle_interval);
            });
        });
        $("#read_qr").click(function(){
            var input_nfc = $("#nfc_id").val();
            $('#qr_content').load("../frontend/read_qr.php", { qr_code: input_nfc}, function(d){
                /*
                if(d == "false"){
                    $('#body').load("../frontend/home.php #home");
                }*/
                //setTimeout(function() { modal.style.display = "none"; }, idle_interval);
            });
        });
        $("#transaction").click(function(){
            var input_nfc = $("#nfc_id").val();
            var business_type = $("#bustype").val();
            $('#body').load("../frontend/transaction.php", function(d){
                if(d.trim() == "false"){
                    $('#body').load("../frontend/home.php #home");
                }
            });
        });
    });
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
