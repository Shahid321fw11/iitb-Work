<?php
require("dbconnect.php");
include('functions.php');
include('validation.php');

session_start();
if (!isset($_SESSION['ldap'])) {
  header("Location:index.php");
  exit();
}

$page = "download";
$all = 0;

if ($_SESSION['ldap'] == "888" || $_SESSION['ldap'] == "1") {
  //$sql="select memberid,fname,lname from login order by fname asc";	//commented by kavita
  $sql = "select memberid,fname,lname from login where `position` <> 'Faculty' and (FROM_UNIXTIME(UNIX_TIMESTAMP(str_to_date( expiry_date , '%m/%d/%Y' )),'%Y-%m-%d'))>= CURDATE() order by fname asc";
  $all = 1;
} else {
  $sql = "select memberid,fname,lname from login where memberid='" . $_SESSION['ldap'] . "'";
  $all = 0;
}


?>


<?php
$currentmonth = date("m");
$currentyear = date("Y");
$currentuser = '';

if (isset($_REQUEST["user"])) {
  $currentuser = mysqli_real_escape_string($link, $_REQUEST["user"]);
}

if ((isset($_REQUEST["from"]) && ($_REQUEST["from"] != "")) && (isset($_REQUEST["to"]) && ($_REQUEST["to"] != ""))) {
  $currentmonth = mysqli_real_escape_string($link, $_REQUEST["from"]);
  $currentyear = mysqli_real_escape_string($link, $_REQUEST["to"]);
}
?>


<?php
$currentmonth = date("m");
$currentyear = date("Y");
$currentuser = '';

if (isset($_REQUEST["user"])) {
  $currentuser = mysqli_real_escape_string($link, $_REQUEST["user"]);
}

