<?php
session_start();


$name; $surname;
list($name, $surname) = explode(" ", $_POST['nameSurname']);


$sql = "DELETE FROM staff ";
    
$sql = $sql." WHERE name = '".$name."' AND surname = '".$surname."'";
echo $sql;
    


require_once "functions.php";
    mysqli_report(MYSQLI_REPORT_STRICT);

    try 
    {
        $db_connection = connect_to_db();

        set_utf8_polish_ci($db_connection);
        send_query($db_connection, $sql);
    }
    catch(Exception $e)	{  echo $e;	}
    finally {
        mysqli_close($db_connection);
//        echo $sql."   ";
        header('Location: index.php');
        exit();
    }

?>