<?php
	Class User{

		protected $firstName, $lastName, $dateOfBirth, $email, $uID;
		public function __construct($id = null){
		// if no arguement is given to construct, $id is given the default value of null

			//if(!isset($id)){
				//check if logged in, if they are give $id their id as a value.
				
				if($this->isLoggedIn()){
					$id = $_SESSION['id'];
					if (isset($id)){
						$this->find($id);
					}
				}
				
				
			//}
			//use find to retrieve the user's real data.
		}
		public function logOut(){
			session_start();
			session_destroy();
			session_unset();
			header('Location: index.php');
		}
		public function isLoggedIn(){
			return isset($_SESSION['id'])? true: false;	
			// Checks if user id is stored in session

		}
		protected function find($id){
			$query = DB::getInstance()->prep("SELECT * FROM users WHERE UserId = ?");
			$query->bindParam(1,$id);
			$query->execute();
			$results=$query->fetchAll(PDO::FETCH_OBJ);
			
			$this->firstName = $results[0]->FirstName;
			$this->email = $results[0]->Email;
			$this->lastName = $results[0]->LastName;
			$this->dateOfBirth = $results[0]->DateOfBirth;
			$this->email = $results[0] ->Email;
			$this->salt =$results[0]->Salt;
			$this->uID = $id;
			//code to populate properties.

		}

		public function login($email, $password){
			// Find user in database using their email, retrive corresponding salt
			// Add salt to password, and hash iterator_apply
			// Compare hashed password with database
			$query = DB::getInstance()->prep("SELECT * FROM users WHERE Email = ?");


			$query->bindValue(1,$email);


			$query->execute();

			$results=$query->fetchAll(PDO::FETCH_OBJ);

			if(count($results)){
				//var_dump($results);
				//echo (hash('sha256',$password, $results[0]->Salt));
				//echo "<br>";
				//echo $results[0]->Password;
				if((hash('sha256',$password, $results[0]->Salt)) == ($results[0]->Password)){

					$_SESSION['id'] = $results[0]->UserId; //User Id is generated in database, and stored in session
					$_SESSION['name'] = $results[0]->FirstName;
					return true;
				}else{
					echo "Wrong Password!";
				}
			}else{
				echo "No Email Exists Please Register. ";
			}

		}
		public function getEmail(){
			return $this->email;
		}
		public function getFirstName(){
			return $this->firstName;
		}
		public function getLastName(){
			return $this->lastName;
		}
		public function getDateOfBirth(){
			return $this->dateOfBirth;
		}

		public function getID(){
			return $this->uID;
		}
		public function getSalt(){
			return $this->salt;
		}
		public function changePassword($oldPass, $newPass, $email){
			//echo "function change password called";
			
			if ($this->login($email,$oldPass)){
				echo "correct password";
				echo '<br>';
				$salt = $this->getSalt();
				echo $salt;
				echo '<br>';
				echo hash("sha256",$newPass,$salt);
				echo '<br>';
				echo '<br>';
				$newPass = hash("sha256",$newPass,$salt);

				
				$query =  DB::getInstance() -> prep("UPDATE users SET Password = ? WHERE Email= ?");
				//"UPDATE users SET Password='$newPass' WHERE Username = '$username' AND Password = '$oldPass'"

				$query -> bindValue(1, $newPass);
				$query -> bindValue(2, $email);
				$query -> execute();
				//var_dump($query);

				
				
			} else {
				echo "Incorrect password";
			}
		}

		public function resetPassword($newPassword,$email){
			echo "Email is".$email;
			echo "<br>";
			echo "New Password is ".$newPassword;
				$salt = $this->getSalt();

				$saltPassword = hash("sha256",$newPassword,$salt);

				echo "Salted Password :".$saltPassword;
				echo "<br>";
				echo "salt is ".$salt;

				
				$query =  DB::getInstance() -> prep("UPDATE users SET Password = ? WHERE Email= ?");
				//"UPDATE users SET Password='$newPass' WHERE Username = '$username' AND Password = '$oldPass'"

				$query -> bindValue(1, $saltPassword);
				//$query -> bindValue(2, $salt);
				$query -> bindValue(2, $email);
				$query -> execute();


		}
	}
?>
