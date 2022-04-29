

<?php

    // Registration of Institute //
    function makeInstituteRegistered($databaseConnectionObject, $instituteId, $instituteName, $instituteEmail, $password){
        
        $databaseConnectionObject->select_db("App_Database");

        $password = password_hash($password, PASSWORD_BCRYPT);

        // Making a seperate database for the  institute //
        $instituteDatabase = get_DatabaseConnectionObject($instituteId);
        
        // Registering the Institute in the App Database //
        $query = "INSERT INTO AppUsers(userId, password, email, instituteName, authority, emailVerified, profilePath, instituteId) VALUES(?,?,?,?,?,?,?,?);";
        $result = runQuery($databaseConnectionObject, $query, [$instituteId, $password, $instituteEmail, $instituteName, "root", "false", constant("fileHrefPrefix") . "Server/Profiles/noProfile.png", $instituteId], "ssssssss", true);
        
        $query = "INSERT INTO LoggedInUsers(userId, sessionId) VALUES(?,?);";
        $result = runQuery($databaseConnectionObject, $query, [$instituteId, "offline"], "ss", true);
        
        $query = "INSERT INTO Institutes(instituteId, instituteName, instituteEmail, institutePhoneNumber, address, city, state, pinCode, profilePath, planId, planDate, planValidity) VALUES(?,?,?,?,?,?,?,?,?,?,?,?);";
        $result = runQuery($databaseConnectionObject, $query, [$instituteId, $instituteName, $instituteEmail, "", "", "", "", "", constant("fileHrefPrefix") . "Server/Profiles/noProfile.png", 1, date("Y-m-d"), 7], "sssssssssisi", true);
        
    
        // -------------------- Creating Tables for a Institute in its Database -------------------- //

        // Making a TeacherInfo Table which will store all teachers related information // 
        $query = "CREATE TABLE TeacherInfo(
            userId VARCHAR(100) PRIMARY kEY, 
            name VARCHAR(100), 
            email VARCHAR(100), 
            gender VARCHAR(100), 
            designation VARCHAR(100), 
            class VARCHAR(100), 
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
            userId VARCHAR(100) PRIMARY kEY, 
            name VARCHAR(100), 
            email VARCHAR(100), 
            gender VARCHAR(100), 
            designation VARCHAR(100), 
            class VARCHAR(100), 
            phoneNo VARCHAR(100), 
            adharCardNo VARCHAR(100), 
            address VARCHAR(100), 
            city VARCHAR(100), 
            state VARCHAR(100),
            pinCode VARCHAR(100),
            feeSubmitted BIGINT(8)
            );";
        runQuery($instituteDatabase, $query, [], "");
        

        // Making a Classes Table which will store all Classes related information // 
        $query = "CREATE TABLE Classes(
            className VARCHAR(100) PRIMARY kEY, 
            fees BIGINT(8)
            );";
        runQuery($instituteDatabase, $query, [], "");


        // Making a UploadedFiles Table which will store all Uploaded Files related information // 
        $query = "CREATE TABLE UploadedFiles(
            fileId BIGINT(8) AUTO_INCREMENT PRIMARY kEY, 
            fileTitle VARCHAR(100), 
            filePathHref VARCHAR(1000),
            filePathMachine VARCHAR(1000),
            fileVisibility VARCHAR(1000),
            uploadDateTime VARCHAR(100),
            uploadedBy VARCHAR(100)
            );";
        runQuery($instituteDatabase, $query, [], "");


        // Making a LiveClasses Table which will store all Live Classes related information // 
        $query = "CREATE TABLE LiveClasses(
            liveClassId BIGINT(8) AUTO_INCREMENT PRIMARY kEY, 
            hostName VARCHAR(100), 
            teacherName VARCHAR(100), 
            subjectName VARCHAR(100),
            topicName VARCHAR(100),
            topicDescription VARCHAR(500),
            startingTime VARCHAR(100),
            endingTime VARCHAR(100),
            classDate VARCHAR(100),
            joiningLink VARCHAR(1000),
            liveClassVisibility VARCHAR(1000)
            );";
        runQuery($instituteDatabase, $query, [], "");


        // Making a Table which will store all Uploaded Assignments information // 
        $query = "CREATE TABLE UploadedAssignments(
            assignmentId BIGINT(8) AUTO_INCREMENT PRIMARY kEY, 
            uploadedBy VARCHAR(100), 
            subjectName VARCHAR(100),
            assignmentTitle VARCHAR(100),
            assignmentDescription VARCHAR(500),
            assignmentDeadline VARCHAR(100),
            uploadedDateTime VARCHAR(100),
            assignmentVisibility VARCHAR(100),
            assignmentFileLinkHref VARCHAR(1000),
            assignmentFileLinkMachine VARCHAR(1000)
            );";
        runQuery($instituteDatabase, $query, [], "");


        // Making a AssignmentSubmissions Table which will store all Submitted Assignments information // 
        $query = "CREATE TABLE AssignmentSubmissions(
            submittedBy VARCHAR(100), 
            submittedDateTime VARCHAR(100),
            assignmentId BIGINT(8), 
            submittedFileLinkHref VARCHAR(1000),
            submittedFileLinkMachine VARCHAR(1000)
            );";
        runQuery($instituteDatabase, $query, [], "");


        // Making a UploadedTest Table which will store all the information about the test uploaded by the teachers // 
        $query = "CREATE TABLE UploadedTest(
            testId BIGINT(8) AUTO_INCREMENT PRIMARY kEY, 
            uploadedBy VARCHAR(100),
            uploadedDateTime DATE,
            subjectName VARCHAR(100),
            topicName VARCHAR(100),
            testDate DATE,
            forClass VARCHAR(100),
            fromTime VARCHAR(100),
            toTime VARCHAR(100),
            questionGapSec INT, 
            testFileLinkHref VARCHAR(1000),
            testFileLinkMachine VARCHAR(1000)
            );";
        runQuery($instituteDatabase, $query, [], "");


        // Making a testSubmission Table which will store all the information about the marks obtained by the students in the test // 
        $query = "CREATE TABLE testSubmission(
            testId BIGINT(8), 
            submittedBy VARCHAR(100),
            submittedDateTime VARCHAR(200),
            totalMarks INT,
            marksAchieved INT
            );";
        runQuery($instituteDatabase, $query, [], "");


        // Making a feesDetails Table which will store all the information about fees details of students // 
        $query = "CREATE TABLE feesDetails(
            id BIGINT(8) AUTO_INCREMENT PRIMARY kEY, 
            studentId VARCHAR(100),
            class VARCHAR(200),
            transactionAmount INT,
            transactionTimestamp VARCHAR(100)
            );";
        runQuery($instituteDatabase, $query, [], "");


        // Making the Institute Folder in the Server //
        $path = getcwd();
        $path = str_replace("Server", "InstituteFolders/" . $instituteId, $path);
        mkdir($path);
        mkdir($path . "/uploadedFiles"); // Download-Upload Files
        mkdir($path . "/profilePhotos"); // Profile Photos of Institute's Persons
        mkdir($path . "/uploadedAssignments"); // Uploaded Assignment File
        mkdir($path . "/assignmentsSubmissions"); // Submitted Answer/File to the Assignment
        mkdir($path . "/uploadedTests"); // Test's Question Files of the student tests
        mkdir($path . "/temporaryDocuments"); // A Temporary Folder that will store some temporary files
        
    }

    

    // Registration of a User(Teacher/Student) //
    function makeTeacherOrStudentRegistered($databaseConnectionObject, $userDetails){
        
        $databaseConnectionObject->select_db("App_Database");
        
        // Registering the User in the App Database //
        $query = "INSERT INTO AppUsers(userId, password, email, instituteName, authority, emailVerified, profilePath, instituteId) VALUES(?,?,?,?,?,?,?,?);";
        runQuery($databaseConnectionObject, $query, [$userDetails['userId'], $userDetails['password'], $userDetails['email'], $userDetails['instituteName'], $userDetails['designation'], "false", constant("fileHrefPrefix") . "Server/Profiles/noProfile.png", $userDetails['instituteId']], "ssssssss", true);
         

        $query = "INSERT INTO LoggedInUsers(userId, sessionId) VALUES(?,?);";
        runQuery($databaseConnectionObject, $query, [$userDetails['userId'], "offline"], "ss", true);
        

        // Switching to the Specific Institute Database //
        $databaseConnectionObject->select_db($userDetails['instituteId']);
        
        // If the new User is a Teacher //
        if( $userDetails['designation'] == "teacher" || $userDetails['designation'] == "Teacher" ){
            
            $query = "INSERT INTO TeacherInfo(userId, name, email, gender, designation, class, phoneNo, adharCardNo, address, city, state, pinCode) VALUES(?,?,?,?,?,?,?,?,?,?,?,?);";
            runQuery($databaseConnectionObject, $query, [ $userDetails['userId'], $userDetails['name'], $userDetails['email'], $userDetails['gender'], $userDetails['designation'], $userDetails['class'], $userDetails['phoneNo'], $userDetails['adharCardNo'], $userDetails['address'], $userDetails['city'], $userDetails['state'], $userDetails['pinCode'] ], "ssssssssssss", true);
        }
        // If the new User is a Student //
        else if( $userDetails['designation'] == "student" || $userDetails['designation'] == "Student" ){
            
            $query = "INSERT INTO StudentInfo(userId, name, email, gender, designation, class, phoneNo, adharCardNo, address, city, state, pinCode, feeSubmitted) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?);";
            runQuery($databaseConnectionObject, $query, [ $userDetails['userId'], $userDetails['name'], $userDetails['email'], $userDetails['gender'], $userDetails['designation'], $userDetails['class'], $userDetails['phoneNo'], $userDetails['adharCardNo'], $userDetails['address'], $userDetails['city'], $userDetails['state'], $userDetails['pinCode'], 0], "ssssssssssssi", true);
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
        $result = "";
        $databaseConnectionObject->select_db($request['instituteId']);

        if( strtolower($request['searchKey']) != "all" ){
            // Searching for students //
            $query = "SELECT * FROM StudentInfo WHERE userId	= ? OR name = ? OR designation = ?";
            $result = runQuery($databaseConnectionObject, $query, [$request['searchKey'], $request['searchKey'], $request['searchKey']], "sss");
        }
        else{
            // Searching for students //
            $query = "SELECT * FROM StudentInfo;";
            $result = runQuery($databaseConnectionObject, $query, [], "");
        }

        if( $result && $result->num_rows ){
            $databaseConnectionObject->select_db("App_Database");

            while($row = $result->fetch_assoc()){
                $row += ["profilePath"=>getColumnValue($databaseConnectionObject, "SELECT * FROM AppUsers WHERE userId = ?;", [$row['userId']], "s", "profilePath"), "authority"=>getColumnValue($databaseConnectionObject, "SELECT * FROM AppUsers WHERE userId = ?;", [$row['userId']], "s", "authority")];
               
                $relatedPersons += [("relatedPerson-".$counter)=>$row];
                $counter+=1;
            }
        }

        $databaseConnectionObject->select_db($request['instituteId']);
        if( strtolower($request['searchKey']) != "all" ){
            // Searching for teachers //
            $query = "SELECT * FROM TeacherInfo WHERE userId = ? OR name = ? OR designation = ?";
            $result = runQuery($databaseConnectionObject, $query, [$request['searchKey'], $request['searchKey'], $request['searchKey']], "sss");
        }
        else{
            // Searching for teachers //
            $query = "SELECT * FROM TeacherInfo";
            $result = runQuery($databaseConnectionObject, $query, [], "");
        }

        if( $result && $result->num_rows ){
            $databaseConnectionObject->select_db("App_Database");
           
            while($row = $result->fetch_assoc()){    
                $row += ["profilePath"=>getColumnValue($databaseConnectionObject, "SELECT * FROM AppUsers WHERE userId = ?;", [$row['userId']], "s", "profilePath"), "authority"=>getColumnValue($databaseConnectionObject, "SELECT * FROM AppUsers WHERE userId = ?;", [$row['userId']], "s", "authority")];

                $relatedPersons += [$counter=>$row];
                $counter+=1;
            }
        }

        return $relatedPersons;
    }



    // Upadte Person Details in the Institute's Database //
    function updatePersonDetails($databaseConnectionObject, $request){

        $databaseConnectionObject->select_db($request['instituteId']);

        if( strtolower($request['designation']) == "teacher" ){

            $query = "UPDATE TeacherInfo SET name = ?, gender = ?, phoneNo = ?, adharCardNo = ?, address = ?, city = ?, state = ?, pinCode = ?, class = ? WHERE userId = ?";

            $result = runQuery($databaseConnectionObject, $query, [$request['name'], $request['gender'], $request['phoneNo'], $request['adharCardNo'], $request['address'], $request['city'], $request['state'], $request['pinCode'], $request['class'], $request['userId']], "ssssssssss", true);
        
        }
        else if( strtolower($request['designation']) == "student" ){
            
            // Finding the current submitted fees //
            $query = "SELECT feeSubmitted FROM StudentInfo WHERE userId = ?;";
            $result = runQuery($databaseConnectionObject, $query, [$request['userId']], "s");
            $submittedFee = $result->fetch_assoc()['feeSubmitted'];
            $feeDifference = ($request['fees'] - $submittedFee);
            
            // If fees has been modified //
            if( $feeDifference ){
                $temp = ["instituteId"=>$request['instituteId'], "studentId" => $request['userId'], "class"=> $request['class'], "transactionAmount"=>$feeDifference, "transactionTimestamp"=>$request['timestamp']];
                updateFees($databaseConnectionObject, $temp);
            }

            $query = "UPDATE StudentInfo SET name = ?, gender = ?, phoneNo = ?, adharCardNo = ?, address = ?, city = ?, state = ?, pinCode = ?, class = ? WHERE userId = ?";
            $result = runQuery($databaseConnectionObject, $query, [$request['name'], $request['gender'], $request['phoneNo'], $request['adharCardNo'], $request['address'], $request['city'], $request['state'], $request['pinCode'], $request['class'], $request['userId']], "ssssssssss");
        }
    }


    // Function to upload files into the institute's database //
    function uploadFileInTheDatabase($databaseConnectionObject, $request, $fileName, $fileTempName){

        $filePath =  ("InstituteFolders/" . $request['instituteId'] . "/uploadedFiles" . "/" . $request['uploadedBy'] . "__" . time() . "__" . $fileName);

        $newPath = getcwd();
        $newPath = str_replace("Server/Utilities", $filePath, $newPath);
        
        move_uploaded_file($fileTempName, $newPath);
        $databaseConnectionObject->select_db($request['instituteId']);
        
        $query = "INSERT INTO UploadedFiles(fileTitle, filePathHref, filePathMachine, fileVisibility, uploadDateTime, uploadedBy) VALUES(?,?,?,?,?,?);";
        
        runQuery($databaseConnectionObject, $query, [$request['fileTitle'], constant("fileHrefPrefix") . $filePath, $newPath, $request['fileVisibility'], $request['uploadDateTime'], $request['uploadedBy']], "ssssss", true);
    }


    // Function to get the Uploaded Files from the Institute's Database //
    function getUploadedFiles($databaseConnectionObject, $request){
        
        $databaseConnectionObject->select_db($request['instituteId']);
        $uploadedFiles = array();
        $query = "SELECT * FROM UploadedFiles;";
        $result = runQuery($databaseConnectionObject, $query, [], "");
        $counter=1;
        while($row = $result->fetch_assoc()){
            $uploadedFiles += ["'$counter'"=>$row];
            $counter+=1;
        }
        return $uploadedFiles;
    }


    // Function to Delete the Selected Files from the Institute's Database //
    function deleteUploadedFiles($databaseConnectionObject, $request){
        
        $databaseConnectionObject->select_db($request['instituteId']);
        $query1 = "DELETE FROM UploadedFiles WHERE fileId = ?;";
        $query2 = "SELECT * FROM UploadedFiles WHERE fileId = ?;";

        for($i=0; $i<count($request['selectedFiles']); $i++){
            $filePathMachine = getColumnValue($databaseConnectionObject, $query2, [$request['selectedFiles'][$i]], "s", "filePathMachine");
            
            if(!unlink($filePathMachine)){
                die("Something Went Wrong.... Error While Deleting File " . $filePathMachine);
            }
            runQuery($databaseConnectionObject, $query1, [$request['selectedFiles'][$i]], "i", true);
        }
    }


    // Function to get the User Profile Path From the Database // 
    function getUserProfilePath($databaseConnectionObject, $institueId, $userId){

        $databaseConnectionObject->select_db("App_Database");
        $path = getColumnValue($databaseConnectionObject, "SELECT * FROM AppUsers WHERE userId = ?", [$userId], "s", "profilePath");
        return $path;
    }


    // Function to get the Student details of an institute  // 
    function getStudentDetails($databaseConnectionObject, $institueId, $userId){

        $databaseConnectionObject->select_db($institueId);

        // Getting student information //
        $query = "SELECT * FROM StudentInfo WHERE userId = ?;";
        $result = runQuery($databaseConnectionObject, $query, [$userId], "s");
        $studentDetails = $result->fetch_assoc();

        // Getting student profile image //
        $studentDetails += ['profilePath'=>getUserProfilePath($databaseConnectionObject, $institueId, $userId)];
        return $studentDetails;
    }


    // Function to check whether the New Email Id is deifferent from the Email Id stored in the Database //
    function isEmailChanged($databaseConnectionObject, $userId, $newEmail){

        $databaseConnectionObject->select_db("App_Database");
        $prevEmail = getColumnValue($databaseConnectionObject, "SELECT * FROM AppUsers WHERE userId = ?", [$userId], "s", "email");

        return !($newEmail == $prevEmail);
    }


    // Function to update the Profile of a Institute //
    function updateInstituteProfile($databaseConnectionObject, $request){

        $databaseConnectionObject->select_db("App_Database");
        $query = "UPDATE Institutes SET instituteName = ?, instituteEmail = ?, institutePhoneNumber = ?, address = ?, city = ?, state = ?, pinCode = ? WHERE instituteId = ?;";

        runQuery($databaseConnectionObject, $query, [$request['updatedInstituteName'], $request['updatedInstituteEmail'], $request['updatedInstitutePhoneNumber'], $request['updatedInstituteAddress'], $request['updatedInstituteCity'], $request['updatedInstituteState'], $request['updatedInstitutePinCode'], $request['instituteId'] ], "ssssssss");


        $query = "UPDATE AppUsers SET instituteName = ? WHERE instituteId = ?;";
        runQuery($databaseConnectionObject, $query, [$request['updatedInstituteName'], $request['loggedInUser'] ], "ss");
        
        if( isEmailChanged($databaseConnectionObject, $request['loggedInUser'], $request['updatedInstituteEmail'])){

            $query = "UPDATE AppUsers SET email = ?, emailVerified = ? WHERE userId = ?;";
            runQuery($databaseConnectionObject, $query, [$request['updatedInstituteEmail'], "false", $request['loggedInUser'] ], "sss");
        }
    }


    // Function to update the Profile of a Teacher //
    function updateTeacherOrStudentProfile($databaseConnectionObject, $request, $authority){

        $databaseConnectionObject->select_db($request['instituteId']);
        $query = "";
        $newEmail = "";
        if( $authority == "teacher" ){
            $newEmail = $request['updatedTeacherEmail'];
            $query = "UPDATE TeacherInfo SET name = ?, email = ?, phoneNo = ?, address = ?, city = ?, state = ?, pinCode = ? WHERE userId = ?;";

            runQuery($databaseConnectionObject, $query, [$request['updatedTeacherName'], $request['updatedTeacherEmail'], $request['updatedTeacherPhoneNumber'], $request['updatedTeacherAddress'], $request['updatedTeacherCity'], $request['updatedTeacherState'], $request['updatedTeacherPinCode'], $request['loggedInUser'] ], "ssssssss");
        }
        else if( $authority == "student" ){
            $newEmail = $request['updatedStudentEmail'];
            $query = "UPDATE StudentInfo SET name = ?, email = ?, phoneNo = ?, address = ?, city = ?, state = ?, pinCode = ? WHERE userId = ?;";

            runQuery($databaseConnectionObject, $query, [$request['updatedStudentName'], $request['updatedStudentEmail'], $request['updatedStudentPhoneNumber'], $request['updatedStudentAddress'], $request['updatedStudentCity'], $request['updatedStudentState'], $request['updatedStudentPinCode'], $request['loggedInUser'] ], "ssssssss");
        }

        if( isEmailChanged($databaseConnectionObject, $request['loggedInUser'], $newEmail)){

            $query = "UPDATE AppUsers SET email = ?, emailVerified = ? WHERE userId = ?;";
            runQuery($databaseConnectionObject, $query, [$newEmail, "false", $request['loggedInUser'] ], "sss");
        }

    }


    // Functio to update the Profile of a User //
    function updateMyProfile($databaseConnectionObject, $request, $authority){

        if( $authority == "root" ){
            updateInstituteProfile($databaseConnectionObject, $request);
        }
        else if( $authority == "teacher" || $authority == "student" ){
            updateTeacherOrStudentProfile($databaseConnectionObject, $request, $authority);
        }
    }


    // Function to get the File Extension from the File Name (Usually for images) //
    function getFileExtension($fileName){

        $ext = "";
        for($i=strlen($fileName)-1; $i>=0; $i--){
            if( $fileName[$i] == '.' )
                break;
            $ext .= $fileName[$i];
        }
        return ("." . strrev($ext));
    }


    // Function to delete previous profile image of a user //
    function deletePreviousProfile($databaseConnectionObject, $userId){
        
        $appDirectory = str_replace("Server/Utilities", "", getcwd());
        $oldProfilePath = getColumnValue($databaseConnectionObject, "SELECT profilePath FROM AppUsers WHERE userId = ?;", [$userId], "s", "profilePath");
        $oldProfilePath = str_replace(constant("fileHrefPrefix"), $appDirectory, $oldProfilePath);
        $noProfile = "noProfile.png";

        // If user does not have the default image as its profile //
        if( strpos($oldProfilePath, $noProfile) == false ){
            unlink($oldProfilePath);
        }
    }


    // Function to update the Profile Image of the User //
    function updateProfileImage($databaseConnectionObject, $fileName, $tmpName, $authority, $userId, $instituteId){

        $databaseConnectionObject->select_db("App_Database");

        // Deleting previous profile image //
        deletePreviousProfile($databaseConnectionObject, $userId);

        $profilePathHref = constant("fileHrefPrefix") . "InstituteFolders/". $instituteId . "/" . "profilePhotos/" . $userId . getFileExtension($fileName);

        $query = "UPDATE AppUsers SET profilePath = ? WHERE userId = ?;";
        runQuery($databaseConnectionObject, $query, [$profilePathHref, $userId], "ss");
        
        if( $authority == "root" ){
            $query = "UPDATE Institutes SET profilePath = ? WHERE instituteId = ?;";
            runQuery($databaseConnectionObject, $query, [$profilePathHref, $userId], "ss");    
        }
        
        $filePath =  ("InstituteFolders/" . $instituteId . "/profilePhotos" . "/" . $userId . getFileExtension($fileName));
        $machinePath = getcwd();
        $machinePath = str_replace("Server/Utilities", $filePath, $machinePath);
        move_uploaded_file($tmpName, $machinePath);
    }


    // Function to get the Expiry Date of a Plan From its Start Date and Validity //
    function getPlanExpiryDate($startDate, $validity){

        $planExpiryDate = date_create($startDate);

        // Converting the plan start to plan's end date //
        date_add($planExpiryDate, date_interval_create_from_date_string($validity . " days"));
        return date_format($planExpiryDate, "D, d M Y");
    }


    // Function to get the User/User's Institute Current Plan Details //
    function getUserPlanDetails($databaseConnectionObject, $instituteId){

        $databaseConnectionObject->select_db("App_Database");
        $query = "SELECT planId, planDate, planValidity FROM Institutes WHERE instituteId = ?;";
        $result = runQuery($databaseConnectionObject, $query, [$instituteId], "s");
        $userPlanDetails = $result->fetch_assoc();
        $planStartDate = date_create($userPlanDetails['planDate']);
        $currDate = date("Y-m-d");

        // Converting the plan start to plan's end date //
        date_add($planStartDate, date_interval_create_from_date_string($userPlanDetails['planValidity'] . " days"));

        $userPlanDetails += ['planEndDate'=>date_format($planStartDate, "Y-m-d")]; 
        $userPlanDetails += ['isPlanExpired'=>($currDate <= $userPlanDetails['planEndDate'])? "No" : "Yes"]; 
        return $userPlanDetails;
    }


    // Function to generate the Order Id for an institute //
    function generateOrderId($databaseConnectionObject, $userId){

        $query = "SELECT COUNT(userId) as rechargeCount FROM RechargePayments;";
        $result = runQuery($databaseConnectionObject, $query, [], "");
        $rechargeCount = 1;

        if( $result && $result->num_rows ){
            $rechargeCount = $result->fetch_assoc()['rechargeCount'] + 1;
        }

        return ("ORDREC_" . $userId . "__" . $rechargeCount); 
    }


    // Function to return the Plan Details by Plan Id(Selected) //
    function getPlanDetails($databaseConnectionObject, $planId){

        $query = "SELECT * FROM RechargePlans WHERE planId = ?;";
        $result = runQuery($databaseConnectionObject, $query, [$planId], "i");
        return $result->fetch_assoc();
    }
    
    
    // Function to update the Profile Image of the User //
    function updateInstituteRechargeDetails($databaseConnectionObject, $request){
        
        $databaseConnectionObject->select_db("App_Database");
        $newOrderId = generateOrderId($databaseConnectionObject, $request['loggedInUser']);
        $planDetails = getPlanDetails($databaseConnectionObject, $request['planId']);
        $todayDate = date("Y-m-d");
        
        $query = "INSERT INTO RechargePayments(userId, orderId, paymentId, planId, planAmount, date) VALUES(?,?,?,?,?,?);";
        runQuery($databaseConnectionObject, $query, [$request['loggedInUser'], $newOrderId, $request['paymentId'], $planDetails['planId'], $planDetails['planAmount'], $todayDate], "sssiis", true);
        
        
        $query = "UPDATE Institutes SET planId = ?, planDate = ?, planValidity = ? WHERE instituteId = ?;";
        runQuery($databaseConnectionObject, $query, [$planDetails['planId'], $todayDate, $planDetails['planValidity'], $request['loggedInUser']], "isis");
        
        return $planDetails;
    }
    

    // Function to get all the RechargePlan Details from the database //
    function getRechargePlans($databaseConnectionObject){

        $databaseConnectionObject->select_db("App_Database");
        $query = "SELECT * FROM RechargePlans WHERE planId NOT IN (?);";
        $result = runQuery($databaseConnectionObject, $query, [1], "i");
        $rechargePlans = array();

        while($row = $result->fetch_assoc()){
            $rechargePlans += [$row['planId']=>$row];
        }
        return $rechargePlans;
    }


    // Function to generate a random Alpha-Numeric OTP //
    function generateRandomId($digit){

        $OTP = "";
        for($i=0; $i<$digit; $i++){

            $candidates = array(rand(48, 57), rand(65, 90), rand(97, 122)); // (0-9, A-Z, a-z)
            $selectedIndex = rand(0,2);
            $OTP .= chr($candidates[$selectedIndex]);
        }
        return $OTP;
    }


    // Function to make a new Password Reset Request //
    function createNewPasswordResetRequest($databaseConnectionObject, $userId){

        $digit = 6;
        $OTP = generateRandomId($digit);
        $OTPHashed = password_hash($OTP, PASSWORD_BCRYPT);
        $query = "INSERT INTO PasswordResetRequests(userId, timeStamp, OTP) VALUES(?, ?, ?);";
        runQuery($databaseConnectionObject, $query, [$userId, time(), $OTPHashed], "sss", true);
        return $OTP;
    }


    // Function to check whether a password reset request has made before or not //
    function hasPasswordResetRequestMadeBefore($databaseConnectionObject, $userId){

        // Password Reset Request Validity //
        $passwordResetRequestValidity = 300; // Five Minutes 

        $query = "SELECT * FROM PasswordResetRequests WHERE userId = ?;";
        $result = runQuery($databaseConnectionObject, $query, [$userId], "s");
        
        if( $result && $result->num_rows ){
            
            $arr = $result->fetch_assoc();
            $currentTimeStamp = time();
            
            if( $currentTimeStamp < ($arr['timeStamp'] + $passwordResetRequestValidity) ){
                return true;
            }
            else{
                $query = "DELETE FROM PasswordResetRequests WHERE userId = ?;";
                runQuery($databaseConnectionObject, $query, [$userId], "s", true);
                return false;
            }
        }
        return false;
    }


    // Function to make a Password Reset Request if possible //
    function makePasswordRequest($databaseConnectionObject, $userId){
        
        // Declaring some request response variables //
        $message = "";
        $isRequestMade = false;
        $OTP = "";
        
        $databaseConnectionObject->select_db("App_Database");
        $query = "SELECT email, emailVerified FROM AppUsers WHERE userId = ?;";
        $result = runQuery($databaseConnectionObject, $query, [$userId], "s");

        // If user account does not exists //
        if( !$result || $result->num_rows == 0 ){
            $message = "Invalid UserId !!!";
            $isRequestMade = "false";
        }
        // If user account exists //
        else{

            $arr = $result->fetch_assoc();
            // If the user's email is not verified //
            if( $arr['emailVerified'] == "false" ){
                $message = "Email Linked to Your Account is not Verified !!!";
                $isRequestMade = "false";
            }
            // If the user's email is verified //
            else{
                // If a password reset request has already been in progress //
                if( hasPasswordResetRequestMadeBefore($databaseConnectionObject, $userId) ){
                    $message = "Request Already in Progress !!!";
                    $isRequestMade = "false";
                }
                // Creating a new password reset request //
                else{
                    $OTP = createNewPasswordResetRequest($databaseConnectionObject, $userId);
                    $message = "Password Reset Request Made Successfully !!!";
                    $isRequestMade = "true";
                }
            }
        }
        return ["isRequestMade"=>$isRequestMade, "message"=>$message, "OTP"=>$OTP];
    }


    // Function to Update OTP or Storing the the postVerifyingOTPID key in the database for further user Identification //
    function updateOTPID($databaseConnectionObject, $userId, $hashedID){

        $databaseConnectionObject->select_db("App_Database");
        $query = "UPDATE PasswordResetRequests SET OTP = ?, timeStamp = ? WHERE userId = ?;";
        runQuery($databaseConnectionObject, $query, [$hashedID, time(), $userId], "sss");
    }


    // Function to verify the OTP sent to the User (For Reseting the Password)//
    function verifyOTP($databaseConnectionObject, $request){

        // Password Reset Request Validity //
        $passwordResetRequestValidity = 300; // Five Minutes 
        $isOTPVerified = "false";
        $message = "";
        $postVerifyingOTPID = ""; // a random id will be generated and will work as a security key between the authorized user and server  //

        $databaseConnectionObject->select_db("App_Database");

        // Getting the record of the password change request from the database //
        $query = "SELECT * FROM PasswordResetRequests WHERE userId = ?;";
        $result = runQuery($databaseConnectionObject, $query, [$request['userId']], "s");
        

        // If there is valid request record available in the database // 
        if( $result && $result->num_rows ){
            
            $arr = $result->fetch_assoc();
            $currentTimeStamp = time();
            
            // If the request is currently running //
            if( $currentTimeStamp < ($arr['timeStamp'] + $passwordResetRequestValidity) ){
                
                // If the OTP matches //
                if( password_verify($request['OTP'], $arr["OTP"]) ){
                    
                    $isOTPVerified = "true";
                    $message = "OTP Verified Successfully";
                    $postVerifyingOTPID = generateRandomId(10); // a random is Id between server and authorized user as a security key //
                    
                    // Updating OTP or Storing the the postVerifyingOTPID key in the database for further user Identification //
                    updateOTPID($databaseConnectionObject, $request['userId'], password_hash($postVerifyingOTPID, PASSWORD_BCRYPT));
                }
                // If the OTP does not matches //
                else{
                    $isOTPVerified = "false";
                    $message = "Invalid OTP";
                }
            }
            // If the request has expired //
            else{
                $query = "DELETE FROM PasswordResetRequests WHERE userId = ?;";
                runQuery($databaseConnectionObject, $query, [$request['userId']], "s", true);
                $isOTPVerified = "false";
                $message = "OTP Has Expired !!!";
            }
        }
        // If there is not a valid request record avaailable in the database // 
        else{
            $isOTPVerified = "false";
            $message = "Invalid OTP Request !!!";
        }

        // Returining the OTP verification request response to the user //
        return ["isOTPVerified"=>$isOTPVerified, "message"=>$message, "postVerifyingOTPID"=>$postVerifyingOTPID];
    }


    // Function to Change the password in the Database //
    function updatePassword($databaseConnectionObject, $userId, $newPassword){

        $query = "UPDATE AppUsers SET password = ? WHERE userId = ?;";
        runQuery($databaseConnectionObject, $query, [password_hash($newPassword, PASSWORD_BCRYPT), $userId], "ss", true);
        
        $query = "DELETE FROM PasswordResetRequests WHERE userId = ?;";
        runQuery($databaseConnectionObject, $query, [$userId], "s", true);
    }


    // Function to change the Password if the request is valid //
    function changePassword($databaseConnectionObject, $request){

        $databaseConnectionObject->select_db("App_Database");

        // Password Reset Request Validity //
        $passwordResetRequestValidity = 300; // Five Minutes 
        $isPasswordChanged = "false";
        $message = "";
        
        // Getting the record of the password change request from the database //
        $query = "SELECT * FROM PasswordResetRequests WHERE userId = ?;";
        $result = runQuery($databaseConnectionObject, $query, [$request['userId']], "s");
        

        // If there is valid request record avaailable in the database // 
        if( $result && $result->num_rows ){
            
            $arr = $result->fetch_assoc();
            $currentTimeStamp = time();
            
            // If the request is currently running //
            if( $currentTimeStamp < ($arr['timeStamp'] + $passwordResetRequestValidity) ){
                
                // If the postOTPVerifyingId matches (Authorized User) // 
                if( password_verify($request['postVerifyingOTPID'], $arr["OTP"]) ){
                    
                    // Changing the password in the database //
                    $isPasswordChanged = "true";
                    $message = "Password Changed Successfully !!!";
                    updatePassword($databaseConnectionObject, $request['userId'], $request['newPassword']);
                }
                // If the postOTPVerifyingId does not matches (Unauthorized User) //
                else{
                    $isPasswordChanged = "false";
                    $message = "You are not Authorised to Change the Password !!!";
                }
            }
            // If the request has expired //
            else{
                $query = "DELETE FROM PasswordResetRequests WHERE userId = ?;";
                runQuery($databaseConnectionObject, $query, [$request['userId']], "s", true);
                $isPasswordChanged = "false";
                $message = "Password Change Request Has Expired !!!";
            }
        }
        // If there is not a valid request record avaailable in the database // 
        else{
            $isPasswordChanged = "false";
            $message = "Invalid Password Change Request !!!";
        }

        // returning the password reset request response //
        return ["isPasswordChanged"=>$isPasswordChanged, "message"=>$message];
    }
?>
