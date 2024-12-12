<?php
error_reporting(0);
require("dbconnect.php");
include('functions.php');

session_start();
if (!isset($_SESSION['ldap'])) {
    header("Location:login_daily_r.php");
    exit();
}

// changes done by shahid ansari on 4 december 2024
// if ($_SESSION['ldap'] == "7" || $_SESSION['ldap'] == "1") { // this change from Rajni mam on 4th December 2024
// if ($_SESSION['ldap'] == "888" || $_SESSION['ldap'] == "1") {
// 	$sql = "SELECT a.ldap,a.date,sum(a.hours) as t_hours FROM `daily_reporting_data` as a  group by a.ldap,a.date";
// } else {
// 	$sql = "SELECT a.ldap,a.date,sum(a.hours) as t_hours FROM `daily_reporting_data` as a   where a.ldap='" . $_SESSION['ldap'] . "' group by a.ldap,a.date";
// }

$sql = "SELECT a.ldap,a.date,sum(a.hours) as t_hours FROM `daily_reporting_data` as a   where a.ldap='" . $_SESSION['ldap'] . "' group by a.ldap,a.date";

$page = "calendar";

?>

<?php
$currentT = date("Y-m-d");
$data = array();
$rid = 1;
$lcolor = '';
$tcolor = '';
$newname = '';

$datasql = mysqli_query($link, $sql);

while ($datar = mysqli_fetch_array($datasql)) {
    $newname = getName($datar["ldap"]);
    $data[] = array(
        "id" => $rid,
        "title" => $newname . " =>\n " . getHoursMinutes($datar["t_hours"]) . " hours",
        "description" => $datar["t_hours"] . " Minutes",
        "start" => $datar["date"],
        "end" => $datar["date"],
        "color" => $lcolor,   // a non-ajax option
        "textColor" => $tcolor, // a non-ajax option	
        "url" => "my_reporting_entry.php?frompage=cal&user=" . $datar["ldap"] . "&datepicker=" . $datar["date"] . "",
    );

    $rid++;
}




?>

<?php include('header.php'); ?>


<div id="main">
    <div class="midContent_about">



        <div id="midContent_bottom" style=" height:auto;">
            <div id="about_left">
                <div class="about_tab">Logged in as <?php echo getName($_SESSION["ldap"]); ?>
                </div>
                <div id="main-menu">


                    <?php include('menu.php'); ?>

                </div>

            </div>


            <div id="about_right">





                <div style="padding-top:20px;">



                    <a href="download_details.php" style="text-align:right; text-decoration:none; vertical-align:middle; font-weight:bold;">Download <img src="images/save.png" alt="save as csv" /></a><br />

                    <div id='calendar'></div>

                    <br /><br />



                    <div style="height:20px;"></div>
                </div>

            </div>
        </div>

        <div style="clear:both;"></div>


    </div>
</div>
<?php include('footer.php'); ?>