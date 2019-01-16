<?php
	Class Exercise{
		private $eId, $name, $description, $image;
		private $rId, $rest, $sets, $reps,$orderNum;
		public function __construct($eId,$rId){
			$this->_rId = $rId;
			$this->_eId = $eId;
			$this->find($eId); // Populates general properties about an exercise
			$this->exerciseDetails(); // Populates properties specific to exercise in a certain routine
		}
		private function find($eId){
			$query = DB::getInstance()->prep("SELECT * FROM exercise WHERE ExerciseId = ?");
			$query->bindParam(1,$eId);
			$query->execute();
			$results=$query->fetchAll(PDO::FETCH_OBJ);

			$this->_name = $results[0]->Name;
			$this->_description = $results[0]->Description;
			$this->_image = $results[0]->Image;
		}
		public function exerciseDetails(){

			$query = DB::getInstance()->prep("SELECT * FROM exercise2routine WHERE ExerciseId = ? AND RoutineId = ?");
			$query->bindParam(1,$this->_eId);
			$query->bindParam(2,$this->_rId);
			$query->execute();
			$results = $query->fetchAll(PDO::FETCH_OBJ);
			$this->_rest = $results[0]->RestBetween;
			$this->_sets = $results[0]->Sets;
			$this->_reps = $results[0]->Reps;
			$this->orderNum = $results[0]->OrderInRoutine;
		}
		public function getName(){
			return $this->_name;
		}

		public function getImage(){
			$query = DB::getInstance()->prep("SELECT Image FROM exercise WHERE ExerciseId=?");
			$query -> bindValue(1,$this->_eId);
			$query -> execute();
			$results = $query->fetchAll(PDO::FETCH_OBJ);

			if ($results[0]->Image <> ""){
				echo "<div id='img_div'>";
				echo "<img src='exerciseImages/".$results[0]->Image."' height='40%' width='32%'>";
				echo "</div>";
			}else{
				echo "No Image Available";
			}
		}

		public function getDescription(){
			return $this->_description;
		}
		public function getSets(){
			return $this->_sets;
		}
		public function getReps(){
			return $this->_reps;
		}
		public function getRest(){
			return $this->_rest;
		}
		public function getOrderInRoutine(){
			return $this->_orderNum;
		}
		public function getExerciseId(){
			return $this->_eId;
		}

		public function getMuscles($output){ // Returns a string of muscle id seperated by commas

			$eId = $this->_eId;
			
			$query = DB::getInstance()->prep("SELECT * FROM muscle
				INNER JOIN muscle2exercise on muscle2exercise.MuscleId = muscle.MuscleId
				WHERE muscle2exercise.exerciseId=? 
				");
			$query -> bindValue(1,$eId);
			$query->execute();
			$result_array = $query->fetchall(PDO::FETCH_ASSOC);
			$stringMusclesId = "";
			$stringMusclesName = "";

			if ($output == 'id'){
				foreach($result_array as $result){
					$stringMusclesId = $result['MuscleId'].','.$stringMusclesId;
				}
				return $stringMusclesId;
			}else{ //$output ='name'
				foreach($result_array as $result){
				 $stringMusclesName = $result['Name']."<br>".$stringMusclesName;
			}
			return $stringMusclesName;
			}
			
		}
	}
?>