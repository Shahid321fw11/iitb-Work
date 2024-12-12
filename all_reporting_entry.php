<?php
//error_reporting(0);
require("dbconnect.php");
include('functions.php');
include "validation.php";

session_start();
if (!isset($_SESSION['ldap'])) {
    header("Location:index.php");
    exit();
}

$page = "";
if (isset($_REQUEST['frompage'])) {
    if ($_REQUEST['frompage'] === "cal") {
        $page = "all_calendar";
    } elseif ($_REQUEST['frompage'] === "detail") {
        $page = "all_details";
    }
}

$userldap = $_SESSION['ldap'];
$editflagno = 0;

$date = date("Y-m-d");
if (isset($_REQUEST['user'])) {
    $userldap = mysqli_real_escape_string($link, $_REQUEST['user']);
    if ($_REQUEST['user'] != $_SESSION['ldap']) {
        $editflagno = 1;
    }
}

if (isset($_REQUEST['datepicker'])) {
    $date = check_date(mysqli_real_escape_string($link, $_REQUEST['datepicker']));
}

$curr_dt_time = date("Y-m-d");
$validsql = mysqli_query($link, "select * from daily_reporting_data where ldap='" . $_SESSION['ldap'] . "' and `date`='" . date("Y-m-d", strtotime($date)) . "'");

if (mysqli_num_rows($validsql) > 0) {
    $finalenddt = strtotime("$date + 48 hours");
} else {
    $finalenddt = strtotime("$date + 1 week");
}

if (strtotime($curr_dt_time) < $finalenddt) {
    $enableflag = 0;
} else {
    $enableflag = 1;
}

$predatesql = mysqli_query($link, "select distinct(date) from daily_reporting_data where ldap='" . $_SESSION['ldap'] . "'");
$predates = array();
while ($fdata = mysqli_fetch_array($predatesql)) {
    $predates[] = $fdata["date"];
}


?>

<?php include('header.php'); ?>

<?php

