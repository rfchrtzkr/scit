<?php
    include('../backend/conn.php');
    include_once('../backend/session.php');
    include_once('../backend/php_functions.php');
    include_once('../backend/terminal_scripts.php');
    if(isset($_SESSION['osca_id']))
    {
        $selected_id = $_SESSION['osca_id'];
        $business_type = $_SESSION['business_type'];
        $company_tin = $_SESSION['company_tin'];
        $show_drugs_button = "";

        $query = "SELECT `g_id`, `g_first_name`, `g_middle_name`, `g_last_name`, `g_sex`, `g_contact_number`, `g_email`, `g_relationship`
                FROM `view_members_with_guardian`
                WHERE `osca_id` = '$selected_id'
                ORDER BY `g_id`;";
        $result = $mysqli->query($query);
        $row_count = mysqli_num_rows($result);
        ?>
            <div class="trans-title">
                GUARDIANS LIST
            </div>
            <div class="guardian-list scrollbar-black">
        <?php
        
        if($row_count != 0)
        {
            $counter = 0;
            $result = $mysqli->query($query);
            while($row = mysqli_fetch_array($result))
            {
                $counter++;
                $g_id = $row['g_id'];
                $g_firstmid_name = ucwords($row['g_first_name'] . " " . $row['g_middle_name'] );
                $g_last_name = strtoupper($row['g_last_name']);
                $g_contact_number = $row['g_contact_number'];
                $g_sex = $row['g_sex'];
                $g_email = $row['g_email'];
                $g_relationship = $row['g_relationship'];
                    ?>
                <div class="row _transaction-record collapse-header" data-toggle="collapse" data-target="#collapse_<?php echo $g_id?>" aria-expanded="false" aria-controls="collapse_<?php echo $g_id?>">
                    <div class="col col-12 d-md-block">
                        <b><?php echo $g_last_name;?>,</b>
                        <?php echo $g_firstmid_name;?>
                    </div>
                    <div id="collapse_<?php echo $g_id?>" class="col collapse" aria-labelledby="heading<?php echo $g_id?>">
                        <div class="col col-12">
                            Relationship: <?php echo $g_relationship ?>
                        </div>
                        <div class="col col-12">
                            Gender: <?php echo determine_sex($g_sex, "display_long"); ?>
                        </div>
                    </div>
                </div>
                <?php
            }
        } else {
            echo "<div class='col col-md-8 text-center mx-auto mt-5'>No guardians recorded yet for this user</div>";
        }
        mysqli_close($mysqli);
        ?>
        </div>
        <?php
    } else {
        echo "false";
    }

?>