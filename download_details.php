<?php
error_reporting(0);
session_start();
include 'dbconnect.php';
include('functions.php');


header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=user_hours_data.csv');

// if ($_SESSION['ldap'] == "7" || $_SESSION['ldap'] == "1")
if ($_SESSION['ldap'] == "888" || $_SESSION['ldap'] == "1") {
	$sql = "SELECT a.ldap,a.date,sum(a.hours) as t_hours FROM `daily_reporting_data` as a group by a.ldap,a.date";
} else {
	$sql = "SELECT a.ldap,a.date,sum(a.hours) as t_hours FROM `daily_reporting_data` as a  WHERE  a.ldap='" . $_SESSION['ldap'] . "' group by a.ldap,a.date";
}

$data = array();
$datasql = mysqli_query($link, $sql);

$output = fopen('php://output', 'w');
fputcsv($output, array("S.No", "User Name", "Date", "Total hours"));

$i = 1;
while ($datar = mysqli_fetch_array($datasql)) {
	$uname = getName($datar["ldap"]);
	fputcsv($output, array($i, $uname, date("d-m-Y", strtotime($datar["date"])), getHoursMinutes($datar["t_hours"])));
	$i++;
}
