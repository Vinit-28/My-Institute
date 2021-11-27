

<?php

    // Function to get the Database Connection Object //
    function get_DatabaseConnectionObject($dbName){
        
        $hostName = "localhost";
        $userName = "root";
        $password = "";
        $databaseConnectionObject = new mysqli($hostName, $userName, $password);
        
        if( $databaseConnectionObject->errno ){
            die($databaseConnectionObject->error);
        }

        if( isDatabaseExists($databaseConnectionObject, $dbName) == false ){
            runQuery($databaseConnectionObject, "CREATE DATABASE " .$dbName, [], "");
        }
        $databaseConnectionObject->select_db($dbName);
        return $databaseConnectionObject;
    }
    
    
    // Function to check whether a particular database exists or not //
    function isDatabaseExists($databaseConnectionObject, $dbName){
        
        $stmt = $databaseConnectionObject->prepare("SHOW DATABASES;");
        $stmt->execute();
        $res = $stmt->get_result();

        while($row = $res->fetch_assoc())
            if( $row['Database'] == $dbName ){ $res->close();return true; }
        $res->close();
        return false;
    }
    
    
    // Function to execute a SQL query and will return the result //
    function runQuery($databaseConnectionObject, $query, $parameterArray, $parameterTypes, $checkAfftectedRows=false){
        
        $stmt = $databaseConnectionObject->prepare($query);
        if( $stmt ){

            if( $parameterTypes != "" ){
                $stmt->bind_param($parameterTypes, ...$parameterArray);
            }
            $stmt->execute();
            $result = $stmt->get_result();
            if( $checkAfftectedRows ) checkAffectedRows($stmt);
            $stmt->close();
            return $result;
        }
        die("SOME INTERNAL ERROR !!!");
    }


    // Function to get a Column Value From a Database //
    function getColumnValue($databaseConnectionObject, $query, $parameterArray, $parameterTypes, $columnName){

        $result = runQuery($databaseConnectionObject, $query, $parameterArray, $parameterTypes);

        if( $result && $result->num_rows ){
            $row = $result->fetch_assoc();
            foreach($row as $col => $val){
                if( $col == $columnName ) return $val;
            }
        }
        else
            die("SOME INTERNAL ERROR !!!");
    }


    // Function to check whether the previously executed query has changed the Database or Not //
    function checkAffectedRows($stmt){
        if( $stmt->affected_rows <= 0 ){
            die($stmt->error);
        }
    }
?>
