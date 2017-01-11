

<?php

	session_start();

	require_once "connection.php";
	mysqli_report(MYSQLI_REPORT_STRICT);

    $sql = "SELECT wydzial FROM pg";
		
	try 
	{
		$db_connection = new mysqli($host, $db_user, $db_password, $db_name);
        
		//dodaje zmienna do odczytu polaczenia z baza danych
		if ($db_connection->connect_errno!=0)
		{
			throw new Exception(mysqli_connect_errno());
		}
		else
		{
			if ( $result = $db_connection->query(sprintf($sql)) )
			{
				$rows_nr = $result->num_rows;
                
				for($i=0; $i<$rows_nr; $i++)
				{
					$row = $result->fetch_assoc();
				    echo $row['wydzial'];
                    //echo $row['katedra'];
                    if($i+1 < $rows_nr)
                        echo "|";
                }
                
            }
			else
			{
				throw new Exception($db_connection->error);
			}
			
			$db_connection->close();
        }
	}
	catch(Exception $e)
	{
		echo $e;
	}
?>