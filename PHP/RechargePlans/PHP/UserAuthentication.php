
<?php

    // Importing the required modules //
    require "../../../Server/Utilities/DatabaseConfigurations.php";
    require "../../../Server/Utilities/UserUtilities.php";


    // Function to check whether the user is Authenticated or not //
    function isUserAuthenticated(){

        // Getting the Database Connection Object //
        $databaseConnectionObject = get_DatabaseConnectionObject("App_Database");
        
        // If the user is authenticated //
        if( ( isset($_SESSION["isUserLogedIn"]) && isset($_SESSION["userId"]) && isset($_SESSION["sessionId"]) && (isUserOnline($databaseConnectionObject, $_SESSION["userId"], $_SESSION["sessionId"])) ) ){
            
            // Getting and Setting the details of the user //
            $_SESSION['userPlanDetails'] = getUserPlanDetails($databaseConnectionObject, $_SESSION['userDetails']['instituteId']);
            $databaseConnectionObject->close();

            return true;
        }

        return false;
    }

?>