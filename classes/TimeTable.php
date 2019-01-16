<?php
	class TimeTable{
		private $daysArray = array();
		private $dateArray = array();

		public function __construct(){
			$this->generateTimeTable();
			$this->displayAddRoutine();
			$this->displayRemoveRoutine();
			
			if(isset($_POST['AddRoutine'])){
				$date = $_POST['date'];
				$routineId = $_POST['routine'];
				$time = $_POST['time'];
				echo $date;
				echo "<br>";
				echo $routineId;
				echo "<br>";
				echo $time;
				echo "<br>";
				// Checks if there is a routine on that day
				$query = DB::getInstance()->prep("SELECT * FROM timetable WHERE SetDate=? AND UserId=?");
				$query -> bindValue(1,$date);
				$query -> bindValue (2, $_SESSION['id']);
				$query -> execute();
				//print_r($query);
				echo "<br>";
				$results = $query->fetchAll(PDO::FETCH_OBJ);
				//print_r($results);
				
				if(count($results)){
					
					echo "There is already a routine, please remove it before adding a new one. ";
				}else{
					echo "You have added a routine on".$date;
					$this->insertTimetableDay($date, $routineId, $time);
				}
			}

			if(isset($_POST['RemoveRoutine'])){
				$date = $_POST['date'];
				echo $date;
				$query = DB::getInstance()->prep("SELECT * FROM timetable WHERE SetDate=? AND UserId =?");
				$query -> bindValue(1,$date);
				$query -> bindValue (2, $_SESSION['id']);
				$query -> execute();
				echo "<br>";
				//print_r($query);
				echo "<br>";
				$results = $query->fetchAll(PDO::FETCH_OBJ);
				//print_r($results);
				
				if(count($results)){
					echo "Removed routine on ".$date;
					$this->removeTimetableDay($date);
				}else{
					echo "There is no routine on this day!";
				}
				
			}
			

		}
		private function removeTimetableDay($date){
			$query = DB::getInstance()->prep("DELETE FROM timetable WHERE SetDate=? AND UserId=?");
			$query -> bindValue(1,$date);
			$query -> bindValue(2,$_SESSION['id']);
			$query -> execute();
		}
		private function displayRemoveRoutine(){
			
			?>
			<form method="post">
				<select name= "date">
						<?php
							for ($i=0 ; $i<=6; $i++){
								echo "<option value= ";
								echo $this->dateArray[$i];
								echo ">";
								echo $this->dateArray[$i];
								echo "</option>";
							}	
						?>
						<input type='submit' name='RemoveRoutine' class='btn' value="Remove Routine">	
					</select>
			</form>
			<?php
		}

		private function insertTimetableDay($date, $routineId, $time){
			$query = DB::getInstance()->prep("INSERT into timetable (UserId, RoutineId, SetTime, SetDate) VALUES (?,?,?,?)");
			$query -> bindValue(1, $_SESSION['id']);
			$query -> bindValue(2, $routineId);
			$query -> bindValue(3, $time);
			$query -> bindValue(4, $date);
			$query -> execute();
		}

		public function displayAddRoutine(){
			?>

				<form method="post">

					<?php

						$query = DB::getInstance() -> prep("SELECT * FROM  routine ");
						$query -> execute();
						$results = $query->fetchALL(PDO::FETCH_ASSOC);
						echo "<select name='routine'>";
							if(count($results)){
								foreach ($results as $result){
								
								echo "<option value=" ;
								echo $result['RoutineId']; 
								echo ">";
								echo "Name: ".$result['Name']; 
								echo "</option>";
							}
						echo "</select>";
						}
					?>	

					<select name= "date">
						<?php
							for ($i=0 ; $i<=6; $i++){
								echo "<option value= ";
								echo $this->dateArray[$i];
								echo ">";
								echo $this->dateArray[$i];
								echo "</option>";
							}	
						?>	
					</select>
				
					Time: <input type='time' name='time' required>
					<input type='submit' name='AddRoutine' class='btn' value='Add Routine'>
				</form>
			<?php
		}

		private function generateTimeTable(){
			$this->_daysArray= array('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday');
			$this->populateDates();
			?>
			<table border = '1px'>
			<tr>
			<?php
			for($i='0'; $i<='6'; $i++){
						
						echo "<br>";
						if ($this->dateArray[$i] == $this->getDate()){
							echo "<th bgcolor = '#66ffff'>"; //Prints today's date in blue 
						}else{
							echo "<th>";
						}
						echo $this->dateArray[$i];
						echo "<br>";
						echo $this->_daysArray[$i];
						echo "</th>";
			}
			echo "</tr>";
			echo "<tr>";

			for($i='0'; $i<='6'; $i++){
				if ($this->checkRoutinePresent($this->dateArray[$i])){
				// print Routine Name, Time Started, Muscle Groups 

				}else{
				// prints empty cell 

					echo "<td width =250px>";
					echo "<br>";
					echo " No Routine ";
					echo "</td>";
				}
			}
			echo "</tr>";
			echo "</table>";
		}
		private function checkRoutinePresent($date){
			$uId = $_SESSION['id'];
			$query = DB::getInstance()->prep("SELECT * FROM timetable WHERE UserId= '$uId' AND SetDate= '$date'");
			//print_r($query);
			$query -> execute();
			echo "<br>";
			//print_r($query);
			$results = $query->fetchAll(PDO::FETCH_OBJ);


			if (count($results)){
				// Print Routine Details For That Day

				echo "<td width =250px>";
				echo "<br>";
				echo "Set Time: ".$results[0]->SetTime;
				echo "<br>";
				echo "Set Date: ".$results[0]->SetDate;
				echo "<br>";


				$routineId = $results[0]->RoutineId;

				$timeTableId = $this->findTimeTableId($date,$uId,$routineId);

				$routine = New Routine($routineId,$timeTableId); // Association Aggregation 
				
				$routine->setDetails(); 
				echo "<strong>".$routine->getRoutineName()."</strong>";
				echo $routine->getMusclesRoutine();
				
				echo "<br>";
				echo "Average Rating: ".$routine->findAverageRating($routineId);

				if ($results[0]->Completed == 1){
					echo "<br>";
					echo "<strong>";
					echo "Routine Completed! ";
					echo "</strong>";
					echo "<br>";
				}else{
					echo "<br>";
					echo "<strong>";
					echo "Yet to do ";
					echo "</strong>";
					echo "<br>";
				}

				echo "</td>";

				return true;
			}

		}
		private function findTimeTableId($date,$uId,$routineId){
			$query = DB::getInstance()->prep("SELECT * FROM timetable WHERE UserId=? AND SetDate=? AND RoutineId=? ");
			$query -> bindValue(1,$uId);
			$query -> bindValue(2,$date);
			$query -> bindValue(3,$routineId);
			$query -> execute();
			$results = $query->fetchAll(PDO::FETCH_OBJ);
			return $results[0]->TimeTableId;
		}

		private function populateDates(){
			// Gets the date of the specific week day e.g Wednesday = 4th November
			$currentDate = $this->getDate();
			$day = date('N', strtotime($currentDate)); //Mon = 1 Sun = 7

			$this->dateArray[$day-1]=$currentDate;



			for($i=0;$i<$day-1;$i++){
				$daysDiff = $day - $i-1;
				// Subtract Current Date and days Diff
				$newDate = date('Y-m-d',strtotime('-'.$daysDiff.'day', strtotime($currentDate)));
				$this->dateArray[$i] = $newDate;
			}

			for($i = $day; $i<=6; $i++){
				$daysDiff = $i - $day + 1;
				// Add Current Date and days Diff
				$newDate = date('Y-m-d',strtotime('+'.$daysDiff.'day', strtotime($currentDate)));
				$this->dateArray[$i] = $newDate;

			}

		}
		private function getDate(){
			//date_default_timezone_get();
			date_default_timezone_set('Singapore');
			$date = date('Y-m-d ');
			
			return $date;
		

		}

	}

?>