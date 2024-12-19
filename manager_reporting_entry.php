<?php
require("dbconnect.php");
include("functions.php");
include "validation.php";


session_start();
if (!isset($_SESSION['ldap'])) {
    header("Location:index.php");
    exit();
}

$page = "";
if (isset($_REQUEST['frompage'])) {
    if ($_REQUEST['frompage'] === "cal") {
        $page = "team_calendar";
    } elseif ($_REQUEST['frompage'] === "detail") {
        $page = "team_details";
    }
}
$userldap = $_SESSION['ldap'];
$editflagno = 0;
// echo 'edit', $editflagno;
$date = date("Y-m-d");
if (isset($_REQUEST['user'])) {
    $userldap = mysqli_real_escape_string($link, $_REQUEST['user']);
    if ($_REQUEST['user'] != $_SESSION['ldap']) {
        $editflagno = 1;
    }
}
// echo 'edit', $editflagno;


if (isset($_REQUEST['datepicker'])) {
    $date = check_date(mysqli_real_escape_string($link, $_REQUEST['datepicker']));
}

$curr_dt_time = date("Y-m-d");
// $validsql = mysqli_query($link, "select * from daily_reporting_data where ldap='" . $_SESSION['ldap'] . "' and `date`='" . date("Y-m-d", strtotime($date)) . "'");
$totalhours = 0;
$existing_query = mysqli_query($link, "select * from daily_reporting_data where date='" . $date . "' and ldap='" . $userldap . "' order by id asc");
if (mysqli_num_rows($existing_query) > 0) {
    $totalhourrow = mysqli_fetch_array(mysqli_query($link, "select sum(hours) as t,timestamp from daily_reporting_data where date='" . $date . "' and ldap='" . $userldap . "'"));
}
?>

<?php include('header.php');
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

