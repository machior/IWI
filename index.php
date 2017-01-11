<?php
session_start();

require_once "functions.php";
    
    if( isset($_POST['logout']) ){
        session_destroy();
    }
?>

<!DOCTYPE HTML>
<html lang="pl">
    <head>
        <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
        <title>HTML5</title>
        <link rel="stylesheet" type="text/css" href="css/common.css" />
        <link rel="stylesheet" type="text/css" href="css/index.css" />
        <!--l<ink rel="stylesheet" type="text/css" href="css/fontello.css" />-->
        
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
        
        <script src="js/three.min.js"></script>
    </head>
    <body>
        
        
        <header>
            <div class="container">
                <div class="jumbotron" 
                     style="padding: 0.5%; margin-top:2%; background: rgba(160, 160, 160, 0.5);">
                    
                    <img src="img/keystoneLogo.png" alt="" style="float: left;" />
                    <h1 style='font-family: "Times New Roman", Times, serif;'>Keystone Employees</h1>
                    <?php if( isset($_SESSION['zalogowany']) ){ ?>
                    <form method="POST" action="index.php">
                        <input style="display: none;" name="logout"/>
                        <span class="label"><input type="submit" class="btn btn-info btn-xs btn-block" value="Log Out" /></span>
                    </form>
                    <?php   }   ?>
                </div>
            </div>
        </header>
        
        
        <main class="container-fluid">
            <!--<div class="row">-->
            <div class="row" style="height:100%;">
            
            <!--<div class="sidepanel" id="leftTable">-->
            <div class="col-xs-2 sidepanel" id="leftTable">
                <h3><b>People</b></h3>
                <hr/>
                                
                    <ul class="nav nav-stacked">

                        
<?php require_once './table_data.php';?>
                </ul>
            </div>
            
            
                <div class="col-xs-8" id="center">

    <!--                mapa-->
                    <div id="map">

                    </div>

                </div>
            
            
            <!--<div class="sidepanel" id="rightTable">-->
                <div class="col-xs-2 sidepanel" id="rightTable">
                    <h3><b>Bondings</h3>
                    <hr/>
                
                    <ul class="nav nav-stacked">
<?php
for($i=0; $i<count($colNames); $i++)
    echo '<li onclick="rightPanelButtonClick(this)" id="'.$colNames[$i].'" >'.$colNames[$i].'</li>
