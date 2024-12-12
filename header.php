<?php
error_reporting(0);
ob_start();
session_start();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>Daily Reporting</title>
	<link rel="stylesheet" href="js_css/style-inner.css" type="text/css" media="screen" />
	<script type="text/javascript">
		function validate_form() {
			valid = "true";
			if (document.login.uid.value == "") {
				alert("Please enter login id");
				valid = false;
			} else {
				var testresults;
				var str = document.login.uid.value;
				var filter = /^.+@.+\..{2,3}$/;

				if (filter.test(str)) {
					testresults = true;

				} else {
					alert("Please enter valid login id");
					valid = false;
				}

			}


			if (document.login.pass.value == "") {
				alert("Please enter password");
				valid = false;
			}
			if (document.login.captcha_code.value == "") {
				alert("Please Verify the Captcha");
				valid = false;
			}
			if (valid == false) {
				return false;
			} else {
				return true;
			}
		}
	</script>

	<link rel="stylesheet" href="../css/jquery-ui.css">
	<script src="js_css/jquery-1.10.2.js"></script>
	<script src="js_css/jquery-ui.js"></script>




	<script>
		var dates = <?php echo json_encode($predates); ?>;
		jQuery(function() {
			jQuery('#datepicker').datepicker({
				dateFormat: 'yy-mm-dd',
				changeMonth: true,
				changeYear: true,
				minDate: "-6d",
				maxDate: new Date(),
				beforeShowDay: function(date) {
					var y = date.getFullYear().toString(); // get full year
					var m = (date.getMonth() + 1).toString(); // get month.
					var d = date.getDate().toString(); // get Day
					if (m.length == 1) {
						m = '0' + m;
					} // append zero(0) if single digit
					if (d.length == 1) {
						d = '0' + d;
					} // append zero(0) if single digit
					var currDate = y + '-' + m + '-' + d;
					if (dates.indexOf(currDate) >= 0) {
						return [true, "ui-highlight"];
					} else {
						return [true];
					}
				}
			});
		})
	</script>

	<script>
		jQuery(function() {
			jQuery('#datepickeruser').datepicker({
				dateFormat: 'yy-mm-dd',
				changeMonth: true,
				changeYear: true,
				maxDate: new Date(),
			});


			$("#from").datepicker({
				dateFormat: 'dd-mm-yy',
				changeMonth: true,
				numberOfMonths: 1,
				onClose: function(selectedDate) {
					$("#to").datepicker("option", "minDate", selectedDate);
				}
			});
			$("#to").datepicker({
				dateFormat: 'dd-mm-yy',
				changeMonth: true,
				numberOfMonths: 1,
				onClose: function(selectedDate) {
					$("#from").datepicker("option", "maxDate", selectedDate);
				}
			});


		})
	</script>



	<script>
		/* Function to ristrict input through keyboard*/
		var KEY_NULL = null;
		var KEY_NONE = 0;
		var KEY_BCKSPC = 8;
		var KEY_TAB = 9;
		var KEY_ENTER = 13;
		var KEY_ESC = 27;

		function validData_new(e, field) {



			var key;
			var keychar;

			if (window.event) {
				key = window.event.keyCode;
			} else if (e) {
				key = e.which;
			} else {
				return true;
			}
			keychar = String.fromCharCode(key);
			//allowed characters



			switch (field) {
				case "hours":
					chars = "1234567890"
					break;
				case "allow_all":
					chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789 ()_+|-={}[]:\";',.?/ !#$%^&*"
					break;
			}
			// Control keys (no @#$% "magic numbers")
			if (
				(key == KEY_NULL) ||
				(key == KEY_NONE) ||
				(key == KEY_BCKSPC) ||
				(key == KEY_TAB) ||
				(key == KEY_ENTER) ||
				(key == KEY_ESC) ||
				((chars).indexOf(keychar) > -1)
			) {
				return true;
			}
			return false;
		}
		/*end of function*/


		function validateUser() {
			var user = $.trim($("#user").val());
			var from = $.trim($("#from").val());
			var to = $.trim($("#to").val());


			if (user == "") {
				alert("Please select user name!!");
				return false;
			}

			if (from == "") {
				alert("Please select from date!!");
				return false;
			}

			if (to == "") {
				alert("Please select to date!!");
				return false;
			}

			return true;
		}

		// added by shahid on 6 Dec, 
		function validateUser2() {
			var user = $.trim($("#user").val());
			var from = $.trim($("#from").val());
			var to = $.trim($("#to").val());


			// if (user == "") {
			// 	alert("Please select user name!!");
			// 	return false;
			// }

			if (from == "") {
				alert("Please select from date!!");
				return false;
			}

			if (to == "") {
				alert("Please select to date!!");
				return false;
			}

			return true;
		}

		function submitform() {
			$("#submitdate").submit();
		}

		function getHoursMinutes() {
			var totalhrs = 0;
			var hoursc = document.getElementsByName('hours[]');
			var tasksc = document.getElementsByName('task[]');

			for (i = 0; i < hoursc.length; i++) {
				if ((tasksc[i].value != "") && (hoursc[i].value != "")) {
					totalhrs = totalhrs + parseInt(hoursc[i].value);
				}
			}




			return totalhrs;

		}

		function showHours() {
			var totalhrs = 0;
			totalhrs = getHoursMinutes();

			if (totalhrs != 0) {
				var hours = Math.floor(totalhrs / 60);
				var minutes = (totalhrs % 60);
				totalhrs = hours + ":" + minutes;
			}

			document.getElementById('totalhours').innerHTML = "Total Hours: " + totalhrs;
			document.getElementById('totalhours1').innerHTML = "Total Hours: " + totalhrs;

		}

		function validate_confirm() {
			var error = 0;
			var totalhrs = 0;

			var tasks = document.getElementsByName('task[]');
			var hours = document.getElementsByName('hours[]');

			for (i = 0; i < tasks.length; i++) {
				if ((tasks[i].value != "") && (hours[i].value == "" || hours[i].value == 0 || hours[i].value > 1440 || isNaN(hours[i].value))) {
					document.getElementById("alt_hours_" + i).innerHTML = "Please enter valid minutes";
					error = 1;
				} else {
					document.getElementById("alt_hours_" + i).innerHTML = "";
				}
			}


			for (j = 0; j < hours.length; j++) {
				if ((hours[j].value != "") && (tasks[j].value == "")) {
					document.getElementById("alt_tasks_" + j).innerHTML = "Please enter task details";
					error = 1;
				} else {
					document.getElementById("alt_tasks_" + j).innerHTML = "";
				}
			}


			if (error == 1) {
				return false;
			} else {

				totalhrs = getHoursMinutes();
				if (totalhrs > 1440) {
					alert("Entered minutes exceeding total minutes in a day!!!");
					window.location.reload();
					return false;
				}
			}





			return true;
		}
	</script>
	<script src="js_css/jquery-1.10.2.js"></script>
	<script src="js_css/jquery-ui.js"></script>

	<link rel='stylesheet' href='js_css/jquery-ui.min.css' />




	<script src='js_css/moment.min.js'></script>
	<link href='js_css/fullcalendar.css' rel='stylesheet' />
	<link href='js_css/fullcalendar.print.css' rel='stylesheet' media='print' />
	<script src='js_css/fullcalendar.min.js'></script>
	<script>
		$(document).ready(function() {

			$('#calendar').fullCalendar({

				theme: true,
				header: {
					left: 'prev,next today',
					center: 'title',
					right: 'year,month,agendaWeek,agendaDay'
				},
				defaultDate: '<?php echo $currentT; ?>',
				editable: true,
				eventLimit: true, // allow "more" link when too many events
				events: <?php echo json_encode($data); ?>,



				eventClick: function(calEvent, jsEvent, view) {

					if (calEvent.id != '') {

					}

					/*var win = window.open('cancelleave.php?id='+calEvent.id, 'Cancel Leave','left=100,top=100,width=500,height=500,resizable=0');
					win.focus();
					void(0);*/


					$(this).css('border-color', 'red');
					$("#eventInfo").html(calEvent.description);
					$("#eventLink").attr('href', event.url);
					$("#eventContent").dialog({
						modal: true,
						title: event.title
					});
				},

				eventMouseover: function(calEvent, jsEvent, view) {
					$(jsEvent.target).attr('title', calEvent.description);
				},


				eventRender: function(event, element) {
					element.bind('mousedown', function(e) {
						/*if (e.which == 3) {
							if(event.id!='ex')
							{
							  var ans=confirm("Do you want to cancel leave??");
							  if(ans)
							  {
							   window.location='cancel_leaves.php?id='+event.id;
							  }
							  else
							  {
							    return false;
							  }
							}
						}*/
					});
				}



			});

		});
	</script>


</head>

<body class="home">
	<div id="header">
		<div class="logo_marg">
			<div id="logo"><a href="index.php"><img src="images/mainlogo.png" border="0" style="outline:none;" /></a>
			</div>
		</div>

		<div style="padding-left:193px;">

			<div style="padding:5px 10px; text-align:right; color:#fffdfd; font-weight:bold;">
				<?php echo date("l dS \of F Y h:i:s A"); ?>
			</div>

			<div style=" background-image:url(images/header_main_bg.jpg); background-position: bottom;    font-family:Verdana, Arial, Helvetica, sans-serif;">
				<div style="font-size:24px; padding:22.5px 15px; font-weight:500;color:#ffffff; text-align: center;">Daily Reporting</div>
				<div style="text-align:right;font-size:12px; color:#FFFFFF; font-weight:bold;">Released on 01-07-2016 Version 1.0 </div>
			</div>

		</div>
	</div>
	<div class="clear"></div>
	<div id="wrapper">