<?php

require_once "connection.php";

function connect_to_db()
{
    global $host, $db_user, $db_password, $db_name;

    $db_connection = mysqli_connect($host, $db_user, $db_password);
    if( !$db_connection ){
        die("Could not connect: " . mysql_error());
    }
    mysqli_select_db($db_connection, $db_name) or die(mysql_error());

    return $db_connection;
}

function set_utf8_polish_ci($db_connection)
{
    mysqli_query($db_connection, "SET NAMES 'utf8' COLLATE 'utf8_polish_ci'") or die(mysql_error());
}

function send_query($db_connection, $sql)
{    
    $result = mysqli_query($db_connection, $sql);
    if( !$result ) mysql_error();
    return $result;
}

function slugify($text)
{ 
  // replace non letter or digits by -
  $text = preg_replace('~[^\\pL\d]+~u', '-', $text);

  // trim
  $text = trim($text, '-');

  // transliterate
  $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

  // lowercase
  $text = strtolower($text);

  // remove unwanted characters
  $text = preg_replace('~[^-\w]+~', '', $text);

  if (empty($text)){
    return 'n-a';
  }

  return $text;
}