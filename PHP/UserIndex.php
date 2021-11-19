<?php

    // Checking the Authenticity of the User //
    session_start();
    require "../Server/Utilities/DatabaseConfigurations.php";
    require "../Server/Utilities/UserUtilities.php";
    
    $databaseConnectionObject = get_DatabaseConnectionObject("App_Database");
    
    if( !( isset($_SESSION["isUserLogedIn"]) && isset($_SESSION["userId"]) && isset($_SESSION["sessionId"]) && (isUserOnline($databaseConnectionObject, $_SESSION["userId"], $_SESSION["sessionId"])) ) ){
        
        session_destroy();
        header('Location: ../index.php');
    }


    // Getting and Setting the details of the user //
    $query = "SELECT * FROM AppUsers WHERE userId = ?";
    $result = runQuery($databaseConnectionObject, $query, [$_SESSION['userId']], "s");
    $userDetails = $result->fetch_assoc();
    $_SESSION['userDetails'] = $userDetails;

    $databaseConnectionObject->close();
    
    // If the Logged In User is the Root User //
    if( $_SESSION['userDetails']['authority'] == "root" ){

        header("Location: ../root/PHP/home.php");
    }
    // If the Logged In User is a Teacher //
    else if( $_SESSION['userDetails']['authority'] == "teacher" ){

        header("Location: ../teacher/PHP/home.php");
    }
    // If the Logged In User is a Student //
    if( $_SESSION['userDetails']['authority'] == "student" ){

        header("Location: ../student/PHP/home.php");
    }
?>