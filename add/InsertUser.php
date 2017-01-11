<?php

$servername = "localhost";
$server_username = "root";
$server_password = "";
$dbName = "cool_yt_rpg";

$username = $_POST["usernamePost"]; //"Lucas Test AC";
$email = $_POST["emailPost"]; //"test email";
$password = $_POST["passwordPost"]; //"123456";

//Make connection
$conn = new mysqli($servername, $server_username, $server_password, $dbName);

if(!$conn)
{
	die("connection failed".mysqli_connect_error());
}

// $sql = "SELECT ID, Name, Type, Cost FROM items";
$sql = "INSERT INTO users (username, email, password) 
		VALUES ('".$username."', '".$email."', '".$password."')";
$result = mysqli_query($conn, $sql);

if(!$result) echo "there was an error in mysqli_querry";
else echo "Everything's OK.";

$conn->close();

?>