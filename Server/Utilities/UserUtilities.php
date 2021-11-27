

<?php

    // Registration of Institute //
    function makeInstituteRegistered($databaseConnectionObject, $instituteId, $instituteName, $instituteEmail, $password){
        
        $databaseConnectionObject->select_db("App_Database");

        $password = password_hash($password, PASSWORD_BCRYPT);
        
        // Registering the Institute in the App Database //
        $query = "INSERT INTO AppUsers(userId, password, email, instituteName, authority, emailVerified, profilePath) VALUES(?,?,?,?,?,?,?);";
        $result = runQuery($databaseConnectionObject, $query, [$instituteId, $password, $instituteEmail, $instituteName, "root", "false", "http://localhost/Server/Profiles/noProfile.png"], "sssssss", true);
        
        $query = "INSERT INTO LoggedInUsers(userId, sessionId) VALUES(?,?);";
        $result = runQuery($databaseConnectionObject, $query, [$instituteId, "offline"], "ss", true);
        
        
        // Making a seperate database for the  institute //
        $instituteDatabase = get_DatabaseConnectionObject($instituteId);
        
        // Making a TeacherInfo Table which will store all teachers related information // 
        $query = "CREATE TABLE TeacherInfo(
            userId VARCHAR(100) PRIMARY kEY, 
            name VARCHAR(100), 
            email VARCHAR(100), 
            gender VARCHAR(100), 
            designation VARCHAR(100), 
            phoneNo VARCHAR(100), 
            adharCardNo VARCHAR(100), 
            address VARCHAR(100), 
            city VARCHAR(100), 
            state VARCHAR(100),
            pinCode VARCHAR(100)
            );";
        runQuery($instituteDatabase, $query, [], "");
        
        // Making a StudentInfo Table which will store all students related information // 
        $query = "CREATE TABLE StudentInfo(
            studentId VARCHAR(100) PRIMARY kEY, 
            studentName VARCHAR(100), 
            department VARCHAR(100),
            designation VARCHAR(100), 
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

    

    // Registration of a User(Teacher/Student) //
    function makeTeacherOrStudentRegistered($databaseConnectionObject, $userDetails){
        
        $databaseConnectionObject->select_db("App_Database");
        
        // Registering the User in the App Database //
        $query = "INSERT INTO AppUsers(userId, password, email, instituteName, authority, emailVerified, profilePath) VALUES(?,?,?,?,?,?,?);";
        runQuery($databaseConnectionObject, $query, [$userDetails['userId'], $userDetails['password'], $userDetails['email'], $userDetails['instituteName'], $userDetails['designation'], "false", "http://localhost/Server/Profiles/noProfile.png
        "], "sssssss", true);
         

        $query = "INSERT INTO LoggedInUsers(userId, sessionId) VALUES(?,?);";
        runQuery($databaseConnectionObject, $query, [$userDetails['userId'], "offline"], "ss", true);
        

        // Switching to the Specific Institute Database //
        $databaseConnectionObject->select_db($userDetails['instituteId']);
        
        // If the new User is a Teacher //
        if( $userDetails['designation'] == "teacher" || $userDetails['designation'] == "Teacher" ){
            
            $query = "INSERT INTO TeacherInfo(userId, name, email, gender, designation, phoneNo, adharCardNo, address, city, state, pinCode) VALUES(?,?,?,?,?,?,?,?,?,?,?);";
            runQuery($databaseConnectionObject, $query, [ $userDetails['userId'], $userDetails['name'], $userDetails['email'], $userDetails['gender'], $userDetails['designation'], $userDetails['phoneNo'], $userDetails['adharCardNo'], $userDetails['address'], $userDetails['city'], $userDetails['state'], $userDetails['pinCode'] ], "sssssssssss", true);
        }
        // If the new User is a Student //
        else if( $userDetails['designation'] == "student" || $userDetails['designation'] == "Student" ){

        }
        
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
        runQuery($databaseConnectionObject, $query, [$sessionId, $userId], "ss", true);
    }


    // Making the User Status Offline //
    function makeUserOffline($databaseConnectionObject, $userId){

        $query = "UPDATE LoggedInUsers SET sessionId = ? WHERE userId = ?;";
        runQuery($databaseConnectionObject, $query, ["offline", $userId], "ss", true);
    }


    // Search Users in the Institute's Database //
    function searchUsers($databaseConnectionObject, $request){

        $relatedPersons = array();
        $counter=1;
        $databaseConnectionObject->select_db($request['instituteId']);

        // Searching for students //
        $query = "SELECT * FROM StudentInfo WHERE studentId	= ? OR studentName = ? OR department = ? OR designation = ?";
        $result = runQuery($databaseConnectionObject, $query, [$request['searchKey'], $request['searchKey'], $request['searchKey'], $request['searchKey']], "ssss");

        if( $result && $result->num_rows ){
            $databaseConnectionObject->select_db("App_Database");

            while($row = $result->fetch_assoc()){
                $row += ["profilePath"=>getColumnValue($databaseConnectionObject, "SELECT * FROM AppUsers WHERE userId = ?;", [$row['userId']], "s", "profilePath")];
                $relatedPersons += [("relatedPerson-".$counter)=>$row];
                $counter+=1;
            }
        }

        // Searching for teachers //
        $databaseConnectionObject->select_db($request['instituteId']);
        $query = "SELECT * FROM TeacherInfo WHERE userId = ? OR name = ? OR designation = ?";
        $result = runQuery($databaseConnectionObject, $query, [$request['searchKey'], $request['searchKey'], $request['searchKey']], "sss");

        if( $result && $result->num_rows ){
            $databaseConnectionObject->select_db("App_Database");
           
            while($row = $result->fetch_assoc()){    
                $row += ["profilePath"=>getColumnValue($databaseConnectionObject, "SELECT * FROM AppUsers WHERE userId = ?;", [$row['userId']], "s", "profilePath")];
                $relatedPersons += [$counter=>$row];
                $counter+=1;
            }
        }

        return $relatedPersons;
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
