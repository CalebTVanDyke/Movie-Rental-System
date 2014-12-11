<?php

// Constants for storing database credentials
$dbUsername = "u319all";
$dbPassword = "024IjLaMj4dI";
$dbServer = "mysql.cs.iastate.edu"; 
$dbName   = "db319all";

class DB
{
	static $conn = null;

	//connection to the database
	function getConnection(){
		if(is_resource(self::$conn))
			return self::$conn;

		global $dbUsername, $dbPassword, $dbServer, $dbName;
		self::$conn = mysqli_connect($dbServer, $dbUsername, $dbPassword, $dbName);
		if(mysqli_connect_errno()){
			die("Failed to connect to MYSQL: " . mysqli_connect_errno());
			return;
		}
		return self::$conn;
	}

	function query($queryStr){
		$conn = self::getConnection();
		$result = mysqli_query($conn, $queryStr);
		return $result;
	}

	function showTable(){
		$conn = self::getConnection();

		$result = mysqli_query($conn, "SELECT * FROM users where Groupnumber=10");

		while($row = mysqli_fetch_array($result)){
			echo $row['Groupnumber']." ".$row['Username']." ";
			echo $row['Password']." ".$row['Email']." ".$row['Phone']." ";
			echo $row['Librarian']." ".$row['Firstname'] . " " . $row['Lastname'] . "<BR>";
		}
	}

	function createShelves(){
		$conn = self::getConnection();
		for($i = 0; $i < 10; $i++){
			for($j = 0; $j < 10; $j++){
				mysqli_query($conn, "INSERT INTO shelves VALUES (10, ". $i .", -1)");
			}
		}
	}
}
?>