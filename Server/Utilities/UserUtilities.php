

<?php

    // Registration of Institute //
    function makeInstituteRegistered($databaseConnectionObject, $instituteId, $instituteName, $instituteEmail, $password){
        
        $password = password_hash($password, PASSWORD_BCRYPT);
        
        // Registering the Institute in the App Database //
        $query = "INSERT INTO AppUsers(userId, password, email, instituteName, authority, emailVerified) VALUES(?,?,?,?,?,?);";
        $result = runQuery($databaseConnectionObject, $query, [$instituteId, $password, $instituteEmail, $instituteName, "root", "false"], "ssssss");
        
        $query = "INSERT INTO LoggedInUsers(userId, sessionId) VALUES(?,?);";
        $result = runQuery($databaseConnectionObject, $query, [$instituteId, "offline"], "ss");
        
        
        // Making a seperate database for the  institute //
        $instituteDatabase = get_DatabaseConnectionObject($instituteId);
        
        // Making a TeacherInfo Table which will store all teachers related information // 
        $query = "CREATE TABLE TeacherInfo(
            teacherId VARCHAR(100) PRIMARY kEY, 
            teacherName VARCHAR(100), 
            department VARCHAR(100), 
            designation VARCHAR(100), 
            email VARCHAR(100), 
            contact VARCHAR(100)
            );";
        runQuery($instituteDatabase, $query, [], "");
        
        // Making a StudentInfo Table which will store all students related information // 
        $query = "CREATE TABLE StudentInfo(
            studentId VARCHAR(100) PRIMARY kEY, 
            studentName VARCHAR(100), 
            department VARCHAR(100), 
            class VARCHAR(100),
            email VARCHAR(100), 
            contact VARCHAR(100)
            );";
        runQuery($instituteDatabase, $query, [], "");
        

        // Making a InstituteConfig Table which will store all institute configurations related information // 
        // $query = "CREATE TABLE InstituteConfig(
        //     departments VARCHAR(100),
        //     designations VARCHAR(100)
        //     );";
        // runQuery($instituteDatabase, $query, [], "");
    }


    // Checking whether the user is authorized or not (Will be used for login purpose)//
    function isUserAuthorized($databaseConnectionObject, $userId, $password){
        
        $databaseConnectionObject->select_db("App_Database");
        $query = "SELECT * FROM AppUsers WHERE userId = ?;";
        $result = runQuery($databaseConnectionObject, $query, [$userId], "s");
        
        if( $result && $result->num_rows ){
            $row = $result->fetch_assoc();
            $result->close();
            return password_verify($password, $row["password"]);
        }
        $result->close();
        return false;
    }


    // Function to check whether the user is Registered in the App or Not //
    function isUserRegistered($databaseConnectionObject, $userId){

        $query = "SELECT * FROM AppUsers WHERE userId = ?";
        $res = runQuery($databaseConnectionObject, $query, [$userId], "s");
        if( $res && $res->num_rows > 0 ){
            return true;
        }
        return false;
    }

    
    // Checking whether the user is online or not //
    function isUserOnline($databaseConnectionObject, $userId, $sessionId){
        
        $query = "SELECT * FROM LoggedInUsers WHERE userId = ? AND sessionId = ?;";
        $result = runQuery($databaseConnectionObject, $query, [$userId, $sessionId], "ss");
        
        if( $result && $result->num_rows && $result->fetch_assoc()['sessionId'] != "offline" ){
            $result->close();
            return true;
        }
        $result->close();
        return false;
    }
    
    
    // Storing the SessionId of the user into the database //
    function makeUserOnline($databaseConnectionObject, $userId, $sessionId){
        
        $query = "UPDATE LoggedInUsers SET sessionId = ? WHERE userId = ?;";
        runQuery($databaseConnectionObject, $query, [$sessionId, $userId], "ss");
    }


    // Making the User Status Offline //
    function makeUserOffline($databaseConnectionObject, $userId){

        $query = "UPDATE LoggedInUsers SET sessionId = ? WHERE userId = ?;";
        runQuery($databaseConnectionObject, $query, ["offline", $userId], "ss");
    }





























    






    // Function to Delete either one or all institutes from the database // (For Development Purpose Only)
    function cleanDatabase($databaseConnectionObject, $instituteId=""){

        $databaseConnectionObject->select_db("App_Database");
        $res = runQuery($databaseConnectionObject, "SELECT userId FROM AppUsers;", [], "");

        if( $res && $res->num_rows > 0){

            while($row = $res->fetch_assoc()){

                if( $row['userId'] == $instituteId || $instituteId == "" ){
                    $databaseConnectionObject->query("DELETE FROM AppUsers WHERE userId = '" . $row['userId'] . "';");
                    $databaseConnectionObject->query("DELETE FROM LoggedInUsers WHERE userId = '". $row['userId'] . "';");
                    if( isDatabaseExists($databaseConnectionObject, $row['userId']) )
                        $databaseConnectionObject->query("DROP DATABASE " . $row['userId'] . ";");
                }
            }
        }
        
    }


?>
