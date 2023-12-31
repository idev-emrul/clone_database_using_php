<?php

    function pre($code){
        echo "<pre>";
        print_r($code);
        echo "</pre>";
        exit();
    }
    $message = "";
    if(isset($_POST['clone_db']) && !empty($_POST['source_db']) && !empty($_POST['new_db'])){
        // ====== general variables========
        $servername = "localhost";
        $username = "root";
        $password = "";
        $source_db = $_POST['source_db'];
        $new_bd = $_POST['new_db'];
        

        // ------ server connection --------
        $conn = new mysqli($servername, $username, $password);
        // ----------- check connectin -----------
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        
        {
            // ----- check source database exist or not----
            $sql = "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$source_db'";
            // Execute query
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                // ===== STEP 1 [Create new data for set destination]===============
                {
                    $sql = "CREATE DATABASE IF NOT EXISTS ".$new_bd." DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci";
                    if ($conn->query($sql) === TRUE) {
                        $message .= "Database ".$new_bd." created successfully </br>";
                    } else {
                        $message .= "Error creating database: " . $conn->error."</br>";
                    }
                }


                // ===== STEP 2 [Collect table name from source database and push that to destination database]===============
                {
                    // ----- get table list of source database -------
                    $conn = new mysqli($servername, $username, $password,$source_db);
                    $sql = "SHOW TABLES";
                    $result = $conn->query($sql);
        
                    // ----- transfer table form source database to new database--------
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            //---- set vauriable for source and destination table------
                            $new_bd_table=$new_bd.".".$row["Tables_in_" . $source_db];
                            $source_db_table = $source_db.".".$row["Tables_in_" . $source_db];
        
                            // -----create table like source tables with full structure then import data form source table----
                            $sql = "CREATE TABLE ".$new_bd_table." LIKE ".$source_db_table;
                            if ($conn->query($sql) === TRUE) {
                                $sql_insert = "INSERT INTO ".$new_bd_table." SELECT * FROM ".$source_db_table;
                                if ($conn->query($sql_insert) === TRUE) {
                                    $message .= "Table ".$new_bd_table." created and data copied successfully.</br>";
                                } else {
                                    $message .= "Error copying data: " . $conn->error."</br>";
                                }
                            } else {
                                $message .= "Error creating table ".$new_bd_table.": " . $conn->error."</br>";
                            }
                        }
                    } else {
                        $message .= "No tables found</br>";
                    }   
                }
            } else {
                $message .= "Database '$source_db' does not exist.</br>";
            }       
        }
        $_POST = array();
        // header("Location: index.php");
        // exit;
    }else{
        $message .= "";
    }
?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bootstrap demo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
  </head>
  <body>
      <form action="index.php" method="post">
          <div class="container">
              <div class="row justify-content-md-center">
                  <div class="col-md-6 ">
                    <h1 class="mt-4">Clone Database by PHP </h1>
                    
                        <?php  
                            if(!empty($message)){ ?>
                            <p style="background:#ff000022; border:1px solid #ff000022;padding:5px;"> <?= $message; ?></p>
                                
                          <?php  }
                        ?>
                    <label for="">Source database</label>
                    <input type="text" class="form-control" name="source_db">

                    <label for="" class="pt-2">Destination database</label>
                    <input type="text" class="form-control" name="new_db">

                    <input type="submit" name="clone_db" class="form-control mt-4 btn btn-primary" value="Save">
                </div>
            </div>
        </div>
    </form>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
  </body>
</html>

