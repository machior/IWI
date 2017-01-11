<?php
    session_start();
    
    if( !isset($_POST['nameSurname']) ){
        header('Location: index.php');
        exit();
    }

    if( isset($_FILES['imageToUploads']['tmp_name']) && strlen($_FILES['imageToUploads']['tmp_name']) > 0 ){
        $image = addslashes(file_get_contents($_FILES['imageToUploads']['tmp_name']));
    }
    $country = $_POST['newCountry'];

    $name; $surname;
    list($name, $surname) = explode(" ", $_POST['nameSurname']);
    $newName; $newSurname;
    list($newName, $newSurname) = explode(" ", $_POST['newNameSurname']);

    $sql = "UPDATE staff SET ";

    for($i=0; $i<count($_SESSION['colNotCountries']); ++$i)
    {
        $sql = $sql.$_SESSION['colNotCountries'][$i];
        $string = $_SESSION['colNotCountries'][$i].'CheckBox';

        if(isset($_POST[$string])){
            $sql = $sql." = '1'";
        }
        else{
            $sql = $sql." = '0'";
        }
        $sql = $sql.", ";
    }

    $sql = $sql." name = '".$newName."', surname = '".$newSurname."'";
    if( isset($image) ){
        $sql = $sql.", image = '".$image."'";
    }
    if(strlen($country) > 0){
        $sql = $sql.", country = '".$country."'";
    }

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
        header('Location: index.php');
        exit();
    }

?>