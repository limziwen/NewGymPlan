<html>
<link rel='stylesheet' type='text/css' href='Style.css'>
<div id = "topBanner"> 
<h1>Create</h1>
</div>
</html>

<?php
/*
Create should allow Users to create a routine, by providing name, exercises, sets, reps drop down list

*/
require_once "core/init.php";
include('ColumnButtons.html');
?>
<html>
	<form  method="get" action='create.php'>
		Name of Routine: <input type='text' name='RoutineName' required> <br>
		Number of Exercises:  <input type='text' name='NumExercise' placeholder='0' required> <br>
		<input type = "submit" value="Create a Routine!" class='btn' name='create'>
	</form>
</html>

<?php
if(isset($_GET['create'])){
	$num = $_GET['NumExercise'];
	$nameRoutine = $_GET['RoutineName'];

	
	echo "<table border='1px' >";
	echo "<tr>";

	echo "<th>";
	echo "Exercise";
	echo "</th>";

	echo "<th>";
	echo "Sets";
	echo "</th>";

	echo "<th>";
	echo "Reps";
	echo "</th>";

	echo "<th>";
	echo "Rest Period (seconds)";
	echo "</th>";

	echo "</tr>";

	echo "<form>";
	echo "<input type='hidden' name='RoutineName' value='$nameRoutine'>";
	echo "<input type='hidden' name='NumExercise' value='$num'>";
	for ($i=1; $i<=$num; $i++){
		echo "<tr>";

		echo "<td>";
		printExercises($i);
		echo "</td>";

		echo "<td>";
		echo "<input type='number' min='1' max='100' name='Sets".$i."'  placeholder='0 Sets' required>";
		echo "</td>";


		echo "<td>";
		echo "<input type='number' min='1' max='100' name='Reps".$i."'  placeholder='0 Reps' required>";
		echo "</td>";

		echo "<td>";
		echo "<input type='number' min='1' max='3600' name='Rest".$i."' required>";
		echo "</td>";

		echo "</tr>";
	}

	echo "</table>";

	//echo "<form method='get' action='create.php'>";
	echo "<input type='submit' name='Create' class='btn' value='Create'>";
	
	echo "</form>";
}

if (isset($_GET['Create'])){ 

		echo "Created Routine!";
		echo "<br>";
		echo "Routine Created: ".$_GET['RoutineName'];
		echo "<br>";
		//echo $_SESSION['name'];
		echo "<br>";

		$query = DB::getInstance()->prep("INSERT into routine (Name, Creator) VALUES (?,?) ");
		$query -> bindValue(1, $_GET['RoutineName']);
		$query -> bindValue(2, $_SESSION['name']);
		$query -> execute();

		//print_r($query);
		echo "<br>";


		$query = DB::getInstance()->prep("SELECT * from routine WHERE Name = ? AND Creator= ? ");
		$query -> bindValue(1, $_GET['RoutineName']);
		$query -> bindValue(2, $_SESSION['name']);
		$query -> execute();
		$results = $query->fetchALL(PDO::FETCH_ASSOC);

		foreach($results as $result){
			$routineId = $result['RoutineId'];
			//echo "Routine Id is ".$routineId;

			for($i=1; $i<=$_GET['NumExercise']; $i++){
				if( isset($_GET["ex".$i]) && isset($_GET["Sets".$i]) && isset($_GET["Reps".$i]) && isset($_GET["Rest".$i]) ){
					$exId = $_GET["ex".$i];
					$order = $i;
					$sets = $_GET["Sets".$i];
					$reps = $_GET["Reps".$i];
					$rest = $_GET["Rest".$i];

					echo "ExerciseId is ".$exId;
					echo "<br>";
					echo "order is ".$order;
					echo "<br>";
					echo "Order ".$order;
					echo "<br>";
					echo "Sets is ".$sets;
					echo "<br>";
					echo "Reps is ".$reps;
					echo "<br>";
					echo "Rest is ".$rest;
					echo "<br>";
					echo "<br>";

				$query = DB::getInstance()->prep("INSERT into exercise2routine (RoutineId, ExerciseId, OrderInRoutine, Reps, Sets, RestBetween) VALUES (?,?,?,?,?,?) ");
				$query -> bindValue(1, $routineId);
				$query -> bindValue(2, $exId);
				$query -> bindValue(3, $i);
				$query -> bindValue(4, $reps);
				$query -> bindValue(5, $sets);
				$query -> bindValue(6, $rest);
				$query->execute();
				//print_r($query);
				

			}
		}
		}

	}

function printExercises($orderInRoutine){
	$query = DB::getInstance()->prep('SELECT * FROM exercise');
	$query -> execute();
	$results = $query->fetchALL(PDO::FETCH_ASSOC);
	if(count($results)){
		//echo "<form method='post'>";
		echo "<select name= 'ex".$orderInRoutine."' >";
		foreach ($results as $result){

			?>

			<option value="<?php echo $result['ExerciseId']; ?>"><?php echo $result['Name']; ?></option>

			<?php
		}
	}

}

?>
