<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// error_reporting(0);
require("dbconnect.php");
include('functions.php');

session_start();
if (!isset($_SESSION['ldap'])) {
    header("Location:login_daily_r.php");
    exit();
}

// Check if the current user is a manager
$qry = "SELECT member_id FROM profile WHERE taken_clearance = 0 AND solid_line_reporting = " . $_SESSION['ldap'];
$worker_result = mysqli_query($link2, $qry);


if (mysqli_num_rows($worker_result) <= 0) {
    $is_manager = false;
} else {
    $is_manager = true;
    $worker_ids = [];
    while ($worker = mysqli_fetch_array($worker_result)) {
        $worker_ids[] = $worker['member_id'];
    }
}

if ($is_manager) {
    $worker_ids_string = implode(",", $worker_ids);
    $sql = "SELECT a.ldap, a.date, SUM(a.hours) as t_hours 
            FROM `daily_reporting_data` as a 
            WHERE a.ldap IN ($worker_ids_string) 
            GROUP BY a.ldap, a.date";
} else {
    $sql = "";
}

$page = "team_calendar";
?>
<?php
$currentT = date("Y-m-d");
$data = array();
$rid = 1;
$lcolor = '';
$tcolor = '';
$newname = '';

// Check if $sql is not empty before executing the query
if (!empty($sql)) {
    $datasql = mysqli_query($link, $sql);

    if (!$datasql) {
        die("Error in query execution: " . mysqli_error($link));
    }

    while ($datar = mysqli_fetch_array($datasql)) {
        $newname = getName($datar["ldap"]);
        $data[] = array(
            "id" => $rid,
            "title" => $newname . " =>\n " . getHoursMinutes($datar["t_hours"]) . " hours",
            "description" => $datar["t_hours"] . " Minutes",
            "start" => $datar["date"],
            "end" => $datar["date"],
            "color" => $lcolor,
            "textColor" => $tcolor,
            "url" => "manager_reporting_entry.php?frompage=cal&user=" . $datar["ldap"] . "&datepicker=" . $datar["date"] . "",
        );
        $rid++;
    }
} else {
    $data = [];
}

?>

<?php include('header.php'); ?>

<div id="main">
    <div class="midContent_about">
        <div id="midContent_bottom" style="height:auto;">
            <div id="about_left">
                <div class="about_tab">Logged in as <?php echo getName($_SESSION["ldap"]); ?></div>
                <div id="main-menu">
                    <?php include('menu.php'); ?>
                </div>
            </div>
            <div id="about_right">
                <div style="padding-top:20px;">
                    <?php if (!$is_manager) { ?>
                        <p style="padding:20px; text-align:center; font-size:18px; color:red; font-weight:bold;">You are not a manager.</p>
                    <?php } else { ?>
                        <!-- <a href="download_details.php" style="text-align:right; text-decoration:none; vertical-align:middle; font-weight:bold;">
                            Download <img src="images/save.png" alt="save as csv" />
                        </a><br /> -->
                        <div id='calendar'></div>
                    <?php } ?>
                    <br /><br />
                    <div style="height:20px;"></div>
                </div>
            </div>
        </div>
        <div style="clear:both;"></div>
    </div>
</div>

<?php include('footer.php'); ?>