?>

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
                                <?php if ($page === "team_calendar") { ?>
                                    <a style="color:#00F; font-weight:bold;" href="manager_calender_view.php"> Back</a>
                                <?php } elseif ($page === "team_details") { ?>
                                    <a style="color:#00F; font-weight:bold;" href="team_reporting_details.php"> Back</a>
                                <?php } ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <?php
                                $getScoreResult = getScore($userldap, $date); // Ensure $userldap and $date are defined
                                echo "<b>Score Value:</b> " . $getScoreResult; // Display the total score
                                ?>
                            </td>
                        </tr>
                    </table>

                    <form method="post" name="submitentry">
                        <input type="hidden" name="datepicker1" id="datepicker1" value="<?php echo $date; ?>" />
                        <table width="80%" cellspacing="0" align="center" border="0" class="manage" id="dailyentry">
                            <?php
                            $label = "Add";

                            // $manager_status_view = false;
                            // echo "Manager Status View: " . ($manager_status_view ? "True" : "False") . "<br>";
                            $sr = 0;
                            // echo "Manager Status View: " . $sr . "<br>";

                            if (mysqli_num_rows($existing_query) > 0) {
                                $label = "Update";
                                $taskarray = array();
                                while ($record = mysqli_fetch_array($existing_query)) {
                                    $taskarray[$sr]["taskdesc"] = $record["taskdesc"];
                                    $taskarray[$sr]["hours"] = $record["hours"];
                                    $taskarray[$sr]['date'] = $record['date'];
                                    $sr++;
                                }
                            }

                            ?>
                            <tr class="bluestrip" height="30">
                                <th width="15%">Sr. No.</th>
                                <th width="70%">Task Details</th>
                                <th width="5%">Minutes</th>
                                <?php
                                echo '<th width="10%">Score</th>';
                                ?>
                            </tr>

                            <?php
                            $sr1 = 0;
                            while ($sr1 < 10) {
                            ?>
                                <tr>
                                    <td align="center" valign="top"><?php echo ($sr1 + 1); ?></td>
                                    <td align="center" valign="top">
                                        <textarea <?php if ($editflagno == 1 || $enableflag == 1) { ?> readonly="readonly" <?php } ?> name="task[]" rows="5" cols="70" onBlur="showHours();"><?php if (isset($taskarray[$sr1]["taskdesc"])) echo $taskarray[$sr1]["taskdesc"]; ?></textarea>
                                    </td>
                                    <td align="center" valign="top">
                                        <input <?php if ($editflagno == 1 || $enableflag == 1) { ?> readonly="readonly" <?php } ?> type="text" name="hours[]" value="<?php if (isset($taskarray[$sr1]["hours"])) echo $taskarray[$sr1]["hours"]; ?>" size="5" maxlength="4" onKeyPress="return validData_new(event,'hours');" />
                                    </td>
                                    <?php
                                    if (isset($taskarray[$sr1]["taskdesc"]) && trim($taskarray[$sr1]["taskdesc"]) != "") {
                                    ?>
                                        <td align="center" valign="top" style="font-size: large;">
                                            <?php
                                            $score_query = mysqli_query($link, "SELECT score FROM daily_reporting_data WHERE ldap='" . $userldap . "' AND `date`='" . $date . "' AND taskid='" . ($sr1 + 1) . "'");
                                            $score_data = mysqli_fetch_array($score_query);
                                            // echo "score data View: " . $score_data . "<br>";
                                            if ($score_data['score'] == '0') {
                                                $days_difference = (strtotime($curr_dt_time) - strtotime($date)) / (60 * 60 * 24);
                                                echo "Days Difference: " . $days_difference . "<br>";
                                                if ($days_difference > 5) {
                                                    // echo "big";
                                            ?>
                                                    <input type="radio" name="score_<?php echo $sr1; ?>" id="thumbs_up_<?php echo $sr1; ?>"
                                                        onchange="updateScore(<?php echo $sr1; ?>, '1/<?php echo $sr; ?>')" />
                                                    <label for="thumbs_up_<?php echo $sr1; ?>" style="cursor:pointer;">👍</label>

                                                    <input type="radio" name="score_<?php echo $sr1; ?>" id="thumbs_down_<?php echo $sr1; ?>"
                                                        onchange="updateScore(<?php echo $sr1; ?>, '-1/<?php echo $sr; ?>')" />
                                                    <label for="thumbs_down_<?php echo $sr1; ?>" style="cursor:pointer;">👎</label>
                                                <?php
                                                } else {
                                                    // echo "else";
                                                ?>
                                                    <input type="radio" name="score_<?php echo $sr1; ?>" id="thumbs_up_<?php echo $sr1; ?>" disabled>
                                                    <label for="thumbs_up_<?php echo $sr1; ?>">👍</label>

                                                    <input type="radio" name="score_<?php echo $sr1; ?>" id="thumbs_down_<?php echo $sr1; ?>" disabled>
                                                    <label for="thumbs_down_<?php echo $sr1; ?>">👎</label>
                                                <?php
                                                }
                                            } else {
                                                // If score is not '0', display the thumbs-up/thumbs-down icon based on the score value
                                                if ($score_data['score'] > 0) {
                                                    // If the score is 'Yes', show thumbs-up
                                                ?>
                                                    <span>👍</span>
                                                <?php
                                                } elseif ($score_data['score'] < 0) {
                                                    // If the score is 'No', show thumbs-down
                                                ?>
                                                    <span>👎</span>
                                            <?php
                                                }
                                            }
                                        } else {
                                            ?>
                                        <td align="center" valign="top"></td>
                                    <?php
                                        }
                                    ?>


                                    </td>
                                </tr>
                            <?php
                                $sr1++;
                            }
                            ?>
                            <!-- <tr>
                                <td colspan="2" align="center">
                                    <input <?php //if ($editflagno == 1 || $enableflag == 1) { 
                                            ?> disabled="disabled" <?php //} 
                                                                    ?> type="submit" name="drentry" value="<?php //echo $label; 
                                                                                                            ?>" />
                                </td>
                                <td id="totalhours1"></td>
                            </tr> -->
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
        console.log('Function called with:', {
            taskId,
            score
        }); // Log function parameters

        var date = '<?php echo $date; ?>'; // Get the current date
        console.log('Date:', date);

        var taskid = taskId + 1; // Task id starts from 1
        console.log('Task ID:', taskid);

        var scoreValue = score;
        console.log('Score Value:', scoreValue);

        var managerLdap = '<?php echo $_SESSION['ldap']; ?>'; // Get manager LDAP
        console.log('Manager LDAP:', managerLdap);

        // Initialize XMLHttpRequest
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'manager_reporting_entry.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        // alert('wait');

        xhr.onreadystatechange = function() {
            console.log('XHR Ready State:', xhr.readyState); // Log ready state

            if (xhr.readyState == 4 && xhr.status == 200) {
                console.log('XHR Ready State:', xhr.readyState, xhr.status); // Log ready state

                console.log('Score updated successfully');
                // alert('wait')

                // window.location.reload(); // Reload the page
            }
        };

        xhr.onerror = function() {
            console.error('XHR encountered an error:', xhr.statusText); // Log network error
        };

        // Send the request
        var params = 'action=update_score&date=' + date + '&taskid=' + taskid + '&score=' + scoreValue + '&manager_ldap=' + managerLdap;
        // var params = 'action=update_score&date=' + date + '&taskid=' + taskid + '&score=' + scoreValue;
        console.log('XHR Params:', params); // Log parameters being sent
        xhr.send(params);
        // alert('wait');

    }
</script>

<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (isset($_POST['action']) && $_POST['action'] == 'update_score') {
    // Log the incoming POST data
    error_log('POST data: ' . print_r($_POST, true));

    // Extract parameters from POST request
    $taskid = $_POST['taskid'];
    $score = $_POST['score'];
    $date = $_POST['date'];
    $manager_ldap = $_POST['manager_ldap'];

    // Fetch the row ID using the combination of date and taskid
    $task_query = mysqli_query($link, "SELECT id FROM daily_reporting_data WHERE date = '$date' AND taskid = '$taskid'");
    if (!$task_query) {
        error_log('Task Query Error: ' . mysqli_error($link));
        echo 'Error in task query';
        exit;
    }

    $task = mysqli_fetch_array($task_query);
    if ($task) {
        $task_id = $task['id'];

        // Update the score and manager_ldap value
        $update_query = "UPDATE daily_reporting_data SET 
                            score = '$score', 
                            manager_ldap = '" . $_SESSION['ldap'] . "', 
                            score_timestamp = NOW() 
                         WHERE id = '$task_id'";
        $update_result = mysqli_query($link, $update_query);

        if (!$update_result) {
            error_log('Update Query Error: ' . mysqli_error($link));
            echo 'Error updating score';
        } else {
            echo 'Score updated successfully';
        }
    } else {
        error_log('Task not found for date: ' . $date . ' and taskid: ' . $taskid);
        echo 'Task not found';
    }
}
?>