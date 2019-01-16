<!DOCTYPE>
<html>
<link rel='stylesheet' type='text/css' href='Style.css'>
<div id = "topBanner"> 
<h1>Profile</h1>
</div>

</html>


<?php
/*
Create should allow Users to create a routine, by providing name, exercises, sets, reps drop down list
*/
ini_set('mysql.connect_timeout',300);
ini_set('default_socket_timeout',300);

require_once"core/init.php";
include('ColumnButtons.html');
$user = new User();

	if(!$user->isLoggedIn()){
		header('Location: index.php');
	}
	
$firstName = $user->getFirstName();
$lastName = $user->getLastName();
$dOB = $user->getDateOfBirth();
$email = $user->getEmail();

if(isset($_POST['change'])){
	
	$oldPass = $_POST['OldPass'];
	$newPass = $_POST['NewPass'];
	$confirmNewPass=$_POST['ConfirmNewPass'];
	echo $oldPass;
	echo "<br>";
	echo $newPass;
	echo "<br>";
	echo $confirmNewPass;
	echo "<br>";

	if($newPass == $confirmNewPass){ //double entry validation 
		if (!empty($oldPass) && !empty($newPass) && !empty($confirmNewPass)){ //entry validation
			$user->changePassword($oldPass, $newPass, $email);
		}
		
	}else{
		echo "Please enter new password correctly!";
	}
}

// PROFILE PICTURE 

$uId = $_SESSION['id'];
if(isset($_POST['upload'])){
	$msg="";
	
	// the path to store the upload image
	$target = "profileImages/".basename($_FILES['image']['name']);
	//File paths parameterised

	$image = $_FILES['image']['name'];
	$query = DB::getInstance()->prep("UPDATE users SET Image = ? WHERE UserId= ?");
	$query -> bindValue(1, $image);
	$query -> bindValue(2, $uId);
	$query -> execute();

	if (move_uploaded_file($_FILES['image']['tmp_name'], $target)){
		$msg = "Upload Succesful";
		echo $msg;
	}else{
		$msg = "There was a problem";
		echo $msg;
	}

}

$query = DB::getInstance()->prep("SELECT Image FROM users WHERE UserId=?");
$query -> bindValue(1,$uId);
$query -> execute();
$results = $query->fetchAll(PDO::FETCH_OBJ);
if ($results[0]->Image <> ""){
	echo "<div id='img_div'>";
	echo "<img src='profileImages/".$results[0]->Image."' height='40%' width='32%'>";
	echo "</div>";
}


?>
<html>
<div id = 'mainContent'>

<div id = 'subtitle'>
Personal Details 
</div>
First Name: <?php echo $firstName; ?> </br>
Last Name: <?php echo $lastName; ?></br>
Date of Birth: <?php echo $dOB; ?> </br>
Email Address: <?php echo $email; ?></br>
</br>
To change password please enter original password</br>
<form method='post' >
Old Password: <input type='password' name='OldPass' placeholder='Old Password'/></br>
New Password <input type='password' name='NewPass' placeholder='New Password'/> </br>
Confirm New Password <input type='password' name='ConfirmNewPass' placeholder='New Password'/> </br>
<input type = 'submit' name = 'change' value='change'/>
</form>
<form method="post" action = "Profile.php" enctype="multipart/form-data">
	<input type="hidden" name="size" value=1000000>
	<br/>
		<input type="file" name="image">
	<br/><br/>
	<input type="submit" name="upload" value="Upload Image"/>
</form>



</div>
</html>