if ((isset($_REQUEST["from"]) && ($_REQUEST["from"] != "")) && (isset($_REQUEST["to"]) && ($_REQUEST["to"] != ""))) {
  $currentmonth = mysqli_real_escape_string($link, $_REQUEST["from"]);
  $currentyear = mysqli_real_escape_string($link, $_REQUEST["to"]);
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



          <form name="submituser" method="post" id="submituser">
            <table width="80%" align="center" border="0">
              <tr>
                <td>
                  <b>Select User: <font color="#FF0000">*</font></b>
                  <select name="user" id="user">
                    <option value="">--Select User--</option>
                    <!--<?php if ($all == 1) { ?><option value="all">All Users</option><?php } ?>-->
                    <?php
                    $usersql = mysqli_query($link1, $sql);
                    while ($userrow = mysqli_fetch_array($usersql)) { ?>
                      <option value="<?php echo $userrow["memberid"]; ?>" <?php if ($currentuser == $userrow["memberid"]) { ?> selected="selected" <?php } ?>><?php echo $userrow["fname"], " " . $userrow["lname"]; ?></option>
                    <?php
                    }
                    ?>
                  </select>
                </td>
                <td><b>Select Date: <font color="#FF0000">*</font></b>
                  <!--<input placeholder="Select Date" type="text" name="datepickeruser" id="datepickeruser" onChange="submitform();"  />
-->

                  <?php
                  $montharray = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
                  ?>
                  Month <select id="from" name="from">
                    <?php for ($monthi = 1; $monthi <= 12; $monthi++) { ?>
                      <option value="<?php echo $monthi; ?>" <?php if ($currentmonth == $monthi) { ?> selected="selected" <?php } ?>><?php echo $montharray[$monthi - 1]; ?></option>
                    <?php } ?>
                  </select>


                  Year <select id="to" name="to">
                    <?php for ($m = date("Y"); $m > (date("Y") - 10); $m--) { ?>
                      <option value="<?php echo $m; ?>" <?php if ($currentyear == $m) { ?> selected="selected" <?php } ?>><?php echo $m; ?></option>
                    <?php } ?>
                  </select>

                </td>
                <td>
                  <input type="submit" name="searchuser" id="searchuser" value="Submit" onclick="return validateUser();" />
                </td>
              </tr>

            </table>
          </form>

          <?php
          if ($_REQUEST["user"] != "all") {
            $colspan = "7";
          } else {
            $colspan = "6";
          }
          ?>

          <br />
          <?php if (isset($_REQUEST["user"])) { ?>
            <?php
            $monthNum  = mysqli_real_escape_string($link, $_REQUEST["from"]);
            $dateObj   = DateTime::createFromFormat('!m', $monthNum);
            $monthName = $dateObj->format('F');
            ?>
            <table width="80%" cellspacing="0" align="center" border="0">
              <tr>
                <td align="left"><b>User Name:</b> <?php if ($_REQUEST["user"] == "all") {
                                                      echo "All";
                                                    } else {
                                                      echo getName($_REQUEST["user"]);
                                                    } ?></td>
                <td align="left"><b>Date:</b> <?php echo $monthName . ", " . $_REQUEST["to"]; ?></td>
              </tr>
            </table>
          <?php } ?>
          <br />

          <table width="100%" cellspacing="0" align="center" border="0" class="manage" id="dailyentry">
            <tr class="bluestrip" height="30">
              <th>Sr. No.</th>
              <th>Name</th>
              <th>Date</th>
              <th>Total Hours</th>
              <th>Updated on</th>
              <?php
              if ($_REQUEST["user"] != "all") {
              ?>
                <th>Task Details</th>
                <th>Slotbooking Details</th>
              <?php } ?>
            </tr>

            <?php
            if (isset($_REQUEST["searchuser"])) {
              $sql = "";
              $esql = "";
              if ((isset($_REQUEST["from"]) && ($_REQUEST["from"] != "")) && (isset($_REQUEST["to"]) && ($_REQUEST["to"] != ""))) {
                //$sql=" date between '".date("Y-m-d",strtotime($_REQUEST["from"]))."' and '".date("Y-m-d",strtotime($_REQUEST["to"]))."'";

                $sql = "MONTH(Date) = '" . mysqli_real_escape_string($link, $_REQUEST["from"]) . "' AND YEAR(Date) = '" . mysqli_real_escape_string($link, $_REQUEST["to"]) . "'";
              }

              if ($_REQUEST["user"] == "all") {
                $esql = "select ldap,date,taskdesc,sum(hours) as t, timestamp from daily_reporting_data where  $sql group by ldap";
                $existing_query = mysqli_query($link, $esql);
              } else {
                $sql .= " and ldap='" . mysqli_real_escape_string($link, $_REQUEST["user"]) . "'";
                $esql = "select ldap,date,taskdesc,sum(hours) as t, timestamp from daily_reporting_data where  $sql group by date";
                $existing_query = mysqli_query($link, $esql);
              }



              if (mysqli_num_rows($existing_query) > 0) {
                $sr = 1;
                $daterangesum = 0;
                while ($record = mysqli_fetch_array($existing_query)) {

                  $slothours = 0;
                  $slothourstotal = 0;
                  $slothoursepoch = 0;

            ?>


                  <?php
                  $slotbookingid = ($record["ldap"]);
                  if ($slotbookingid != 0) {

                    $slotsql = mysqli_query($link1, "select * from `reservations` where `invite_users`='" . $slotbookingid . "' and from_unixtime(`startdate`,'%Y-%m-%d')='" . date("Y-m-d", strtotime($record["date"])) . "'");
                  }
                  ?>

                  <tr>
                    <td align="center" valign="top"><?php echo $sr; ?></td>
                    <td align="center" valign="top"><?php echo getName($record["ldap"]); ?></td>
                    <td align="center" valign="top"><?php echo date("d-m-Y", strtotime($record["date"])); ?></td>
                    <td align="center" valign="top"><?php
                                                    if ($slotbookingid != 0) {
                                                      if (mysqli_num_rows($slotsql) > 0) {


                                                        echo getHoursMinutes($record["t"]) . "";
                                                        while ($slotsqlrow = mysqli_fetch_array($slotsql)) {

                                                          $slothoursepoch = $slotsqlrow["enddate"] - $slotsqlrow["startdate"];
                                                          $slothours = getHoursMinutesFmEpoch($slothoursepoch);
                                                          echo "+" . $slothours;
                                                          $slothourstotal = $slothourstotal + ($slothoursepoch / 60);
                                                        }


                                                        echo "=" . getHoursMinutes($record["t"] + ($slothourstotal)) . ' Hours';
                                                      } else {
                                                        echo getHoursMinutes($record["t"]) . ' Hours';
                                                      }

                                                      $sumtoadd = $slothourstotal + $record["t"];
                                                    }
                                                    ?></td>
                    <td align="center" valign="top"><?php echo date("d-m-Y h:s", strtotime($record["timestamp"])); ?></td>
                    <?php if ($_REQUEST["user"] != "all") { ?>
                      <td align="center" valign="top">
                        <a href="daily_reporting_entry.php?frompage=detail&user=<?php echo $record["ldap"]; ?>&datepicker=<?php echo
                                                                                                                          $record["date"]; ?>&from=<?php echo $_REQUEST["from"]; ?>&to=<?php echo $_REQUEST["to"]; ?>"><img src="images/task_view.png" /></a>
                      </td>
                      <td align="center" valign="top">
                        <?php
                        if ($slotbookingid != 0) {
                          if (mysqli_num_rows($slotsql) > 0) {
                        ?>
                            <a onclick="window.open('slot_details.php?memberid=<?php echo $slotbookingid; ?>&rdate=<?php echo $record["date"]; ?>', 'Slot Details', 'width=750, height=500'); return false;" href="#"><img src="images/slot_view.png" /></a>
                        <?php
                          }
                        } else {
                          echo "Not Found";
                        }
                        ?>
                      </td>
                    <?php } ?>
                  </tr>
            <?php



                  $daterangesum = $daterangesum + $sumtoadd;



                  $sr++;
                }
              } else {
                echo "<tr><td colspan='$colspan'>No data found</td></tr>";
              }
            }
            ?>
            <tr>
              <td colspan="<?php echo $colspan; ?>" align="left"><b><?php if (isset($_REQUEST["searchuser"])) { ?>Total hours in selected date range=> <?php echo getHoursMinutes($daterangesum); ?> <?php } ?></b></td>
            </tr>
          </table>
          </form>
          <br /><br />



          <br /><br />



          <div style="height:20px;"></div>
        </div>

      </div>
    </div>

    <div style="clear:both;"></div>


  </div>
</div>


<?php include('footer.php'); ?>