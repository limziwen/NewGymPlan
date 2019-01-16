<?php
	require_once('core/init.php');
	$user = new User();
	if(isset($_POST['createOwner'])){
		
		$dbConn = DB::getInstance();
		
		$name = $_POST["name"];
		$email = $_POST["email"];
		$salt = mcrypt_create_iv(10);
		$password = $_POST["password"];
		$confirmPassword = $_POST["confirmPassword"];
		
		if($password == $confirmPassword) {
			
			$password = hash("sha256", $password, $salt);
			
			if(!empty($name) && !empty($email) && !empty($password)) {
					
				$query = $dbConn -> prep("INSERT INTO users (first_name, email, password, salt, type) VALUES(?, ?, ?, ?, ?)");
				// takes the input and changes the records in the data base for that specific user
				$query -> bindValue(1, $name);
				$query -> bindValue(2, $email);
				$query -> bindValue(3, $password);
				$query -> bindValue(4, $salt);
				$query -> bindValue(5, 2);

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
		<form method='post'>
			<input type='text' name='name' placeholder='Name' required>
			<input type='email' name='email' placeholder='Email' required>
			<input type='password' name='password' placeholder='Password' required>
			<input type='password' name='confirmPassword' placeholder='Confirm Password' required>
			<input type='submit' name='createOwner' value='Create Account'>
		</form>
	</body>
</html>