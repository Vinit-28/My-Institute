<?php

    // Checking the Authenticity of the User //
    session_start();

    require "./UserAuthentication.php";

    if( isUserAuthenticated() == false ){

        session_destroy();
        header('Location: ../index.php');
    }

    // If the Logged In User is the Root User //
    if( $_SESSION['userDetails']['authority'] == "root" ){

        header("Location: ../root/PHP/home.php");
    }
    // If the Logged In User is a Teacher //
    else if( $_SESSION['userDetails']['authority'] == "teacher" ){

        header("Location: ../teacher/PHP/home.php");
    }
    // If the Logged In User is a Student //
    else if( $_SESSION['userDetails']['authority'] == "student" ){

        header("Location: ../student/PHP/home.php");
    }
?>