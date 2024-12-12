<html>
<head><title>Slotbooking Details</title></head>
<style>
body, table
{
	font:12px/20px Arial,Helvetica,sans-serif
}
</style>
<body>
<table width="100%" cellspacing="0" align="center" border="0" style="border: thin solid #76070D;" id="dailyentry">
<tr style="color: #FFF;background-color: #2E64AE;" height="30">
<th valign="top">Sr. No.</th>
<th valign="top">User Name</th>
<th valign="top">Guide Name</th>
<th valign="top">Project</th>
<th valign="top">Slot Start Date & Time</th>
<th valign="top">Slot End Date & Time</th>
<th valign="top">Tool Name</th>
<th valign="top">Booked On</th>
<th valign="top">Summary</th>
<th valign="top">Oparator Name</th>
</tr>

<?php
require("dbconnect.php");
require("functions.php");


$memberid=mysqli_real_escape_string($link1,$_REQUEST["memberid"]);
$rdate=mysqli_real_escape_string($link1,$_REQUEST["rdate"]);

$slotsql=mysqli_query($link1,"select * from `reservations` where `invite_users`='".$memberid."' and from_unixtime(`startdate`,'%Y-%m-%d')='".date("Y-m-d",strtotime($rdate))."'");

if(mysqli_num_rows($slotsql)>0)
{
$srno=1;
while($slotrecords=mysqli_fetch_array($slotsql))
{
   ?>
   <tr <?php if($srno%2==0){?>  bgcolor="#D9FAFF" <?php } ?>>
   <td><?php echo $srno;?></td>
   <td><?php echo getName($slotrecords["memberid"]);?></td>
   <td><?php echo getName($slotrecords["guide"]);?></td>
   <td><?php echo $slotrecords["project"];?></td>
   <td><?php echo date("d-m-Y H:i",$slotrecords["startdate"]);?></td>
   <td><?php echo date("d-m-Y H:i",$slotrecords["enddate"]);?></td>
   <td><?php echo getToolName($slotrecords["machid"]);?></td>
   <td><?php echo date("d-m-Y H:i",$slotrecords["datetime"]);?></td>
   <td><?php echo $slotrecords["summary"];?></td>
   <td><?php echo getName($slotrecords["invite_users"]);?></td>
   </tr>
   <?php
   $srno++;
}
}
else
{


echo "<tr><td colspan='10'>Not Found</td></tr>";	
	
}
	

?>
</table>
</body>
</html>
