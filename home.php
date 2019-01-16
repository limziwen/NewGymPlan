<html></head>
<link rel='stylesheet' type='text/css' href='Style.css'>
	<div id = "topBanner"> 
		<h1>GYM PLANNER </h1>
	<form method = "post" >
		<input type ="submit" name = "logOut" value = "Log Out" class='btn' />
	</div>
	</form>
</html>
<?php
	include('ColumnButtons.html');
	require_once('core/init.php');
	$user = new User();
	if(!$user->isLoggedIn()){
		$user->logOut();
	}

	
	if(isset($_POST['logOut'])){
		$user->logOut();
	}

	echo "Hello ";
	echo $user->getFirstName();
	echo "<br>";

	//echo "<br>";
	//echo "User ID is: ".$_SESSION ['id'];
	//echo "<br>";


	$date = todayDate();

	checkRoutinePresent($date);



function todayDate(){
	date_default_timezone_set('Singapore');
	$date = date('Y-m-d ');
	return $date;
}

function checkRoutinePresent($date){
	$query = DB::getInstance()->prep("SELECT * FROM timetable WHERE SetDate=? AND UserId=? AND Completed = '0'");
	$query -> bindValue(1,$date);
	$query -> bindValue(2, $_SESSION['id']);
	$query -> execute();
	$results=$query->fetchAll(PDO::FETCH_OBJ);
	
	if(count($results)){
		echo "<br>";
		echo "Today's Routine";
		echo "<br>";
		echo "Date: ".$results[0]->SetDate;
		echo "<br> ";
		$routine = new Routine($results[0]->RoutineId,$results[0]->TimeTableId);
		$routine->checkRoutineDoneBefore();
		$routine->displayRoutine();
		$routine->printFinishRoutine();
		
		
	}else{
		echo "No Routine Today";
	}
	

}


?>
