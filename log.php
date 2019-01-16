<html>
<link rel='stylesheet' type='text/css' href='Style.css'>
<div id = "topBanner"> 
<h1>Log</h1>
</div>
</html>

<?php
require_once "core/init.php";

include('ColumnButtons.html');

/*
Log prints historcal data
- performed routines e.g Leg Routine 12/10/2017
- user: bmi, weight, height
*/
printPastPerformedRoutines();


function printPastPerformedRoutines(){
	$query = DB::getInstance()->prep("SELECT * FROM timetable WHERE UserId=? AND Completed='1' GROUP BY SetDate desc");
	$query -> bindValue(1, $_SESSION['id']);
	$query->execute();
	$results = $query->fetchAll(PDO::FETCH_ASSOC);
	//print_r($results);
	echo "<table border='1px'>";
	echo "<tr>";
	echo "<th>";
	echo "Date performed";
	echo "</th>";
	echo "<th>";
	echo "Your Rating";
	echo "</th>";
	echo "<th>";
	echo "Routine Name";
	echo "</th>";
	echo "</tr>";


	foreach($results as $result){
		echo "<tr>";
		echo "<th>";
		echo $result['SetDate'];
		echo "</th>";

		echo "<th>";
		echo $result['Rating'];
		echo "</th>";


		echo "<th>";
		$query = DB::getInstance()->prep("SELECT * FROM routine WHERE RoutineId = ? ");
		$query -> bindValue(1, $result['RoutineId']);
		$query -> execute();
		$results = $query->fetchAll(PDO::FETCH_ASSOC);

		foreach($results as $result){
			echo $result['Name'];
		}
		

		echo "</th>";
		echo "</tr>";
	}
	echo "</table>";
}
?>
