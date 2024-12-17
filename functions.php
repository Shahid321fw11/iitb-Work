<?php
function getName($ldap)
{
	global $link1;
	$name = mysqli_fetch_array(mysqli_query($link1, "select fname,lname from login where memberid='" . $ldap . "'"));
	return $name[0] . " " . $name[1];
}



function getToolName($tid)
{
	global $link1;
	$name = mysqli_fetch_array(mysqli_query($link1, "select name from resources where machid='" . $tid . "'"));
	return $name[0];
}


function getSlotbookingID($ldap)
{
	global $link;
	$sid = mysqli_fetch_array(mysqli_query($link, "select slotbooking_id from daily_reporting_login where ldap='" . $ldap . "'"));
	return $sid[0];
}


function getHoursMinutes($totalminutes)
{
	$totalhrs = 0;
	$hours = 0;
	$minutes = 0;

	if ($totalminutes != 0) {
		$hours = floor($totalminutes / 60);
		$minutes = ($totalminutes % 60);
		$totalhrs = $hours . ":" . $minutes;
	}
	return $totalhrs;
}


function getHoursMinutesFmEpoch($totalseconds)
{
	$totalhrs = 0;
	$hours = 0;
	$minutes = 0;

	if ($totalseconds != 0) {
		$minutes = ($totalseconds / 60) % 60;
		$hours = $totalseconds / 3600;
		$totalhrs = floor($hours) . ":" . $minutes;
	}
	return $totalhrs;
}




function toMinues($t)
{

	$totalmns = 0;
	$hours = 0;
	$minutes = 0;

	if ($t != 0) {
		$totalmns = (strtotime($t) - strtotime(date("Y-m-d"))) / 60;
	}

	return $totalmns;
}


function getScore($ldap, $date)
{
	global $link;
	$existing_query = mysqli_query($link, "SELECT * FROM daily_reporting_data WHERE date = '$date' AND ldap = '$ldap' ORDER BY id ASC");
	$totalScore = 0; // Variable to calculate total score
	$counter = 0;

	if (mysqli_num_rows($existing_query) > 0) {
		while ($row = mysqli_fetch_assoc($existing_query)) {
			$score = $row['score'];
			// Convert $score to a numeric value (float in this case)
			$numeric_score = floatval($score); //
			$totalScore += $numeric_score;
			$counter++;
		}
	}
	echo "<script>console.log('Total Score:', $totalScore);</script>";
	echo "<script>console.log('Total row:', $counter);</script>";
	$averageScore = ($counter > 0) ? $totalScore / $counter : 0;
	$formattedScore = number_format($averageScore, 2); // Format the result to 2 decimal places

	// Return the formatted average score
	return $formattedScore;
}
