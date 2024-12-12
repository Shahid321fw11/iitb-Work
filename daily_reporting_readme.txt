CEN - Daily Reporting module 
url : http://www.cen.iitb.ac.in/dailyreporting/
Database name :  daily_reporting_cen
Tables : 
3.	daily_reporting_data  
4.	daily_reporting_login
user interface : 
7.	user can login with his / her  slot booking id and its passwords
8.	user can entries their daily reporting details for current weeks( 7 days )
9.	user can edit their data only today and their previous day (only for two days )
10.	user can view their daily reporting on calendar 
11.	user can see full detais on  daily reporting details 
12.	if user has done  slot booking  on the respect dates then  it will show( only if he/she inserted daily reporting on that day )
notes : -  slot booking  time duration  will be added in daily reporting module.

admin (valli madam , Shweta)
•	admin see all users and their details on calendar view 
•	admin can see all users or particular users with full details (daily reporting + slot booking details)
Added by Rajni

Admin is hard coded. It is there in menu.php, it is there in daily-reporting_details.php.
As of now admin is slotbooking member id =1 and 888.
login is using slotbooking account
