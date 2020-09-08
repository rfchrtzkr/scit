            </div>
        </div>
        <input type="hidden" id="bustype" value="<?php echo $_SESSION['business_type'];?>">
    </body>
</html>

<script>
    $(document).ready(function(){
        $("#read").click(function(){
            var input_nfc = $("#nfc_id").val();
            var business_type = $("#bustype").val();
            $.post("../frontend/read.php", {input_nfc: input_nfc, business_type:business_type }, function(d){
                if(d != "false"){
                    $('#body').load("../frontend/read.php", { input_nfc: input_nfc, business_type:business_type });
                } else {
                    alert(d);
                    $('#body').load("../frontend/home.php #home");
                }
            });
        });
        $("#transaction").click(function(){
            var input_nfc = $("#nfc_id").val();
            var business_type = $("#bustype").val();
            $.post("../frontend/transaction.php", {input_nfc: input_nfc, business_type:business_type }, function(d){
                if(d != "false"){
                    $('#body').load("../frontend/transaction.php", { input_nfc: input_nfc, business_type:business_type });
                } else {
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
    var monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December" ];

    function time() {
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
