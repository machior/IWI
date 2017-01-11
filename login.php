<?php

    session_start();

    if ((!isset($_POST['login'])) || (!isset($_POST['password'])))
    {
        header('Location: index.php');
        exit();
    }

    require_once "functions.php";

    try 
    {
        $polaczenie = connect_to_db();
//        $polaczenie = pg_connect($pg_connect);

        
        $login = $_POST['login'];
        $haslo = $_POST['password'];

        $login = htmlentities($login, ENT_QUOTES, "UTF-8");

        $sql = sprintf("SELECT * FROM admins WHERE login='%s';",
        mysqli_escape_string($polaczenie,$login));
//        echo "sql = ".$sql;
        $result = mysqli_query($polaczenie, $sql);
        if ( !$result )   {   throw new Exception($polaczenie->error);    }

        $ilu_userow = mysqli_num_rows($result);
        
        if($ilu_userow < 1){
            $_SESSION['blad'] = '<span style="color:red">Nie ma takiego urzytkownika!</span>';
            header('Location: index.php');
            exit();
        }
        
        $row = mysqli_fetch_assoc($result);

        $sqlPassword = $row['password'];
        mysqli_close($polaczenie);

//                    if ( !password_verify($haslo, $row['pass']) )
        if ( strcmp($haslo, $sqlPassword) != 0 )
        {
            $_SESSION['blad'] = '<span style="color:red">Nieprawidłowe hasło!</span>';
            header('Location: index.php');
            exit();
        }

        $_SESSION['zalogowany'] = true;
//                            $_SESSION['id'] = $wiersz['id'];
//        $_SESSION['user'] = $login;


        unset($_SESSION['blad']);
        header('Location: index.php');
    }
    
    catch(Exception $e)
    {
//        echo '<span style="color:red;">Błąd serwera! Przepraszamy za niedogodności i prosimy o wizytę w innym terminie!</span>';
        echo '<br />Informacja developerska: '.$e;
    }
?>