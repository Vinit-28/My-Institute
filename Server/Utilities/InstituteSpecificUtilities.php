<?php


    // Importing the required files //
    require "./DatabaseConfigurations.php";
    require "./UserUtilities.php";

    // Getting the database connection object //
    $databaseConnectionObject = get_DatabaseConnectionObject("App_Database");



    // If the ROOT User has made a request //
    if( isset($_POST['request']) ){
        
        $request = json_decode($_POST['request'], true);
        
        // If the request is to Add a teacher in the Database //
        if( $request['task'] == 'Add Teacher' && isUserOnline($databaseConnectionObject, $request['instituteId'], $request['sessionId']) ){
            
            $request['password'] = password_hash($request['password'], PASSWORD_BCRYPT);
            $databaseConnectionObject->select_db("App_Database");

            $request['instituteName'] = getColumnValue($databaseConnectionObject, "SELECT * FROM AppUsers WHERE userId = ?", [$request['instituteId']], "s", "instituteName");

            makeTeacherOrStudentRegistered($databaseConnectionObject, $request);


            $response = array(
                "result"=>"Success",
                "message"=>"Person Added Successfully !!!",
            );
            
            
            // Sending the response //
            echo json_encode($response);
        }


    }


?>