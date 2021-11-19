<?php

    // starting the Session //
    session_start();

    //Report all errors except warnings.
    error_reporting(E_ALL ^ E_WARNING);

    // Importing the required files //
    require "./Utilities/DatabaseConfigurations.php";
    require "./Utilities/UserUtilities.php";

    // Getting the database connection object //
    $databaseConnectionObject = get_DatabaseConnectionObject("App_Database");


    // Trying to login in the App //
    function login($userId, $password){
        
        global $databaseConnectionObject;
        return( isUserAuthorized($databaseConnectionObject, $userId, $password) );
    }


    // Trying to Register an Institute in the App //
    function signUp($instituteId, $instituteName, $instituteEmail, $password){
        
        global $databaseConnectionObject;
        
        if( isUserRegistered($databaseConnectionObject, $instituteId) ){
            return array(
                "result"=>"Failed",
                "message"=>"User Id Already Exists !!!",
            );
        }
        makeInstituteRegistered($databaseConnectionObject, $instituteId, $instituteName, $instituteEmail, $password);
        return array(
            "result"=>"Success",
            "message"=>"Registration Successfull, Now Login !!!",
        );
    }


    // If a client has made a request //
    if( isset($_POST['request']) ){
        
        $request = json_decode($_POST['request'], true);


        // If the request is for Login //
        if( $request['task'] == 'login' ){
            

            // If credentials are valid //
            if( login($request['userId'], $request['password']) ){
                
                session_regenerate_id();
                $sessionId = session_id();

                $_SESSION['isUserLogedIn'] = true;
                $_SESSION['userId'] = $request["userId"];
                $_SESSION['sessionId'] = $sessionId;
                

                // Making the user Online and storing the session id in the database //
                makeUserOnline($databaseConnectionObject, $request["userId"], $sessionId);

                $response = array(
                    "result"=>"Success",
                    "message"=>"",
                );
                
                // Sending the response //
                echo json_encode($response);
            }

            // If credentials are not valid //
            else{
                $response = array(
                    "result"=>"Failed",
                    "message"=>"Invalid User Id or Password !!!",
                );
                
                // Sending the response //
                echo json_encode($response);
            }
        }


        // If the request is for Sign Up //
        else if( $request['task'] == 'signup' ){

            $response = signUp($request['instituteId'], $request['instituteName'], $request['instituteEmail'], $request['password']);
            
            // Sending the response //
            echo json_encode($response);
        }



        // If the request is for Logout //
        else if( $request['task'] == 'logout' ){

            // Making user offling || Destroying SessionId //
            makeUserOffline($databaseConnectionObject, $request['userId']);

            $response = array(
                "result"=>"Success",
                "message"=>"",
            );
                
            // Sending the response //
            echo $response;
        }
    }
?>