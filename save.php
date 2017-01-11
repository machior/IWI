<?php
session_start();

if( !isset($_POST['name']) || !isset($_POST['surname']) || !isset($_POST['country']) ){
    header('Location: index.php');
    exit();
}

if( !isset($_FILES['imageToUpload']['tmp_name']) ){
    header('Location: index.php');
    exit();
}
$image = addslashes(file_get_contents($_FILES['imageToUpload']['tmp_name']));
//$image_name = addslashes($_FILES['imageToUpload']['name']);
//$sql = "INSERT INTO `product_images` (`id`, `image`, `image_name`) VALUES ('1', '{$image}', '{$image_name}')";

$name = $_POST['name'];
$surname = $_POST['surname'];
$country = $_POST['country'];
//list($name, $surname) = explode(" ", $_POST['nameSurname']);


$sql = "INSERT INTO staff ( ";

$sql = $sql." country, name, surname, ";
for($i=0; $i<count($_SESSION['colNotCountries']); ++$i)
{
    $sql = $sql.$_SESSION['colNotCountries'][$i];
    
    $sql = $sql.", ";
}

$sql = $sql." image ) VALUES ( '".$country."', '".$name."', '".$surname."', ";

for($i=0; $i<count($_SESSION['colNotCountries']); ++$i)
{
    $string = $_SESSION['colNotCountries'][$i].'CheckBox';
    
    if(isset($_POST[$string])){
        $sql = $sql."'1'";
    }
    else{
        $sql = $sql."'0'";
    }
    
    $sql = $sql.", ";
}

$sql = $sql."'".$image."' ) ";
    
echo $sql."   ";



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
//        header('Location: index.php');
        exit();
    }

?>