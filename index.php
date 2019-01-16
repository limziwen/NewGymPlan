<?php
	require_once('core/init.php');
	$user = new User();
	if($user->isLoggedIn()){
		header('Location: home.php');
	}
	if(isset($_POST['login'])){
		
		if($user->login($_POST['email'], $_POST['password'])){
		
			header('Location: home.php');
		}
	}	
?>
<html>
<head> <link rel='stylesheet' type='text/css' href='Style.css'> </head>
	<body align = "center">

	<title>
	Gym Planner 
	</title>

		<div id='topBanner'>
			<h1> GYM PLANNER </h1>
		</div>
	
	<div id ='backgroundImage'>
		<div id ='login'>
		Please log in with your Gym Planner account
		</div>

		<form method='post'>
			Username: <input type='text' name='email' placeholder='Email' required>
			Password: <input type='password' name='password' placeholder='Password' required>
			<input type='submit' name='login' value='Login'  class='btn'>
			
		</form>
		<form method = "post" action = "register.php" >
		Click here to register: <input type = "submit" value="register" class='btn'></p>
		</form>
		
		<form method = 'post' action='forgotpassword.php'>
		<input type = 'submit' value = 'Forgot Password' class = 'btn'>

	</div>
	</body>
</html>

