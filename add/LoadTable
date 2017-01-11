<?php

require_once "connection.php";

$username = $_POST["usernamePost"];
$password = $_POST["passwordPost"];

//Make connection
$conn = new mysqli($host, $db_user, $db_password, $db_name);
if(!$conn)
{
	die("connection failed".mysqli_connect_error());
}

$sql = "SELECT password FROM users WHERE username = '".$username."' ";
$result = mysqli_query($conn, $sql);

//Get the result and add confirm login
if(mysqli_num_rows($result) > 0){
	//show data for each row
	while($row = mysqli_fetch_assoc($result)){
		if($row['password'] == $password) {
			echo "login success ";
			echo $row['password']."<br/>";
		} else {
			echo "password incorrect ";
			echo $row['password']."<br/>";
		}
	}
} else {
	echo "user not found";
}

$conn->close();

?>