if (isset($_REQUEST["drentry"])) {


    $dateentry = check_date(mysqli_real_escape_string($link, $_REQUEST['datepicker1']));
    $tasks = array();
    $hours = array();
    $minutes = array();


    $tasks = array_map(array($link, 'real_escape_string'), $_REQUEST["task"]);
    $hours = array_map(array($link, 'real_escape_string'), $_REQUEST["hours"]);



    $i = 1;
    for ($counter = 0; $counter < count($tasks); $counter++) {

        $taskdescription = $tasks[$counter];
        $taskhours = $hours[$counter];

        if (trim($tasks[$counter]) != "") {


            $existing_task = mysqli_query($link, "select * from `daily_reporting_data` where `date`='" . $dateentry . "' and `ldap`='" . $_SESSION['ldap'] . "' and `taskid`='$i'");
            if (mysqli_num_rows($existing_task) > 0) {
                mysqli_query($link, "update `daily_reporting_data` set `taskdesc`='" . ($taskdescription) . "',`hours`='" . $taskhours . "' where `ldap`='" . $_SESSION['ldap'] . "' and `date`='$dateentry' and `taskid`='$i'");
            } else {
                // $recordset = mysqli_query($link, "insert into `daily_reporting_data` (`ldap`,`date`,`taskid`,`taskdesc`,`hours`) values ('" . $_SESSION['ldap'] . "','" . $dateentry . "','" . $i . "','" . ($taskdescription) . "','" . $taskhours . "')");
                // Insert query updated to include default values for new columns
                mysqli_query($link, "insert into `daily_reporting_data` (`ldap`,`date`,`taskid`,`taskdesc`,`hours`,`manager_ldap`,`score`,`score_timestamp`) 
                values ('" . $_SESSION['ldap'] . "','" . $dateentry . "','" . $i . "','" . ($taskdescription) . "','" . $taskhours . "', NULL, '', NULL)");
            }
        }


        $i++;
    }
    header("location:daily_reporting_entry.php?datepicker=$dateentry");
}

?>



<?php
$totalhours = 0;
$existing_query = mysqli_query($link, "select * from daily_reporting_data where date='" . $date . "' and ldap='" . $userldap . "' order by id asc");
if (mysqli_num_rows($existing_query) > 0) {

    $totalhourrow = mysqli_fetch_array(mysqli_query($link, "select sum(hours) as t,timestamp from daily_reporting_data where date='" . $date . "' and ldap='" . $userldap . "'"));
}

?>


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

                    <table width="80%" align="center" border="0">
                        <tr>
                            <td colspan="3" align="center">

                            </td>
                        </tr>

                        <tr>
                            <td>
                                <b>Date:</b> <?php echo date("d-m-Y", strtotime($date)); ?>
                            </td>
                            <td id="totalhours">
                                <b>Total Hours:</b> <?php if ($totalhourrow["t"] != "") {
                                                        echo getHoursMinutes($totalhourrow["t"]);
                                                    } ?>
                            </td>
                            <td align="center">
                                <form name="submitdate" method="post" id="submitdate">
                                    <?php if ($editflagno == 0) { ?>
                                        <input placeholder="Select Date" type="text" name="datepicker" id="datepicker" onChange="submitform();" />
                                    <?php } else { ?>
                                        <input type="text" value="<?php echo date("d-m-Y", strtotime($date)); ?>" readonly="readonly" />
                                    <?php } ?>
                                </form>
                            </td>
                        </tr>

                        <tr>
                            <td><?php if ($editflagno == 1) { ?><b>Updated by:</b> <?php echo getName($userldap); ?> <?php } ?></td>
                            <td><?php if ($editflagno == 1) { ?><b>Updated on:</b> <?php echo date("d-m-Y h:i", strtotime($totalhourrow["timestamp"])); ?> <?php } ?></td>
                            <td align="center">
                                <?php if ($_REQUEST['frompage'] == "detail") { ?>
                                    <a style="color:#00F; font-weight:bold;" href="daily_reporting_details.php?searchuser=true&user=<?php echo $userldap; ?>&from=<?php echo $_REQUEST['from']; ?>&to=<?php echo $_REQUEST['to']; ?>"> Back </a>
                                <?php } else if ($_REQUEST['frompage'] == "cal") { ?>
                                    <a style="color:#00F; font-weight:bold;" href="daily_reporting_calendar.php?searchuser=true&user=<?php echo $userldap; ?>&from=<?php echo $_REQUEST['from']; ?>&to=<?php echo $_REQUEST['to']; ?>"> Back </a>
                                <?php } ?>
                            </td>
                        </tr>

                    </table>

                    <br /><br />

                    <form method="post" name="submitentry">
                        <input type="hidden" name="datepicker1" id="datepicker1" value="<?php echo $date; ?>" />
                        <table width="80%" cellspacing="0" align="center" border="0" class="manage" id="dailyentry">
                            <tr class="bluestrip" height="30">
                                <th width="15%">
                                    Sr. No.
                                </th>
                                <th width="70%">
                                    Task Details
                                </th>
                                <th width="15%">
                                    Minutes
                                </th>
                            </tr>

                            <?php
                            $label = "Add";
                            if (mysqli_num_rows($existing_query) > 0) {
                                $label = "Update";
                                $sr = 0;
                                $taskarray = array();
                                while ($record = mysqli_fetch_array($existing_query)) {
                                    $taskarray[$sr]["taskdesc"] = $record["taskdesc"];
                                    $taskarray[$sr]["hours"] = $record["hours"];
                                    $sr++;
                                }
                            }

                            ?>
                            <?php

                            $sr1 = 0;
                            while ($sr1 < 10) {
                            ?>
                                <tr>
                                    <td align="center" valign="top"><?php echo ($sr1 + 1); ?></td>
                                    <td align="center" valign="top">
                                        <textarea <?php if ($editflagno == 1 || $enableflag == 1) { ?> readonly="readonly" <?php } ?> name="task[]" rows="5" cols="70" onBlur="showHours();"><?php echo $taskarray[$sr1]["taskdesc"]; ?></textarea>
                                        <div style="color:#F00;" id="alt_tasks_<?php echo $sr1; ?>"></div>
                                    </td>
                                    <td align="center" valign="top">
                                        <input <?php if ($editflagno == 1 || $enableflag == 1) { ?> readonly="readonly" <?php } ?> type="text" name="hours[]" value="<?php echo $taskarray[$sr1]["hours"]; ?>" onblur="showHours();" size="5" maxlength="4" onKeyPress="return validData_new(event,'hours');" />
                                        <div style="color:#F00;" id="alt_hours_<?php echo $sr1; ?>"></div>
                                    </td>
                                </tr>
                            <?php
                                $sr1++;
                            }

                            ?>


                            <tr>
                                <td colspan="2" align="center">
                                    <input <?php if ($editflagno == 1 || $enableflag == 1) { ?> disabled="disabled" <?php } ?> type="submit" name="drentry" value="<?php echo $label; ?>" onClick="return validate_confirm();" />
                                </td>
                                <td id="totalhours1"></td>
                            </tr>
                        </table>
                    </form>



                    <div style="height:20px;"></div>
                </div>

            </div>
        </div>

        <div style="clear:both;"></div>


    </div>
</div>
<?php include('footer.php'); ?>