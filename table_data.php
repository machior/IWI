<?php

    $sql = "SELECT * FROM staff";
    $countries = array();
    $colNotCountries = array();
    $colNames = array();
    $employeesNames = array();
    $employeesRawNames = array();
    $fullArray = array();
                        
	try 
	{
            $db_connection = connect_to_db();
//            echo $sql."   ";

            set_utf8_polish_ci($db_connection);

            $result = send_query($db_connection, $sql);
            $rows_nr = mysqli_num_rows($result);
            
            for($i=0; $i<$rows_nr; ++$i)
            {
                $row = mysqli_fetch_assoc($result);
                
                if($i==0){
                    foreach($row as $colname => $val){
                        if($colname != 'id' && $colname != 'name' && $colname != 'surname' 
                    && $colname != 'image' && $colname != 'href' && $colname != 'country'){
                            array_push($colNames, $colname);
                            array_push($colNotCountries, $colname);
                    }
                }}
                
                for ($j=0; $j<count($colNames); ++$j){
                    if( strcmp($colNames[$j], $row['country']) == 0 ){
                        break;
                    }
                    if( $j+1 == count($colNames) ){
                        array_push($colNames, $row['country']);
                        array_push($countries, $row['country']);
                    }
                }
            }
            
            mysqli_data_seek($result, 0);

            for($i=0; $i<$rows_nr; $i++)
            {
                $row = mysqli_fetch_assoc($result);
                
                $rawData = $row['name']." ".$row['surname'];

                $data = slugify($rawData);
                $employeesNames[$i] = $data;
                $employeesRawNames[$i] = $rawData;

                echo '<li style="user-select: none;" onclick="leftPanelButtonClick(this)" id="'.$data.'" >'.$rawData.'</li>
                ';
                for($j=0; $j<count($colNames); $j++){
                    if( isset($row[$colNames[$j]]) ){
                        $fullArray[$data][$colNames[$j]] = $row[$colNames[$j]];
                    }else{
                        $fullArray[$data][$colNames[$j]] = 0;
                        if( strcmp($row['country'], $colNames[$j]) == 0 ){
                            $fullArray[$data][$colNames[$j]] = 1;
                        }
                    }
                }
            }
            
            $_SESSION['colNames'] = $colNames;
            $_SESSION['colNotCountries'] = $colNotCountries;
            
	}
        catch(Exception $e)	{  echo $e;	}
?>