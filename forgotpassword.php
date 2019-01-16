
<?php
/*
1.	User requests change password 
2.	Email sent to user, with a code. Code can be generated using a salt on their current password
User enters the code. If match allow user to overwrite their current password
*/
	require_once('core/init.php');
	if (isset($_POST['returnIndex'])){
		if (isset($_SESSION['id']) or isset($_SESSION['code'])){
			$_SESSION['id'] = null;
			$_SESSION['code']=null;
		}
		header("Location: index.php");
		
	}
	
	if (isset($_POST['Send'])){
		$email = $_POST['Email'];
		if(checkEmailExists($email)){
			$message = generateRecoveryCode();
			$_SESSION['code']=$message;
			$subjectTitle = "Forgetten Password Recovery Code";
			$from = "GymPlanner@coursework.com";

			$email = new EmailObj($email,'forgot');
			$email->createBody($message);

			echo "<form method='post'>";
			echo "<input type='text' name='code' placeholder='enter code' required>";

			echo "<input type = 'submit' name = 'enter' value = 'enter' class='btn'/>";
			echo "</form>";
		}else{
			echo "No such email exists";
		}
	}

	if (isset($_POST['enter'])){ // problem here

				$code = $_POST['code'];
				echo $code;
				echo "enter is pressed";

			

				//if($code == '1111'){ // 1111 is used as a dummy code as email cannot be sent
				if($code = $_SESSION['code']){
					// User is verfied without needing his password
					

					echo "<form method='post'>";
					echo "<input type='text' name='newPassword' placeholder='New Password' required>";
					echo "<input type = 'submit' name = 'newPasswordSubmit' class='btn'/>";
					echo "</form>";

				}

	}

	if (isset($_POST['newPasswordSubmit'])){
		echo $_SESSION['id'];
		$user = new User ($_SESSION['id']);
		$user -> resetPassword($_POST['newPassword'], $user ->getEmail());
	}
/*
function changePassword($email){
	

	if (isset($_POST['newPasswordSubmit'])){
		if ($_POST['newPassword'] == $_POST['newPassword2']){

			$salt = mcrypt_create_iv(32);
			$password = hash("sha256", $_POST['newPassword'], $salt);
			$query = DB::getInstance() -> prep("UPDATE users SET Password = ? WHERE Email= ?");
			$query -> bindValue(1, $password);
			$query -> bindValue(2, $email);

			$query -> execute();
			echo "Password Successfully changed! ";

			header("Location: Index.php");
		}else{
			echo "Please try again, you have inputted it incorrectly ";
		}
	}

}
*/
function checkEmailExists($email){
	$query = DB::getInstance()->prep("SELECT * FROM users WHERE Email = ?");
	$query -> bindValue(1,$email);
	$query -> execute();
	$results = $query->fetchAll(PDO::FETCH_OBJ);
	//print_r($results);
	if ($results){
		$_SESSION['id'] = $results[0]->UserId;
		return true;
	}else{
		return false;
	}
}

function generateRecoveryCode(){
	$randomNum = random_int(100000, 999999);

	return $randomNum;

}



?>

<!DOCTYPE html>
<html>
	<body>
		<link rel='stylesheet' type='text/css' href='Style.css'>

	<div id='topBanner'>
		<h1> Enter Email to Send Recovery Password </h1>
	</div>

	<div id ='backgroundImage'>
		<form method='post' input align = "center">
			<input type='email' name='Email' placeholder='Email' required> <br>
			<input type = "submit" name = "Send" value = "Send" class='btn'/>
			<br>
			<br>
		</form>
		<form method = "post" align="center">
		<input type = "submit" value="Return to Log In" name='returnIndex' class='btn'></p>
		</form>
	</div>

	</body>
</html>


