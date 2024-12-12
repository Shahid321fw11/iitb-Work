<?php
set_time_limit(0);
$link = mysqli_connect("localhost", "root", "", "daily_reporting_cen");
if (!$link) {
    die('Failed to connect to server: ' . mysqli_error());
}


$link1 = mysqli_connect("localhost", "root", "", "slotbooking");
if (!$link1) {
    die('Failed to connect to server: ' . mysqli_error());
}

// changes done by shahid on 4th October 2024,  for connecting Hr_table > reporting structure
$link2 = mysqli_connect("localhost", "root", "", "hr_portal");
if (!$link2) {
    die('Failed to connect to server: ' . mysqli_error());
}
