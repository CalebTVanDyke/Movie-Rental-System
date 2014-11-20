<?php
require_once('objects/user.php');
session_start();

function validation(){
	if(isset($_POST['username'])){
		$uname = $_POST["username"];
		$pw1   = $_POST["password"];
		$pw2   = $_POST["confPass"];
		$email = $_POST["email"];
		$phone = $_POST["phone"];
		$isLib = $_POST["isLib"];
		$first = $_POST["firstName"];
		$last  = $_POST["lastName"];
		$bLib = 0;

		// Validate input
		if(!preg_match("/^[a-zA-Z0-9]+$/", $uname)){
			echo "<script>alert('Invalid username: " .$uname. "')</script>";
			return false;
		}
		if($pw1 !== $pw2){
			echo "<script>alert('Passwords did not match')</script>";
			return false;
		}	
		$passhash = md5($pw1);	
		if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
			echo "<script>alert('Invalid email: " .$email. "')</script>";
			return false;
		}
		if($phone !== "" && !(preg_match("/^[0-9]{10}$/", $phone) 
			|| preg_match("/^[0-9]{3}[-]{1}[0-9]{3}[-]{1}[0-9]{4}$/", $phone))){
			echo "<script>alert('Invalid phone:" .$phone. "')</script";
			return false;
		}
		if($isLib !== "" && !preg_match("/^(true|false)$/", $isLib)){
			echo "<script>alert('Invalid isLib: " .$isLib. "')</script>";
			return false;
		}
		// Since isLib is optional, 
		if($isLib === "true")
			$bLib = 1;
		if(!preg_match("/^[a-zA-Z]+$/", $first)){
			echo "<script>alert('Invalid Firstname: " .$first. "')</script";
			return false;
		}
		if(!preg_match("/^[a-zA-Z]+$/", $last)){
			echo "<script>alert('Invalid lastname: " .$last. "')</script>";
			return false;
		}

		$user = User::createUser($uname,$passhash,$email,$phone,$bLib,$first,$last);
		if($user != null){
			$_SESSION['username'] = $user->getUsername();
			$_SESSION['bIsLib']   = $user->isLib();
			header("Location: home.php");
		}
		else{
			echo "<script>alert('User: " .$uname. " already exists.')</script>";
			return false;
		}
	}
}

validation();
?>


<html>
<head>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">	
	<link rel="stylesheet" type="text/css" href="style.css">
</head>
<title>Unified Rental Service - Register</title>
<body>

<div id="register" class="col-md-8 col-md-offset-2">
<h1>Registration</h1>
<hr>
<div class="urs-container">
<form  action="" method="post"> 
	<h5>Username</h5><input name="username" type="text" placeholder="Username" required><br>
	<h5>Password</h5><input name="password" type="text" placeholder="********" required><br>
	<h5>Confirm Password</h5><input name="confPass" type="text" placeholder="********" required><br>
	<h5>Email</h5><input name="email"    type="text" placeholder="xxx@xxx.xxx" required><br>
	<h5>Phone</h5><input name="phone"    type="text" placeholder="xxx-xxx-xxxx" ><br>
	<h5>Is Administrator</h5><input name="isLib"    type="text"   placeholder="true or false" ><br>
	<h5>First Name</h5><input name="firstName" type="text"  placeholder="First name" required><br>
	<h5>Last Name</h5><input name="lastName"  type="text"  placeholder="Last name" required><br>
	<br>
	<input type="submit" class="btn btn-success" value="Register"> 
</form>
</div>
</div>

</body>
</html>