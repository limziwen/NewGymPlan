<?php
	Class Routine{
		private $numOfExercise, $rId, $eId, $creator, $name;
		private $exerciseArray = array();
		private $weightButton,$submitButton, $feedbackButton;
		private $recordId;
		private $lastPerformed;
		private $weight;
		private $doneBefore;
		private $musclesTrained = array();
		public $e2rId;
		private $timeTableId;
		private $prevTimeTableId;
		private $arrayMuscle = array();
		private $arrayMuscleNames =  array();

		public function __construct($rId,$timeTableId){ 
		/*
		The Routine Id will be found before creating a new instance of routine
		*/ 

			$this->findNumEx($rId);
			$this->find($rId);
			$this->_rId = $rId;;
			$this->_timeTableId = $timeTableId;
			

			for ($i=1; $i<=$this ->_numOfExercise; $i++){

				$this->findExerciseDetails($i,$rId); // Finds the exercise id given the order number and routine id
				$this->_exerciseArray[$i] = New Exercise($this->_eId, $rId); // Stores each object of exercise in an array 
				// composition aggregation
			}
			
		}
		public function printFinishRoutine(){
			// Create a form, to input whether the routine was completed and the rating
			echo "<form method='post'>";
			echo "Completed";
			echo "<input type='radio' value='Completed' name='Completed'>";
			echo "<br>";
			echo "Rating: ";
			echo "<input type ='number' placeholder ='0-10' max='10' min='0' name='Rating' >";
			echo "<br>";
			echo "<input type ='submit' value='CompletedRoutine' name='CompleteCheck'>";
			echo "</form>";

			if (isset($_POST['Completed']) && isset($_POST['Rating']) && isset($_POST['CompleteCheck'])){
				$this->completeRoutine($_POST['Rating']);
			}
		}

		private function completeRoutine($rating){

			$query = DB::getInstance()->prep("UPDATE timetable SET Completed = ?, Rating = ? WHERE UserId = ? AND RoutineId = ? AND SetDate = ?");
			$query -> bindValue (1, 1);
			$query -> bindValue(2, $rating);
			$query -> bindValue(3, $_SESSION['id']);
			$query -> bindValue(4, $this->_rId);
			$query -> bindValue(5, $this->getCurrentDate());
			$query -> execute();
			//print_r($query);
		}


		
		public function findAverageRating(){
			$rId = $this->_rId;
			$query = DB::getInstance()->prep("SELECT AVG(Rating) as average FROM timetable WHERE RoutineId=? AND Completed='1' ");
			$query -> bindValue(1,$rId);
			$query -> execute();
			$results=$query->fetchAll(PDO::FETCH_OBJ);
			return $results[0]->average;
		}

		private function find($rId){
			$query = DB::getInstance()->prep("SELECT * FROM routine WHERE RoutineId = ?");
			$query->bindParam(1,$rId);
			$query->execute();
			//print_r($query);
			$results = $query->fetchAll(PDO::FETCH_OBJ);
			$this->_name = $results[0]->Name;
			$this->_creator = $results[0]->Creator;
			
			
		}
		
		
		private function findExerciseDetails($orderNum,$rId){
		// Retrieves exerciseid
			$query= DB::getInstance()->prep("SELECT * FROM exercise2routine WHERE OrderInRoutine = '$orderNum' AND RoutineId='$rId' ");	

			$query -> execute();
			$results = $query->fetchAll(PDO::FETCH_OBJ);
			if (count($results)){
				$this ->_eId = $results[0]->ExerciseId;
			}
		}
		
		private function findNumEx($rId){
			// finds the number of exercises belonging to 1 routine
			$query= DB::getInstance()->prep("SELECT COUNT(ExerciseId) AS num FROM exercise2routine WHERE RoutineId='$rId'"); //aggrgegate function
			$query -> execute();
			$results = $query->fetchAll(PDO::FETCH_OBJ);
			$this ->_numOfExercise = $results[0]->num;
		}

		public function displayRoutine(){ 

		echo "<table border='1px'>";
			echo "<tr bgcolor='#2ECC71'>";
				echo "<td>";
				echo "Name of Routine: ";
				echo "</td>";
				echo "<td>";
				echo "<strong>".$this->_name."</strong>";
				echo "</td>";
			echo "</tr>";
			
			echo "<tr bgcolor='#AED6F1'>";
				echo "<td>";
				echo "Creator: ";
				echo "</td>";
				echo "<td>";
					echo "<i>".$this->_creator."</i>";
				echo "</td>";
			echo "</tr>";
		echo "</table>";

				// Prints a Routine, with no suggested weight/ prints suggested weight if routine is done before
				// temporary test for loop
			$count = 0;
			foreach ($this->_exerciseArray as $exercise){
				$this->_recordId = -1;
				// Record Id is set to -1 at the beginning of every new exercise
				$count = $count + 1;

				if ($this->_timeTableId<>-1){ // timetable id is -1, when the user is simply viewing the routine not in timetable

					if ($this->_doneBefore==true){
						?>
						<html>
							<table border='1px' id="routineTable">
								<tr>
									<th> Suggested Starting Weight </th>
								</tr>
						</html>
						<?php
						//SQL search for date, set, weight, uid
						//if there is a value set TRUE

						echo '<td>';
						echo "Suggested Starting Weight for ".$exercise->getName()." is : ";
						$this->generateSuggestedWeight($exercise->getExerciseId(),$exercise->getSets());
						echo '</td>';
						?>
						<html>
							</table>
						</html>
						<?php
					}
				}
					?>
					<html>
						<table border='1px' id="routineTable">
						<tr bgcolor="#666699">
							<th>Name</th>
							<th>Description</th>
							<th>Muscles</th>
							<th>Image</th>
							<th> Reps  per Set</th>
							<th> Rest Period </th>
						</tr>
					</html>
					<?php
					echo "<br>";
					echo '<tr bgcolor ="#a3a3c2">';
						echo '<td>'.$exercise->getName().'</td>'; 
						echo '<td>'.$exercise->getDescription().'</td>';
						echo '<td>'.$exercise->getMuscles('name').'</td>';
						echo '<td>'.$exercise->getImage().'</td>';
						echo '<td>'.$exercise->getReps().'</td>';
						echo '<td>'.$exercise->getRest().'</td>';
					echo '</tr>';

					if($this->_timeTableId<>-1){ // When timetableid = -1, it is when the routine is viewed from search so no feedback table

						?>
							<html>
								</table>
								<table border='1px' id="feedbackTable">
							</html>
						<?php
						echo '<br>';
						echo '<tr>';
						
						 
						for ($i=1; $i<=$exercise->getSets(); $i++){

							echo '<td>'.'Set No. '.$i;
							$this->_feedbackButton = $i."f".$exercise->getExerciseId();
							$this->_weightButton = $i."w".$exercise->getExerciseId();
							$this->_submitButton = $i."s".$exercise->getExerciseId();
							//Feedback, Weight and Submit are given a unique name, combination of set number, f/w/s, exerciseId

							?>
							<html>
								<body>
									<form method="post">
										<select name="<?php echo htmlspecialchars($this->_feedbackButton) ?>">
											<option value=1> Easy </option>
											<option value=2> Challenging </option>
											<option value=3> Too Difficult </option>
										</select>

										<?php

										// To check whether a user has performed a set, check the entry in the feedback table
										// Infomation required is 1. RecordId and 2. Set Number
										//checkInputForm checks if feedback has already been entered for a specific set 

										$this->findRecordId($exercise->getExerciseId());

										if($this->checkInputForm($i)){

											//echo "Form Checked";
											//if already inputted disable form
											$placeText = "Record Entered";
											$disabled = "disabled";

										}else{
											$placeText = "Enter Weight";

											$disabled = "required";
										}
										?>										

										Weight: <input type='text' name="<?php echo htmlspecialchars($this->_weightButton) ?>" placeholder='<?php echo $placeText ?> ' <?php echo $disabled ?>>



										<input type='submit' name="<?php echo htmlspecialchars($this->_submitButton) ?>" value='submit'  class='btn'>

										<?php
										
							// if boolean = true then echo "disabled" else echo "required"  							
							

							

							if (isset($_POST[$this->_submitButton])){

								if  ($i==1){
									// inputs data into record table once per exercise and finds recordId, needed for input feedback
									$this->inputRecordTable($exercise->getExerciseId());
								}
								$this->findRecordId($exercise->getExerciseId());

								$feedback = $_POST[$this->_feedbackButton];
								$weight= $_POST[$this->_weightButton];

								$this->inputFeedbackTable($feedback,$weight,$i);

								echo "Your suggested weight is: ";
								echo ($this->implementFeedback($feedback,$weight,$exercise->getReps())); 

							} 
							?>
							<html>
								</form>
							</body>
							</html>
							<?php 	echo '</td>';
						}

					echo '</tr>';
					?>
					<html>
						</table>
					</html>

					<?php
					}
				}

		}
		
		private function inputFeedbackTable($feedback,$weight,$currentSet){ 
			//Input Set Number, Feedback, Weight, RecordId
			$recordId = $this->_recordId;
			//echo "recordId is: ".$recordId;
			$query= DB::getInstance()->prep("INSERT INTO feedback (SetNumber, Feedback, Weight, RecordId) VALUES (?,?,?,?)");			
			//print_r($query);
			$query -> bindValue(1,$currentSet);
			$query -> bindValue(2,$feedback);
			$query -> bindValue(3,$weight);
			$query -> bindValue(4,$recordId);

			$query -> execute();
			
		}
		private function inputRecordTable($exerciseId){ 
			//Input RoutineId, UserId, ExerciseId, Date
			
			$userId = $_SESSION ['id'];

			$this->getE2RId($exerciseId);
			$query= DB::getInstance()->prep("INSERT INTO record (TimeTableId, E2RId) VALUES (?,?)");		
			$query -> bindValue(1, $this->_timeTableId);
			// timetableId is passed into new Routine from timetable class
			// it can be stored as a property in routine class and passed into this function
			$query -> bindValue(2, $this ->_e2rId);
			//e2rId can be retrieved within this class

			$query -> execute();
			//print_r($query);
		}

		private function findRecordId($exerciseId){

			$this->getE2RId($exerciseId);
			$timeTableId = $this->_timeTableId;
			$query = DB::getInstance()->prep("SELECT * FROM record WHERE E2RId = '$this->_e2rId' AND TimeTableId= '$this->_timeTableId' ");
			$query -> execute();
			$results = $query->fetchAll(PDO::FETCH_OBJ);

			//print_r($results);
			if (count($results)){
				$this->_recordId = $results[0]->RecordId;
			}else{
				$this->_recordId = -1;
			}			
		}
		private function findPreviousRecordId($exerciseId){
			$this->getE2RId($exerciseId);
			if ($this->_doneBefore == true){
				// If performed before, find previous timetable id to look for previous feedback
				$this->_prevTimeTableId = $this->getPreviousTimeTableId();
				
				$query = DB::getInstance()->prep("SELECT * FROM record WHERE E2RId = '$this->_e2rId' AND TimeTableId= '$this->_prevTimeTableId' ");
				$query -> execute();
				$results = $query->fetchAll(PDO::FETCH_OBJ);

				if (count($results)){
					$this->_recordId = $results[0]->RecordId;
				}
			}else{

			}
		}


		private function getPreviousTimeTableId(){
			//Find previous timetable id
			$uId = $_SESSION['id'];
			$query = DB::getInstance()->prep("SELECT * FROM timetable WHERE SetDate='$this->_lastPerformed' AND UserId ='$uId' AND RoutineId='$this->_rId' ");

			$query -> execute();
			$results = $query->fetchAll(PDO::FETCH_OBJ);

			//print_r($results);

			return $results[0]->TimeTableId;
			
		}
		
		public function checkRoutineDoneBefore(){ 
			
			/*
			Search TimeTable class to see if there is a record of matching UserId and RoutineId
			Take into account of date, for example if routine done too long ago, don't use data: user likely to undergo reversiblity hence cant maintain same intensity
			*/
			$rId = $this->_rId;
			$uId = $_SESSION['id'];
			$query = DB::getInstance()->prep("SELECT * FROM timetable WHERE RoutineId=? AND UserId=? AND Completed='1' GROUP BY SetDate desc");
			$query -> bindValue(1, $rId);
			$query -> bindValue(2, $uId);
			$query->execute();
			$results = $query->fetchAll(PDO::FETCH_OBJ);


			if (count($results)){
				//echo "<br>";
				$this->_lastPerformed = $results[0]->SetDate; 
				//echo " Last Performed: ".$this->_lastPerformed;
				//echo '<br>';

				$now = $this->getCurrentDate(); 

				$date1 = new DateTime($this->_lastPerformed);
				$date2 = new DateTime($now);

				$diff = $date2->diff($date1)->format("%a");

				if ($diff<=31){
					$this->_doneBefore = true;
					return true;
				}else{
					$this->_doneBefore = false;
					// if the routine was done more than a month ago return false
					return false;
				}

			}else{
				$this->_doneBefore = false;
				return false;
			}
			

		}


		private function getCurrentDate(){
			date_default_timezone_set('Singapore');
			$date = date('Y-m-d ');
			//$date = date('Y/m/d H:i:s'); Gets Hour, Minutes, Seconds
			return $date;
		}

		private function generateSuggestedWeight($eId,$sets){ 

			$challengingWeight = 0;
			$lowerLimit = 0;
			$upperLimit = 1000000;

			for ($i=1; $i<=$sets; $i++){

				$feedback = $this->getFeedback($eId,$sets,$i);

				if ($feedback==2){ // 2 = challenging 
					if ($challengingWeight<$this->_weight){
						$challengingWeight = $this->_weight;
						// Sets the highest possible challenging weight
					}
				}elseif($feedback == 1){ // 1 = too easy
					if($lowerLimit < $this->_weight){
						$lowerLimit = $this->_weight;
						// Sets the highest possible easy weight
					}
				}else{ //feedback = too difficult
					if($upperLimit > $this->_weight){
						$upperLimit = $this->_weight;
						// Sets the lowest possible too difficult weight
					}
				}

			}
			if ($challengingWeight > 0){
				echo $challengingWeight."kg";
			}elseif($lowerLimit<$upperLimit){
				if($upperLimit<1000000 && $lowerLimit>0){
					echo "Between ".$upperLimit."kg and".$lowerLimit."kg";
				}elseif($lowerLimit==0){
					echo "Below ".$upperLimit."kg";
				}else{
					echo "Above ".$lowerLimit."kg";
				}

			}else{
				echo "No Weight can be suggested";
			}
			

		}
		private function getFeedback($eId,$setNo,$currentSet){ 

			// Use last performed date, eId, userId, routineId to find RecordId
			// Use RecordId, SetNumber to find Weight AND Feedback

			$this->findPreviousRecordId($eId); //Finds previous recordid to find previous feedback

			$query = DB::getInstance()->prep("SELECT * FROM feedback WHERE recordId='$this->_recordId' AND SetNumber='$currentSet' ");
			$query->execute();
			$results = $query->fetchAll(PDO::FETCH_OBJ);
			if (count($results)){
				$feedback = $results[0]->Feedback;
				$this->_weight = $results[0]->Weight;
				/*
				echo "Your feedback was ".$feedback;
				echo "<br>";
				echo "Weight was".$this->_weight;
				*/
				return $feedback;
			}
			
		}

		private function implementFeedback($feedback,$weight,$reps){
			//generates a suggested weight based on the feedback
			// For exercises with a rep range of 6 or below, add increments of 5kg and decrease by 2.5kg
			// For exercises with a rep range greater than 6, change by increments of 2.5kg
			if($feedback == 2){
  				return $weight;
			}elseif($feedback == 1){
  				if ($reps <=6){
     				$SuggestedWeight = $weight + 5;
  				} elseif ($reps > 6){
    				$SuggestedWeight = $weight + 2.5;
  				}
  				
			}elseif($feedback == 3){
				if ($reps <=6){
     				$SuggestedWeight = $weight - 2.5;
  				} elseif ($reps > 6){
    				$SuggestedWeight = $weight - 2.5;
  				}
			}
			return $SuggestedWeight;
   					
		}
		public function setDetails(){

			// Gets Muscles Trained in a list seperated by commas 
			$muscleString = $this->getMusclesTrained();


			// Turn string seperated by commas into array
			$this->_arrayMuscle = explode(',', $muscleString);

			// Make sure all values in array are unique, so only 1 muscle of each type can be outputted
			$this->_arrayMuscle = array_unique($this->_arrayMuscle);

			$count = -1;

			foreach($this->_arrayMuscle as $muscle){
				$count = $count +1;

				$query = DB::getInstance()->prep("SELECT Name FROM muscle WHERE MuscleId=?");
				$query -> bindValue(1, $muscle);
				$query -> execute();
				$results = $query->fetchAll(PDO::FETCH_OBJ);

				$this->_arrayMuscleNames[$count] = $results[0]->Name;
				
			}

		}
		public function getRoutineName(){
			return $this->_name;
		}

		public function getMusclesTrained(){
		// using muscle 2 exercise, input the muscles trained into the array , ensuring no repeats
		// each exercise class holds the property of muscles trained
			$count = 0;

			foreach($this->_exerciseArray as $exercise){

				$this->musclesTrained[$count] = $exercise->getMuscles('id'); 

				// Array holding a string of muscles id seperated by commas, with each position in the array holding infomation about the muscles trained for that corresponding exercise
				$count = $count + 1;
			}
			$muscleString="";

			for($i=0; $i<$this->_numOfExercise;$i++){
				$muscleString = $this->musclesTrained[$i].$muscleString;
				// Turns array into a long string of muscle ids, which end in a comma
			}

			// Remove last comma
			$length = strlen($muscleString);
			$muscleString = substr_replace($muscleString,"",$length-1,$length);
			

			return $muscleString;

			

		}
		private function getE2RId($exerciseId){
			$query = DB::getInstance()->prep("SELECT * FROM exercise2routine WHERE RoutineId = ? AND ExerciseId = ?");
			$query -> bindValue(1, $this->_rId);
			$query -> bindValue(2, $exerciseId);
			$query -> execute();
			$results = $query->fetchAll(PDO::FETCH_OBJ);
			//print_r($results);
			$this->_e2rId = $results[0]->E2RId;			

		}

		public function getLastPerformedDate(){
			return $this->_lastPerformed;
		}

		public function getRoutineId(){
			return $this->_rId;
		}

		public function routinePerformedBefore(){
			return $this->_doneBefore;
		}
		public function checkMuscle($muscleSearch){
		//Checks if muscle is in routine
			foreach($this->_arrayMuscleNames as $muscle){
				if($muscle == $muscleSearch){
					return true;
				}
			}
			return false;
		}
		public function getMusclesRoutine(){

			foreach($this->_arrayMuscleNames as $muscle){
				echo "<br>";
				echo $muscle;

			}
		}

		private function checkInputForm($setNumber){

			if ($this->_recordId<>-1){
				//if recordId has been set, set 1 has been performed 
				//recordid must be used to deterine if other sets have been performed


				$query = DB::getInstance()->prep("SELECT * FROM feedback WHERE RecordId = ? AND SetNumber = ?");
				// NEED to use RECORDID 
				// ERROR Disables future forms 

				$query -> bindValue(1, $this->_recordId);
				
				$query -> bindValue(2, $setNumber);

				$query -> execute();

				$results = $query ->fetchAll(PDO::FETCH_OBJ);
				

				if (count($results)){
					//echo "Set ".$setNumber. "has been performed";

					return true;
				}else{
					//echo "Set ".$setNumber." has not been performed";
					return false;
				}

			}else{

				//echo "Record Id not set";
				return false;
			}
			

		}

	}

?>