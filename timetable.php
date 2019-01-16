<html>
<link rel='stylesheet' type='text/css' href='Style.css'>
<div id = "topBanner"> 
<h1>TimeTable</h1>
</div>
</html>
<?php
/*
Time table will show 
- Days of the Week
- The body part(s) to be trained on that day
User must be able to change timetable
*/
require_once "core/init.php";
include('ColumnButtons.html');
$user = new User();
	if(!$user->isLoggedIn()){
		header('Location: index.php');
	}
$timetable = new TimeTable;





?>
