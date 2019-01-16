<?php
Class Search{
	private $routineTemp = array();
	private $sortVar; 


	private function sortRating(){
		// Sorts routineTemp() based on sortVar in descending order
		// Highest Rated First / Most Recently Performed First
		for ($i=0;$i<count($this->_routineTemp);$i++){
			for ($j=0;$j<count($this->_routineTemp)-1;$j++){
				if($this->_routineTemp[$j]->findAverageRating()<$this->_routineTemp[$j+1]->findAverageRating()){ 
					$temp = $this->_routineTemp[$j];
					$this->_routineTemp[$j]=$this->_routineTemp[$j+1];
					$this->_routineTemp[$j+1]=$temp;

				}
			}
		}
	}

	private function sortDate(){  

		for ($i=0;$i<count($this->_routineTemp);$i++){

			for ($j=0;$j<count($this->_routineTemp)-1;$j++){

				if ($this->_routineTemp[$j]->checkRoutineDoneBefore() == true && $this->_routineTemp[$j+1]->checkRoutineDoneBefore() == true){
					
					if(strtotime($this->_routineTemp[$j]->getLastPerformedDate()) < strtotime($this->_routineTemp[$j+1]->getLastPerformedDate())){ 

						$temp = $this->_routineTemp[$j];
						$this->_routineTemp[$j]=$this->_routineTemp[$j+1];
						$this->_routineTemp[$j+1]=$temp;

					}

				}elseif($this->_routineTemp[$j]->checkRoutineDoneBefore() == true && $this->_routineTemp[$j+1]->checkRoutineDoneBefore() ==false){
					// do nothing, allow done routine to be ahead of one which is not done
				}elseif($this->_routineTemp[$j]->checkRoutineDoneBefore() == false && $this->_routineTemp[$j+1]->checkRoutineDoneBefore() ==true){
					// switch it around

					$temp = $this->_routineTemp[$j];
					$this->_routineTemp[$j]=$this->_routineTemp[$j+1];
					$this->_routineTemp[$j+1]=$temp;

				} // else $this->_routineTemp[$j] == false && $this->_routineTemp[$j+1]==false 
				

			}
		}

	}

	public function searchRoutine($name, $sortVar, $searchParam){
		$query = DB::getInstance()->prep("SELECT * FROM routine WHERE  $searchParam LIKE '%$name%'");
		$query -> execute();
		print_r($query);
		$results= $query->fetchALL(PDO::FETCH_ASSOC);

		if(count($results)){
			$count = -1;
			$this->_sortVar = $sortVar;
			foreach($results as $result){
				$count = $count + 1;
				
				$routineId = $result['RoutineId']; 

				$this->_routineTemp[$count] = new Routine($routineId,-1); // Association Aggrgegation
				
			}

		//$this->mergeSort($this->_routineTemp);
			if ($sortVar == 'Rating'){
				$this->sortRating();
			}else{

				$this->sortDate();
			}
			
			for($i=0; $i<=$count; $i++){
				echo "<br>";
				$this->_routineTemp[$i] ->displayRoutine();
				echo "<br>";

				echo "<table border='1px'>";
				echo "<tr>";
					echo "<td>";
					echo "Average rating is: ";
					echo "</td>";

					echo "<td>";
					echo $this->_routineTemp[$i]->findAverageRating();
					echo "<br>";
					echo "</td>";
				echo "</tr>";

					if ($this->_routineTemp[$i]->checkRoutineDoneBefore()==true){

						echo "<tr>";
							echo "<td>";
								echo "Last performed date ";
							echo "</td>";
							echo "<td>";
								echo $this->_routineTemp[$i]->getLastPerformedDate();
							echo "</td>";
						echo "<br>";
						echo "</tr>";


					}else{

						echo "<tr>";
							echo "<td>";
								echo "Never performed before ";
							echo "</td>";
						echo "</tr>";
					}

				echo "</table>";
				echo "<br>";



			}	


		}else{
			return false;
		}

		
		

	}

	public function searchRoutineMuscle($muscleSearch, $sortVar){
		$this->_sortVar = $sortVar;
		$query = DB::getInstance()->prep("SELECT * FROM routine");
		$query -> execute();
		$results = $query -> fetchALL(PDO::FETCH_ASSOC);
		$muscleInRoutine = false;

		if(count($results)){
			$count = -1;
			foreach($results as $result){
				
				$routineId = $result['RoutineId'];
				$tempRoutine = new Routine($routineId, -1);
				$tempRoutine -> setDetails();
				

				if($tempRoutine->checkMuscle($muscleSearch)){ // See if muscle is in routine
					$muscleInRoutine = true;
					$count = $count + 1;
					$this->_routineTemp[$count] = new Routine($routineId, -1);

				}				
			}
			if ($sortVar == 'Rating' && $muscleInRoutine == true){
				$this->sortRating();
			}elseif ($sortVar == 'DatePerformed' && $muscleInRoutine == true){
				$this->sortDate();
			}else{
				echo "<br>";
				echo "No results found ";
				echo "<br>";
			}
			
			for($i=0; $i<=$count; $i++){
				echo $result['Name'];
				$this->_routineTemp[$i] ->displayRoutine();
			}

		}
	}



	/*
	private function mergeSort($arrayToSort){
		

		$len = count($arrayToSort);


    	if($len==1){
        	return $arrayToSort;
    	}

    	$mid = intval($len/2);

    	echo "Length is ".$len;
    	echo "Mid is ".$mid;

    	// Recursion

    	$this->_left = $this->mergeSort(array_slice($arrayToSort, 0, $mid)); //Takes first half of list, all numbers from 0 to mid value are taken
    	

    	$this->_right = $this->mergeSort(array_slice($arrayToSort, $mid)); //Takes second half of list, all numbers up to and including mid are removed

    	return $this->merge($this->_left, $this->_right);

	}

	private function merge(&$lF, &$rF){  
		//& means passing by reference, which means the actual variable is passed, not just a copy.
    	$result = array();  
   		// while both arrays have something in them  
    	while (count($lF)>0 && count($rF)>0) {  
        	//if ( $lF[0] <= $rF[0] ) {   Compare $this-> sortVar
        	if ($this->compareSortVar($lF, $rF)){ 
            	array_push($result, array_shift($lF));  
            	// array push adds an element to back of array taking in the (array, value to be added)
            	// array shift removes first element of array, and returns the remaining
        	}else{  
            	array_push($result, array_shift($rF));  

        	}  
    	}  

    array_splice($result, count($result), 0, $lF);  
    array_splice($result, count($result), 0, $rF);  

    return $result;  
}  

private function compareSortVar($lF, $rF){
		// is lf<=rf ?
		//Compares the two single item arrays
		//retrieve the data from array
	foreach($lF as $routine){
			$leftRating = $routine->findAverageRating();
			echo "<br>";
			echo "Rating for: ".$routine->getRoutineName()." is ".$leftRating;

			$routine->checkRoutineDoneBefore();
			if($routine->routinePerformedBefore()){
				//get last peformed date
				$leftDate = $routine->getLastPerformedDate();

			}else{
				//echo "Routine Id: ".$routine->getRoutineId()." never performed";
				$leftDate = '-1';
			}
		}

	foreach($rF as $routine){
			$rightRating = $routine->findAverageRating();
			echo "<br>";
			echo "Rating for: ".$routine->getRoutineName()." is ".$rightRating;

			$routine->checkRoutineDoneBefore();
			if($routine->routinePerformedBefore()){
				//get last peformed date
				$rightDate = $routine->getLastPerformedDate();
			}else{
				//echo "Routine Id: ".$routine->getRoutineId()." never performed";
				$rightDate = '-1';
			}
		}

	if($this->_sortVar == 'Rating'){
		if($leftRating > $rightRating){ //Highest Rating is placed first, so unlike traditional merge sort where smallest is first, the largest number is place first
			return true;
		}else{
			return false;
		}
	}

	if($this->_sortVar = 'DatePerformed'){
		if(strtotime($leftDate) > strtotime($rightDate)){ //Left Date is more current than right date, most current date goes first (so like the rating) it places the largest value first
			return true;
		}else{
			return false;
		}

	}
}
 


}
*/
	}
?>