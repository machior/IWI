<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbName = "cool_yt_rpg";

//Make connection
$conn = new mysqli($servername, $username, $password, $dbName);

if(!$conn)
{
	die("connection failed".mysqli_connect_error());
}

// $sql = "SELECT ID, Name, Type, Cost FROM items";
$sql = "SELECT * FROM items";
$result = mysqli_query($conn, $sql);

if(mysqli_num_rows($result)  > 0) 
{
	while($row = mysqli_fetch_assoc($result)){
		echo "ID:".$row['ID']."|Name:".$row['Name']."|Type:",$row['Type']."|Cost:".$row['Cost'].";";
	}
}

$conn->close();

?>