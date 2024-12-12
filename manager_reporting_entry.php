<?php
require("dbconnect.php");
include('functions.php');
include "validation.php";

session_start();
if (!isset($_SESSION['ldap'])) {
    header("Location:index.php");
    exit();
}

$page = "team_calendar";
$userldap = $_SESSION['ldap'];
$editflagno = 0;

// echo "<script>console.log('hello',$userldap)</script>";

$date = date("Y-m-d");
if (isset($_REQUEST['user'])) {
    $userldap = mysqli_real_escape_string($link, $_REQUEST['user']);
    if ($_REQUEST['user'] != $_SESSION['ldap']) {
        $editflagno = 1;
    }
}
// echo "<script>console.log('hello2',$userldap)</script>";


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

if (isset($_REQUEST["drentry"])) {
    $dateentry = check_date(mysqli_real_escape_string($link, $_REQUEST['datepicker1']));
    $tasks = array();
    $hours = array();
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
                $recordset = mysqli_query($link, "insert into `daily_reporting_data` (`ldap`,`date`,`taskid`,`taskdesc`,`hours`) values ('" . $_SESSION['ldap'] . "','" . $dateentry . "','" . $i . "','" . ($taskdescription) . "','" . $taskhours . "')");
            }
        }
        $i++;
    }
    header("location:manager_reporting_entry.php?datepicker=$dateentry");
}

$totalhours = 0;
$existing_query = mysqli_query($link, "select * from daily_reporting_data where date='" . $date . "' and ldap='" . $userldap . "' order by id asc");
if (mysqli_num_rows($existing_query) > 0) {
    $totalhourrow = mysqli_fetch_array(mysqli_query($link, "select sum(hours) as t,timestamp from daily_reporting_data where date='" . $date . "' and ldap='" . $userldap . "'"));
}

?>

<?php include('header.php'); ?>

<div id="main">
    <div class="midContent_about">
        <div id="midContent_bottom" style=" height:auto;">
            <div id="about_left">
                <div class="about_tab">Logged in as <?php echo getName($_SESSION["ldap"]); ?></div>
                <div id="main-menu">
                    <?php include('menu.php'); ?>
                </div>
            </div>

            <div id="about_right">
                <div style="padding-top:20px;">
                    <table width="80%" align="center" border="0">
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
                    </table>

                    <form method="post" name="submitentry">
                        <input type="hidden" name="datepicker1" id="datepicker1" value="<?php echo $date; ?>" />
                        <table width="80%" cellspacing="0" align="center" border="0" class="manage" id="dailyentry">
                            <tr class="bluestrip" height="30">
                                <th width="15%">Sr. No.</th>
                                <th width="70%">Task Details</th>
                                <th width="5%">Minutes</th>
                                <th width="10%">Score</th>
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
                                    </td>
                                    <td align="center" valign="top">
                                        <input <?php if ($editflagno == 1 || $enableflag == 1) { ?> readonly="readonly" <?php } ?> type="text" name="hours[]" value="<?php echo $taskarray[$sr1]["hours"]; ?>" size="5" maxlength="4" onKeyPress="return validData_new(event,'hours');" />
                                    </td>

                                    <td align="center" valign="top">
                                        <?php if (trim($taskarray[$sr1]["taskdesc"]) != "") { ?>
                                            <input type="radio" name="score_<?php echo $sr1; ?>" value="Yes" id="thumbs_up_<?php echo $sr1; ?>"
                                                <?php
                                                $score_query = mysqli_query($link, "SELECT score FROM daily_reporting_data WHERE ldap='" . $userldap . "' AND `date`='" . $date . "' AND taskid='" . ($sr1 + 1) . "'");
                                                $score_data = mysqli_fetch_array($score_query);
                                                if ($score_data['score'] == 'Yes') echo 'checked';
                                                ?>
                                                onchange="updateScore(<?php echo $sr1; ?>, 'Yes')" />
                                            <label for="thumbs_up_<?php echo $sr1; ?>" style="cursor:pointer;">👍</label>

                                            <input type="radio" name="score_<?php echo $sr1; ?>" value="No" id="thumbs_down_<?php echo $sr1; ?>"
                                                <?php if ($score_data['score'] == 'No') echo 'checked'; ?>
                                                onchange="updateScore(<?php echo $sr1; ?>, 'No')" />
                                            <label for="thumbs_down_<?php echo $sr1; ?>" style="cursor:pointer;">👎</label>
                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php
                                $sr1++;
                            }
                            ?>

                            <tr>
                                <td colspan="2" align="center">
                                    <input <?php if ($editflagno == 1 || $enableflag == 1) { ?> disabled="disabled" <?php } ?> type="submit" name="drentry" value="<?php echo $label; ?>" />
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

<!-- JavaScript to handle the score update -->
<script>
    function updateScore(taskId, score) {
        var date = '<?php echo $date; ?>'; // Get the current date
        var taskid = taskId + 1; // Task id starts from 1
        var scoreValue = score; // The score value (Yes or No)
        var managerLdap = '<?php echo $userldap; ?>';
        // Send AJAX request to update the score
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'manager_reporting_entry.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4 && xhr.status == 200) {
                console.log('Score updated successfully');
            }
        };
        xhr.send('action=update_score&date=' + date + '&taskid=' + taskid + '&score=' + scoreValue);
    }
</script>

<?php
// if (isset($_POST['action']) && $_POST['action'] == 'update_score') {
//     $taskid = $_POST['taskid'];
//     $score = $_POST['score'];
//     $date = $_POST['date'];

//     // Update the score in the database
//     $update_query = "UPDATE daily_reporting_data SET score = '$score' WHERE ldap = '" . $_SESSION['ldap'] . "' AND date = '$date' AND taskid = '$taskid'";
//     mysqli_query($link, $update_query);
// }

if (isset($_POST['action']) && $_POST['action'] == 'update_score') {
    $taskid = $_POST['taskid'];
    $score = $_POST['score'];
    $date = $_POST['date'];
    // $manager_ldap = $_POST['manager_ldap']; // Manager LDAP passed via AJAX

    // Fetch the row ID using the combination of date and taskid (since it's unique for each record)
    $task_query = mysqli_query($link, "SELECT id FROM daily_reporting_data WHERE date = '$date' AND taskid = '$taskid'");
    $task = mysqli_fetch_array($task_query);

    if ($task) {
        $task_id = $task['id'];

        // Update the score and set the manager_ldap value
        $update_query = "UPDATE daily_reporting_data SET score = '$score', manager_ldap = '" . $_SESSION['ldap'] . "', score_timestamp = NOW() WHERE id = '$task_id'";
        mysqli_query($link, $update_query);

        echo 'Score updated successfully';
    } else {
        echo 'Task not found';
    }
}



?>