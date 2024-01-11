<?php
require_once 'config.php';

class DatabaseClone {
    private $servername = DB_SERVER;
    private $username = DB_USERNAME;
    private $password = DB_PASSWORD;
        
    // ===== function for show the code output exit execution=======
    public function pre($code){
        echo "<pre>";
        print_r($code);
        echo "</pre>";
        exit();
    }

    public function cloneDatabases($submitForm,$sourceDb, $newDb) {
        $conn = new mysqli($this->servername, $this->username, $this->password);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
               
        $message = "";

        if (isset($submitForm) && !empty($sourceDb) && !empty($newDb)) {
            $result = $this->checkDatabaseExists($conn, $sourceDb);
            // $this->pre($result->num_rows);

            if ($result->num_rows > 0) {
                // ----- create new database ----------
                $message .= $this->createNewDatabase($conn, $newDb);

                // ----- import tables and record to newBd from sourceDb---------
                $conn = new mysqli($this->servername, $this->username, $this->password, $sourceDb); //-- update connection for sourceDb----
                $tables = $this->getTables($conn, $sourceDb);
                $message .= $this->cloneTables($conn, $tables, $sourceDb, $newDb);
            } else {
                $message .= "Source Database '$sourceDb' does not exist.</br>";
            }
        } else {
            $message .= "";
        }

        return $message;
    }

    // ----- CHECK DATABSE EXIST OR NOT ----
    private function checkDatabaseExists($conn, $dbName) {
        $sql = "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$dbName'";
        return $conn->query($sql);
    }

    // ----- CREATE NEW DATABASE -----------
    private function createNewDatabase($conn, $dbName) {
        $sql = "CREATE DATABASE IF NOT EXISTS $dbName DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci";
        if ($conn->query($sql) === TRUE) {
            return "Database $dbName created successfully </br>";
        } else {
            return "Error creating database: " . $conn->error . "</br>";
        }
    }

    // ------ GET ALL TABLE NAMES FROM A DATABASE --------
    private function getTables($conn, $dbName) {
        $sql = "SHOW TABLES";
        $result = $conn->query($sql);
        $tables = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $tables[] = $row["Tables_in_$dbName"];
            }
        }
        return $tables;
    }


    //---- CLONE TABLE STRUCTURE AND TABLE DATA TO NEW DATABASE---------- 
    private function cloneTables($conn, $tables, $sourceDb, $newDb) {
        $message = "";
        $sn = 1;
        foreach ($tables as $table) {
            $sourceTable = "$sourceDb.$table";
            $newTable = "$newDb.$table";

            $sql = "CREATE TABLE IF NOT EXISTS $newTable LIKE $sourceTable";
            if ($conn->query($sql) === TRUE) {
                $sql_insert = "INSERT INTO $newTable SELECT * FROM $sourceTable";
                if ($conn->query($sql_insert) === TRUE) {
                    $message .= $sn . ". Table $table created | data copied successfully.</br>";
                } else {
                    $message .= $sn . ". Table $table Error copying data: " . $conn->error . "</br>";
                }
            } else {
                $message .= "Error creating table $table: " . $conn->error . "</br>";
            }
            $sn++;
        }
        return $message;
    }
}

// Usage example:
if(isset($_POST['clone_db'])){
    $dbCloner = new DatabaseCloner();
    $message = $dbCloner->cloneDatabases($_POST['clone_db'], $_POST['source_db'], $_POST['new_db']);
    if(!empty($message)){
        $encodedMessage = urlencode($message);
        header("Location: index.php?message=$encodedMessage");
        exit();
    }
}else{
    $message = 'Direct access not allowed';
    $encodedMessage = urlencode($message);
        header("Location: index.php?message=$encodedMessage");
        exit();
}