';
?>
                    </ul>
                </div>
            
            </div>
        

            <div class="container" id="editionBlock">

                <table class="table">
                    <thead>
                        <tr>
                            <th>Automatic Spinning:</th>
                            <th>Outer Sphere</th>
                            <th>Inner Sphere</th>
                            <th>Swap Spheres</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><button class="btn" id="automaticRotate" onclick="automaticRotate(this)">OFF</button></td>
                            <td><button class="btn" id="outerSphereVisible" onclick="outerSphereVisible(this)">ON</button></td>
                            <td><button class="btn" id="innerSphereVisible" onclick="innerSphereVisible(this)">ON</button></td>
                            <td><button class="btn" id="swapSpheresButton" onclick="swapItemsOnSpheres()">SWAP</button></td>
                        </tr>
                    </tbody>
                </table>
                
                <?php if( !isset($_SESSION['zalogowany']) ) { ?>
                <label>Sign In To Edit</label>
                <div class="row">
                    <div class="col-sm-4"></div>
                    <div class="col-sm-4">
                        <form action="login.php" method="POST">
                            <input type="text" class="form-control" name="login" placeholder="Login" />
                            <input type="password" class="form-control" name="password" placeholder="********" />
                            <input type="submit" class="btn btn-info btn-xs btn-block" value="Sign In" />
                        </form>
                    </div>
                </div>
                
                <?php   }else{   ?>
                
                <table class="table">
                    <thead>
                        <tr>
                            <th>Edit Employee:</th>
                            <th>New Employee:</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>                
                                <form action="#openEdition">
                                    <input class="btn" type="submit" value="Edit" />
                                </form>
                            </td>
                            <td>
                                <form action="#openCreation">
                                    <input class="btn" type="submit" value="New" />
                                </form>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <?php   }    ?>

                <div id="openEdition" class="modalDialog">
                    <div>
                        <a href="#close" title="Close" class="close">X</a>
                        <h2>Edit Employee</h2>
                        <div class="tableDiv">

                            <form enctype="multipart/form-data" id="editingTable" class="editingFieldShown" method="post" action="update.php">


                                <label id="editTextLabel" ></label>
                                <img style="width:60px;" id="editImage" src="" />
                                <input type="text" id="editTextField" name="nameSurname" style="display:none;" /> <br/>
                                <label id="oldCountry" ></label> <br/>

                                <input type="text" id="newEditTextField" name="newNameSurname" placeholder="Imię i nazwisko" /> <br/>
                                <input type="file" accept="image/*" name="imageToUploads" /><br/><br/>

                <div style="display: inline-block; text-align: center;">
                <?php                
                for($i=0; $i<count($colNotCountries); $i++){
                    echo '<div class="checkbox">
                            <label><input type="checkbox" id="'.$colNotCountries[$i].'CheckBox" name="'.$colNotCountries[$i].'CheckBox">'.$colNotCountries[$i].'</label>
                          </div>';
                }
                ?> 
                    <input class="" type="text" id="newCountry" name="newCountry" placeholder="Country" /> <br/>

                </div>
                                <br/>
                                <input class="btn btn-primary" type="submit" id="submitEdition" value="Submit" disabled />

                            </form>

                            <button class="btn btn-sm btn-default" id="deleteEmployee" onclick="deleteEmployee()">Remove Employee</button>

                        </div>
                    </div>
                </div>


                <div id="openCreation" class="modalDialog">
                    <div>
                        <a href="#close" title="Close" class="close">X</a>
                        <h2>Add New Employee</h2>
                        <div class="tableDiv">

                            <form enctype="multipart/form-data" id="creatingTable" class="editingFieldShown" method="post" action="save.php">

                                <label> First Name: </label>
                                <input type="text" id="creationTextField" name="name" placeholder="Imię" /> <br/>
                                <label> Last Name: </label>
                                <input type="text" id="creationTextField" name="surname" placeholder="Nazwisko" /> <br/>
                                <input type="file" accept="image/*" name="imageToUpload" /><br/><br/>

                <?php                
                for($i=0; $i<count($colNames); $i++)
                    echo '<label style="display=none;" id="'.$colNames[$i].'CheckBox" />
                    ';
                ?>  
                    <div style="display: inline-block;">
                    <?php                
                    for($i=0; $i<count($colNotCountries); $i++){
                        echo '<div class="checkbox">
                                <label><input type="checkbox" id="'.$colNotCountries[$i].'CheckBox" name="'.$colNotCountries[$i].'CheckBox">'.$colNotCountries[$i].'</label>
                              </div>';
                    }
                                  
                
                    ?> <br/>
                    Country:
                    <br/>
                        <input type="text" id="creationTextField" name="country" /> <br/>
                    </div>
                                <br/>
                                <input class="btn btn-primary" type="submit" id="submitButton" value="Submit" />

                            </form>

                        </div>
                    </div>
                </div>



            </div>
        
            All Rights <u>Not</u> Reserved <span id="copyleft">&copy;</span>
        </main>
        
<!--    <div style="display:none;">-->
    <div id="photoZone">
<?php
    try 
    {
        mysqli_data_seek($result, 0);

        for($i=0; $i<$rows_nr; $i++)
        {
            $row = mysqli_fetch_assoc($result);

            if(isset($row['image']))
                echo '<img style="display:none;" id="'.$employeesNames[$i].'" src="data:image/jpeg;base64,'.base64_encode( $row['image'] ).'"/>
                ';
        }
    }
    catch(Exception $e)	{  
        echo $e;	
    }finally {
        mysqli_close($db_connection);
    }
 
        
?>    
        </div>
        
        
        <script type="text/javascript">
            var myObj = { 
                fred: { apples: 2, oranges: 4, bananas: 7, melons: 0 }, 
                mary: { apples: 0, oranges: 10, bananas: 0, melons: 0 }, 
                sarah: { apples: 0, oranges: 0, bananas: 0, melons: 5 } 
            }
            
            var table = {
            <?php 
            for($i=0; $i<count($employeesNames); ++$i){
                echo "'".$employeesNames[$i]."': { ";
                for($j=0; $j<count($colNames); ++$j)
                {
                    echo "'".$colNames[$j]."': ".$fullArray[$employeesNames[$i]][$colNames[$j]];
                    if($j+1 < count($colNames)) echo ", ";
                }
                if($i+1 < count($employeesNames)) echo "},
                ";
                else echo "}
                ";
            }
            ?> 
            };    
            var columns = [
            <?php 
                for($j=0; $j<count($colNames); ++$j)
                {
                    echo " '".$colNames[$j]."'";
                    if($j+1 < count($colNames)) echo ", ";
                }
            ?> 
            ];
            var names = [
            <?php 
                for($j=0; $j<count($employeesNames); ++$j)
                {
                    echo " '".$employeesNames[$j]."'";
                    if($j+1 < count($employeesNames)) echo ", ";
                }
            ?> 
            ];
        </script>
        
        
        <script src="js/jquery-3.1.1.min.js" ></script>
        <script type="text/javascript" src="js/common.js" ></script>
        <script type="text/javascript" src="js/employees.js" ></script>
        
        
    </body>
</html>