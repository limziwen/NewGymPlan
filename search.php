<html>
<link rel='stylesheet' type='text/css' href='Style.css'>
<div id = "topBanner"> 
<h1>Search</h1>
</div>
</html>
<?php
/*
Search Page should search for  a routine in the database
Give user options: search by rating/muscle trained
Display Results by showing matching words
Allow routine to be shown by clicking on name
Allow user to have the option to "Add Routine" in to Time Table and/or Log
*/

require_once "core/init.php";
include('ColumnButtons.html');

$newSearch = new Search;

if( isset ($_POST['submitSearch'])){
	$sortVar = $_POST['sort'];
	echo "<br>";
	echo "Sorted by: ".$sortVar;
	echo "<br>";
}
if( isset ($_POST['submitSearch'])){
	$searchVar = $_POST['search'];
	echo "Searching for ".$searchVar;
	echo "<br>";
	$searchInput = $_POST['SearchInput'];

	if ($searchVar == "Name" or $searchVar == "Creator"){
		$newSearch -> searchRoutine($searchInput, $sortVar, $searchVar);
	}elseif ($searchVar == "Muscle"){		
		echo "Searching for muscle".$searchInput;
		$newSearch -> searchRoutineMuscle($searchInput, $sortVar);
		
	}
	
}




?>
<html>
<table border = '1px' id='searchTable'>
	<th>
		<form method="post">
			Sort by:
			<select name="sort"">
				<option value="Rating">Rating</option>
				<option value="DatePerformed">DatePerformed</option>
			</select>
			
			<br>
			<br>

			Search By:
			<select name="search"">
				<option value="Name">Name</option>
				<option value="Creator">Creator</option>
				<option value="Muscle">Muscle</option>
			</select>

			
			<input type='text' name='SearchInput' placeholder='Search input'>
			

			<input type='submit' name='submitSearch' class='btn' value="Search">
		</form>
	</th>
</table>

</html>