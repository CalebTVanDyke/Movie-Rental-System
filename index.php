<?php

include 'objects/user.php';
session_start();	

if(isset($_POST['username'], $_POST['password'])){
	$user = $_POST['username'];
	$pass = $_POST['password'];
	if(User::checkUserAndPass($user, $pass)){
		$_SESSION['user'] = serialize(User::getUser($user));
		header('Location: home.php');
	} else
		echo "<script>alert('Username or password incorrect!')</script>";
}

?>

<html>
<head>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="style.css">
<br>
</head>
<title>Unified Rental Service - Login</title>
<body>

<div id="login" class="col-md-8 col-md-offset-2 ">
<form action="" method="post">
<fieldset>
<h1>Login</h1>
	<hr>
	<div class="urs-container">
		<h5>Username</h5><input name="username" type="text" placeholder="Username"><br>
		<h5>Password</h5><input name="password" type="password" placeholder="******"><br><br>
		<input type="submit" class="btn btn-success" value="Login">
		<br><br>
		<a href="register.php">Need to register?</a>
	</div>
</fieldset>
</form>
</div>

</body>
<script>
</script>
</html>