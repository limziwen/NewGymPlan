<?php
	require_once('core/init.php');
	$user = new User();
	if(isset($_POST['Register'])){
		
		$dbConn = DB::getInstance();
		
		$firstName = $_POST["FirstName"];
		$lastName = $_POST["LastName"];
		$email = $_POST["Email"];
		$dob = $_POST["DateOfBirth"];
		$salt = mcrypt_create_iv(32);
		$password = $_POST["Password"];
		$confirmPassword = $_POST["confirmPassword"];
		
		if($password == $confirmPassword) {
			
			$password = hash("sha256", $password, $salt);
			
			if(!empty($firstName) && !empty($lastName) && !empty($email) && !empty($password)) {
					
				$query = $dbConn -> prep("INSERT INTO users (FirstName, LastName, Email, DateOfBirth, Salt, Password ) VALUES(?,?,?,?,?,?)");
				// takes the input and changes the records in the data base for that specific user
				$query -> bindValue(1, $firstName);
				$query -> bindValue(2, $lastName);
				$query -> bindValue(3, $email);
				$query -> bindValue(4, $dob);
				$query -> bindValue(5, $salt);
				$query -> bindValue(6, $password);
				$query -> execute();
				
				header("Refresh: 1; url = home.php?");
				
			} else {
				
			echo "<p align=center>You have not properly filled the form</p>";		

			}						
				
			} else {
				
			echo "Password aren't  the same!";
			
			}
		
		//create worker account then log worker in.
		
		
	}

?>
<!DOCTYPE html>
<html>
	<body>
		<link rel='stylesheet' type='text/css' href='Style.css'>
	<div id='topBanner'>
		<h1> REGISTER PAGE </h1>
	</div>
	<div id ='backgroundImage'>
		<form method='post' input align = "center">
			<input type='text' name='FirstName' placeholder='FirstName' required> <br>
			<input type='text' name='LastName' placeholder='LastName' required> <br>
			<input type='email' name='Email' placeholder='Email' required> <br>
			<input type='date' name='DateOfBirth' placeholder='DateOfBirth' required> <br>
			<input type='password' name='Password' placeholder='Password' required> <br>
			<input type='password' name='confirmPassword' placeholder='Confirm Password' required> <br>
			<input type = "submit" name = "Register" value = "Register" class='btn'/>
			<br>
			<br>
		</form>
		<form method = "post" action = "index.php" align="center">
		<input type = "submit" value="Return to Log In" class='btn'></p>
		</form>
	</div>
	</body>
</html>