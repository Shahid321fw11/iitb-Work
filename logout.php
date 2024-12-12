<?php
error_reporting(0);
session_start();
?>
<?php include("dbconnect.php");?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Daily Reporting</title>
<style>
body
{
	    font: 14px/20px Arial,Helvetica,sans-serif;
}


#dailyentry tr td
{
	border-bottom:1px solid #666;
	padding-top:4px;
	padding-bottom:4px;
}

.ui-highlight .ui-state-default{
			background: #5574C9 !important;
			border-color: #5574C9 !important;
			color: white !important;
		}


</style>
</head>
<body>
<LINK REL="SHORTCUT ICON" HREF="images/icon.ico">
<!--BodyPan-->
<div id="bodyPan">




<div style=" float:left;width:100%; padding-top:15px; text-align:center;">

<?php
if(isset($_SESSION['ldap']))
{
    unset($_SESSION['uname']); 
	unset($_SESSION['ldap']); 
    print("<img height=\"100\" src=\"images/logout.png\">");
    print("<h1>You have logged out</h1>");
    echo "<a href='index.php'>Click here to Login</a>";
}
else
{
    print("<img height=\"100\" src=\"images/logout.png\">");
    print("<h1>You need to first Login</h1>");
    echo "<a href='index.php'>Click here to Login</a>";
}
?>
</div>
</div>
</body>
</body>
</html>