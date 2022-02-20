
<?php

    include "./readData.php";

    // Function to check whether a Class alraedy exits in the Institute Database or Not //
    function isClassExists($databaseConnectionObject, $className){

        $query = "SELECT * FROM Classes WHERE className = ?;";
        $result = runQuery($databaseConnectionObject, $query, [$className], "s");
        if( $result && $result->num_rows ) return true;
        return false;
    }

    
    // Function to Add a Class in the Institute's Database //
    function addClass($databaseConnectionObject, $request){

        $query = "INSERT INTO Classes(className, fees) VALUES(?,?);";
        runQuery($databaseConnectionObject, $query, [$request['className'], $request['fees']], "si", true);
    }
    
    
    // Function to Update a Class in the Institute's Database //
    function updateClass($databaseConnectionObject, $request){

        $query = "UPDATE Classes SET className = ?, fees = ? WHERE className = ?;";
        runQuery($databaseConnectionObject, $query, [$request['updatedClassInfo']['updatedClassName'], $request['updatedClassInfo']['updatedFees'], $request['updatedClassInfo']['className']], "sis", true);
       
        $query = "UPDATE StudentInfo SET class = ?  WHERE class = ?;";
        runQuery($databaseConnectionObject, $query, [$request['updatedClassInfo']['updatedClassName'], $request['updatedClassInfo']['className']], "ss");
    }
    
    
    // Function to Delete a Class from the Institute's Database //
    function DeleteClasses($databaseConnectionObject, $request){

        $query1 = "DELETE FROM Classes WHERE className = ?;";
        $query2 = "UPDATE StudentInfo SET class = ? WHERE class = ?;";
        for($i=0; $i<count($request['selectedClasses']); $i++){
            runQuery($databaseConnectionObject, $query1, [$request['selectedClasses'][$i]], "s", true);
            runQuery($databaseConnectionObject, $query2, ["Class", $request['selectedClasses'][$i]], "ss");
        }
    }
    
    
    // Function to get the classes from the Institute's Database //
    function getClasses($databaseConnectionObject){

        $classes = array();
        $counter = 1;
        $query = "SELECT * FROM Classes;";
        $result = runQuery($databaseConnectionObject, $query, [], "");
        while($row = $result->fetch_assoc()){
            $classes += ["'$counter'" => $row];
            $counter+=1;
        }
        return $classes;
    }


    // Function to create a Live Class //
    function createLiveClass($databaseConnectionObject, $request){

        $query = "INSERT INTO LiveClasses(hostName, teacherName, subjectName, topicName, topicDescription, startingTime, endingTime, classDate, joiningLink, liveClassVisibility) VALUES(?,?,?,?,?,?,?,?,?,?);";
        runQuery($databaseConnectionObject, $query, [$request['hostName'], $request['teacherName'], $request['subjectName'], $request['topicName'], $request['topicDescription'], $request['startingTime'], $request['endingTime'], $request['classDate'], $request['joiningLink'], $request['liveClassVisibility']], "ssssssssss", true);
        
    }


    // Function to get the hosted classes //
    function getLiveClasses($databaseConnectionObject, $request){

        $query = "SELECT * FROM LiveClasses;";
        $result = runQuery($databaseConnectionObject, $query, [], "");
        $liveClasses = array();
        $counter = 1;
        while($row = $result->fetch_assoc()){
            $liveClasses += ["'$counter'" => $row];
            $counter+=1;
        }
        return $liveClasses;
    }
    
    
    // Function to get the hosted classes //
    function deleteLiveClasses($databaseConnectionObject, $request){

        $query = "DELETE FROM LiveClasses WHERE liveClassId = ?;";

        for($i=0; $i<count($request['selectedLiveClasses']); $i++){
            
            runQuery($databaseConnectionObject, $query, [$request['selectedLiveClasses'][$i]], "i", true);
        }
    }


    // Function to get the Institute Data //
    function getInstituteData($databaseConnectionObject, $request){

        $query = "SELECT * FROM Institutes WHERE InstituteId = ?;";
        $instituteData = array();
        $result = runQuery($databaseConnectionObject, $query, [$request['instituteId']], "s");
        $instituteData += [$result->fetch_assoc()];
        return $instituteData;
    }


    // Function to get the Teacher Data //
    function getTeacherData($databaseConnectionObject, $request){
        
        $databaseConnectionObject->select_db($request['instituteId']);
        $query = "SELECT * FROM TeacherInfo WHERE userId = ?;";
        $teacherData = array();
        $result = runQuery($databaseConnectionObject, $query, [$request['loggedInUser']], "s");
        $teacherData += [$result->fetch_assoc()];
        return $teacherData;
    }
    
    
    // Function to get the Student Data //
    function getStudentData($databaseConnectionObject, $request){
        
        $databaseConnectionObject->select_db($request['instituteId']);
        $query = "SELECT * FROM StudentInfo WHERE userId = ?;";
        $studentData = array();
        $result = runQuery($databaseConnectionObject, $query, [$request['loggedInUser']], "s");
        $row = $result->fetch_assoc();
        $row += ["totalFee" => getColumnValue($databaseConnectionObject, "SELECT * FROM Classes WHERE className = ?;", [$row['class']], "s", "fees")];
        $studentData += [$row];
        return $studentData;
    }


    // Function to Upload New Assignment //
    function uploadNewAssignment($databaseConnectionObject, $request, $fileName, $tmpName){

        $databaseConnectionObject->select_db($request['instituteId']);

        $filePath = ("InstituteFolders/". $request['instituteId'] . "/" . "uploadedAssignments/" . $request['loggedInUser'] . "__" . time() . "__" . $fileName);

        $machinePath = getcwd();
        $machinePath = str_replace("Server/Utilities", $filePath, $machinePath);
        move_uploaded_file($tmpName, $machinePath);

        $query = "INSERT INTO UploadedAssignments(uploadedBy, subjectName, assignmentTitle, assignmentDescription, 	assignmentDeadline, uploadedDateTime, assignmentVisibility, assignmentFileLinkHref, assignmentFileLinkMachine) Values(?,?,?,?,?,?,?,?,?);";
        
        runQuery($databaseConnectionObject, $query, [$request['uploadedBy'], $request['subjectName'], $request['assignmentTitle'], $request['assignmentDescription'], $request['assignmentDeadline'], $request['uploadedDateTime'], $request['assignmentVisibility'], constant("fileHrefPrefix") . $filePath, $machinePath ], "sssssssss", true);
    }



    // Function to Edit Uploaded Assignment //
    function updateUploadedAssignment($databaseConnectionObject, $request, $fileName="", $tmpName=""){

        $databaseConnectionObject->select_db($request['instituteId']);

        $query = "UPDATE UploadedAssignments SET subjectName=?, assignmenttitle=?, assignmentDescription=?, assignmentDeadline=?, assignmentVisibility=? WHERE assignmentId=?;";
        
        runQuery($databaseConnectionObject, $query, [$request['subjectName'], $request['assignmentTitle'], $request['assignmentDescription'], $request['assignmentDeadline'], $request['assignmentVisibility'], $request['assignmentId']], "sssssi");

        // If the file has been edited //
        if( $fileName != "" ){

            // Removing the Old File //
            $oldFilePath = getColumnValue($databaseConnectionObject, "SELECT * FROM UploadedAssignments WHERE assignmentId = ?;", [$request['assignmentId']], "i", "assignmentFileLinkMachine");
            unlink($oldFilePath);

            // Creating and storing New File //
            $newFilePath = ("InstituteFolders/". $request['instituteId'] . "/" . "uploadedAssignments/" . $request['loggedInUser'] . "__" . time() . "__" . $fileName);

            $machinePath = getcwd();
            $machinePath = str_replace("Server/Utilities", $newFilePath, $machinePath);
            move_uploaded_file($tmpName, $machinePath);
            
            $query =  "UPDATE UploadedAssignments SET assignmentFileLinkMachine = ?, assignmentFileLinkHref = ? WHERE assignmentId = ? ;";
            runQuery($databaseConnectionObject, $query, [$machinePath, (constant("fileHrefPrefix") . $newFilePath), $request['assignmentId']], "ssi", true);
        }
    }


    // Function to Delete Uploaded Assignment //
    function deleteUploadedAssignment($databaseConnectionObject, $request){

        $databaseConnectionObject->select_db($request['instituteId']);

        $query1 = "SELECT * FROM AssignmentSubmissions WHERE assignmentId=?;";
        $query2 = "DELETE FROM AssignmentSubmissions WHERE assignmentId=?;";
        $query3 = "SELECT * FROM UploadedAssignments WHERE assignmentId = ?;";
        $query4 = "DELETE FROM UploadedAssignments WHERE assignmentId=?;";

        for($i=0;$i<count($request['selectedAssignments']);$i++){

            $assignmentId = $request['selectedAssignments'][$i];
            
            // Removing the Submissions of the Assignment and its Entry from the Database //
            $result = runQuery($databaseConnectionObject, $query1, [$assignmentId], "i");
            while($row = $result->fetch_assoc()){
                unlink($row['submittedFileLinkMachine']);
            }
            runQuery($databaseConnectionObject, $query2, [$assignmentId], "i");

            // Removing the Assignment File and Assignment Entry from the Database //
            $result = runQuery($databaseConnectionObject, $query3, [$assignmentId], "i");
            unlink($result->fetch_assoc()['assignmentFileLinkMachine']);
            runQuery($databaseConnectionObject, $query4, [$assignmentId], "i", true);
        }
    }



    // Function to check whether a assignment is submitted by a student or not // 
    function isAssignmentSubmitted($databaseConnectionObject, $assignmentId, $studentId){

        $query = "SELECT * FROM AssignmentSubmissions WHERE assignmentId=? and submittedBy=?;";
        $result = runQuery($databaseConnectionObject, $query, [$assignmentId, $studentId], "is");

        if ( $result && $result->num_rows == 1 ){
            return [true, $result->fetch_assoc()['submittedFileLinkHref']];
        }
        return [false, ""];
    }
    

    // Function to show all the assignments to the student //
    function getUploadedAssignmentsForTeachers($databaseConnectionObject, $request){

        $databaseConnectionObject->select_db($request['instituteId']);
        $query = "SELECT * FROM UploadedAssignments WHERE uploadedBy = ?;";
        $result = runQuery($databaseConnectionObject, $query, [$request['loggedInUser']], "s");
        $assignments = array();
        $counter=1;
        while($row = $result->fetch_assoc()){
            $assignments += [$counter=>$row];
            $counter+=1;
        }

        return $assignments;
    }


    // Function to show all the assignments to the student //
    function getUploadedAssignmentsForStudents($databaseConnectionObject, $request){

        $databaseConnectionObject->select_db($request['instituteId']);
        $query = "SELECT * FROM UploadedAssignments WHERE assignmentVisibility = ?;";
        $result = runQuery($databaseConnectionObject, $query, [$request['studentClass']], "s");
        $assignments = array();
        $counter=1;
        while($row = $result->fetch_assoc()){

            $res = isAssignmentSubmitted($databaseConnectionObject, $row['assignmentId'], $request['loggedInUser']);
            $row += ["isSubmitted"=>$res[0], "submittedFileLinkHref"=>$res[1]];
            $assignments += [$counter=>$row];
            $counter+=1;
        }

        return $assignments;
    }


    // Function to get all the Submissions of a particular Assignment // 
    function getAssignmentSubmissions($databaseConnectionObject, $request){

        $tempAppDatabase = get_DatabaseConnectionObject("App_Database"); //Making another Database Object for Profile of the Students Who Submitted Assignment //
        $databaseConnectionObject->select_db($request['instituteId']);
        $query = "SELECT * FROM AssignmentSubmissions WHERE assignmentId=?;";
        $result = runQuery($databaseConnectionObject, $query, [$request['assignmentId']], "i");
        $submissions = array();
        $counter=1;
        while($row = $result->fetch_assoc()){
            
            $row += ["studentName"=>getColumnValue($databaseConnectionObject, "SELECT * FROM StudentInfo WHERE userId = ?;",[$row['submittedBy']], "s", "name"), "profilePath"=>getColumnValue($tempAppDatabase, "SELECT * FROM AppUsers WHERE userId = ?;",[$row['submittedBy']], "s", "profilePath")];

            $submissions += [$counter=>$row];
            $counter+=1;
        }
        return $submissions;
    }


    // Function to Delete the Assignment Submission of a Student // 
    function deleteAssignmentSubmission($databaseConnectionObject, $request){

        $databaseConnectionObject->select_db($request['instituteId']);

        // Removing the Submissions made to that particular Assignment //
        $query = "SELECT * FROM AssignmentSubmissions WHERE submittedBy=? AND assignmentId=?;";
        $result = runQuery($databaseConnectionObject, $query, [$request['submittedBy'], $request['assignmentId']], "si");
        
        // Removing from the Machine //
        while($row = $result->fetch_assoc()){
            unlink($row['submittedFileLinkMachine']);
        }
        
        // Removing from the Database //
        $query = "DELETE FROM AssignmentSubmissions WHERE submittedBy=? AND assignmentId=?;";
        $result = runQuery($databaseConnectionObject, $query, [$request['submittedBy'], $request['assignmentId']], "si", true);
    }


    // Function to submit the Assignment //
    function submitAssignment($databaseConnectionObject, $request, $fileName, $tmpName){

        $databaseConnectionObject->select_db($request['instituteId']);

        $filePath = ("InstituteFolders/". $request['instituteId'] . "/" . "assignmentsSubmissions/" . $request['loggedInUser'] . "__" . time() . "__" . $fileName);

        $machinePath = getcwd();
        $machinePath = str_replace("Server/Utilities", $filePath, $machinePath);
        move_uploaded_file($tmpName, $machinePath);


        $query = "INSERT INTO AssignmentSubmissions(submittedBy, submittedDateTime, assignmentId, submittedFileLinkHref, submittedFileLinkMachine) VALUES(?,?,?,?,?);";

        runQuery($databaseConnectionObject, $query, [$request['loggedInUser'], $request['submittedDateTime'], $request['assignmentId'], constant("fileHrefPrefix") . $filePath, $machinePath ], "ssiss", true);
    }
    

    // Function to create a table if that table is not created //
    function createTableIfNotCreated($databaseConnectionObject, $instituteId, $tableName){

        $databaseConnectionObject->select_db($instituteId);
        $query = "SHOW TABLES;";
        $isTableCreated = false;

        // If Query executed Successfully //
        if( $result = runQuery($databaseConnectionObject, $query, [], "") ){
            while($row = $result->fetch_assoc()){
                if( $row["Tables_in_".$instituteId] == $tableName ){
                    $isTableCreated = true;
                    break;
                }
            }
        }

        // If table is not Created //
        if( !$isTableCreated ){
            // Making a YearYearNo. Table which will store all attendance related information of the specified year // 
            $query = "CREATE TABLE $tableName(
                userId VARCHAR(100), 
                name VARCHAR(100), 
                class VARCHAR(100), 
                attendanceDate DATE, 
                updatedBy VARCHAR(100),
                status VARCHAR(100)
                );";
            runQuery($databaseConnectionObject, $query, [], "");
        }  
    }


    // Function to check whether a person's Attendance entry is in the Table or Not // 
    function updateAttendanceIfExists($databaseConnectionObject, $personDetails, $attendanceDate, $tableName, $loggedInUser){

        $query = "SELECT * FROM $tableName WHERE userId = ? AND attendanceDate = ?;";
        $result = runQuery($databaseConnectionObject, $query, [$personDetails['userId'], $attendanceDate], "ss");
        if($result && $result->num_rows){
            $query = "UPDATE $tableName SET updatedBy = ?, status = ? WHERE userId = ? AND attendanceDate = ?;";
            runQuery($databaseConnectionObject, $query, [$loggedInUser, $personDetails['status'], $personDetails['userId'], $attendanceDate], "ssss");
            return true;
        }
        return false;
    }


    // Function to Make a Attendance Entry of a Particular Person in the Table // 
    function makeAttendanceEntry($databaseConnectionObject, $personDetails, $attendanceDate, $tableName, $className, $loggedInUser){
        
        $query = "INSERT INTO $tableName(userId, name, class, attendanceDate, updatedBy, status) VALUES(?,?,?,?,?,?);";
        runQuery($databaseConnectionObject, $query, [$personDetails['userId'], $personDetails['name'], $className, $attendanceDate, $loggedInUser, $personDetails['status']], "ssssss", true);
    }
    

    // Function to Set the Attendance in the Database //
    function setOrUpdateAttendance($databaseConnectionObject, $request){

        $databaseConnectionObject->select_db($request['instituteId']);
        $tableName = "Year" . $request['year'];
        createTableIfNotCreated($databaseConnectionObject, $request['instituteId'], $tableName);
        
        foreach($request['persons'] as $person){

            if( ! updateAttendanceIfExists($databaseConnectionObject, $person, $request['date'], $tableName, $request['loggedInUser']) ){
                makeAttendanceEntry($databaseConnectionObject, $person, $request['date'], $tableName, $request['class'], $request['loggedInUser']);
            }
        }
    }


    // Function to get the Attendance Status and If Attendance entry not exists then it will create default "Attendance Entry" that is "status = Not-Set, updatedBy = Bot" //
    function getAttendanceStatus($databaseConnectionObject, $tableName, $request, $personId, $personName){

        $query = "SELECT * FROM $tableName WHERE userId = ? AND attendanceDate = ?;";
        $result = runQuery($databaseConnectionObject, $query, [$personId, $request['date']], "ss");

        if( $result && $result->num_rows ){
            return $result->fetch_assoc()['status'];
        }

        makeAttendanceEntry($databaseConnectionObject, ['userId'=>$personId, 'name'=>$personName, 'status'=>'Not-Set'], $request['date'], $tableName, $request['class'], "Bot");
        return getAttendanceStatus($databaseConnectionObject, $tableName, $request, $personId, $personName);
    }


    // Function to Get the Attendance From the Database //
    function getAttendance($databaseConnectionObject, $request){

        $databaseConnectionObject->select_db($request['instituteId']);
        $tempDatabaseConnectionObject = get_DatabaseConnectionObject("App_Database");

        $tableName = "Year" . $request['year'];
        createTableIfNotCreated($databaseConnectionObject, $request['instituteId'], $tableName);
        
        $query = "SELECT userId, name FROM StudentInfo WHERE class = ?;";
        $result = runQuery($databaseConnectionObject, $query, [$request['class']], "s");
        $personAttendance = array();
        $counter = 1;


        while($row = $result->fetch_assoc()){
            $row += ["status"=>getAttendanceStatus($databaseConnectionObject, $tableName, $request, $row['userId'], $row['name']), "profilePath" => getColumnValue($tempDatabaseConnectionObject, "SELECT * FROM AppUsers WHERE userId = ?;",[$row['userId']], "s", "profilePath"), "class"=>$request['class']];

            $personAttendance += [$counter=>$row];
            $counter += 1;
        }

        return $personAttendance;
    }


    // Function to get the attendance of a person between the specified dates //
    function getAttendanceBetweenDates($databaseConnectionObject, $userId, $startingDate, $endingDate, $tableName){
        
        $personAttendance = array();

        // Getting all the Records //
        $query = "SELECT * FROM $tableName WHERE attendanceDate BETWEEN ? AND ? AND userId = ? AND  status <> ?;";
        $result = runQuery($databaseConnectionObject, $query, [$startingDate, $endingDate, $userId, "Not-Set"], "ssss");
        $personAttendance += ['attendanceRecords'=>$result->fetch_all(MYSQLI_ASSOC)];
        
        // Getting Count of Presents //
        $query = "SELECT COUNT(status) as presents FROM $tableName WHERE attendanceDate >= ? AND attendanceDate <= ? AND userId = ? AND  status = ?;";
        $result = runQuery($databaseConnectionObject, $query, [$startingDate, $endingDate, $userId, "present"], "ssss");

        $personAttendance += ["presents"=>$result->fetch_assoc()["presents"]];
        $personAttendance += ["absents"=> (count($personAttendance['attendanceRecords']) - $personAttendance["presents"])];
        $personAttendance += ["totalDays"=> $personAttendance['presents'] + $personAttendance["absents"]];
        return $personAttendance;
    }


    // Function to combine the small part of attendance into the larger part // (Used in Combining the Attendance of Two Different Years)
    function combineAttendance(&$personAttendance, &$newAttendance, &$recordsLength){

        $personAttendance['presents'] += $newAttendance['presents'];
        $personAttendance['absents'] += $newAttendance['absents'];
        $personAttendance['totalDays'] += $newAttendance['totalDays'];

        foreach($newAttendance['attendanceRecords'] as $key=>$value){
            $personAttendance['attendanceRecords'] += [$recordsLength=>$value];
            $recordsLength += 1;
        }
    }


    // Function to Get the Attendance of a Particular Person From the Database //
    function getParticularPersonAttendance($databaseConnectionObject, $request){

        $databaseConnectionObject->select_db($request['instituteId']);
        $startingDate = $request['fromYear'] . "-" . $request['fromMonth'] . "-" . $request['fromDate'];
        $endingDate = $request['toYear'] . "-" . $request['toMonth'] . "-" . $request['toDate'];


        // If Years of Starting Date & Ending Date do not Matches //
        if( $request['fromYear'] != $request['toYear'] ){
            
            $personAttendance = array();
            $personAttendance += ['presents' => 0, 'absents'=>0, 'totalDays'=>0, 'attendanceRecords'=>array()];
            $recordsLength = 1;

            // Getting the Starting Year's Attendance //
            createTableIfNotCreated($databaseConnectionObject, $request['instituteId'], "Year".$request['fromYear']);
            $startYearAtt = getAttendanceBetweenDates($databaseConnectionObject, $request['forUser'], $startingDate, $request['fromYear']."-"."12-31", "Year".$request['fromYear']);
            
            combineAttendance($personAttendance, $startYearAtt, $recordsLength);


            // Getting the Middle Years Attendance If Any Middle Year Exists Between Starting & Ending Date //
            $currYear = ((int)$request['fromYear'])+1;
            $endingYear = ((int)$request['toYear']);
            
            while($currYear < $endingYear){

                // Getting the Current/Middle Year's Attendance //
                createTableIfNotCreated($databaseConnectionObject, $request['instituteId'], "Year".$currYear);
                $middleYearAtt = getAttendanceBetweenDates($databaseConnectionObject, $request['forUser'], $currYear."-1-1", $currYear . "-12-31", "Year".$currYear);
                
                combineAttendance($personAttendance, $middleYearAtt, $recordsLength);
                $currYear+=1;
            }


            // Getting the Last Year's Attendance //
            createTableIfNotCreated($databaseConnectionObject, $request['instituteId'], "Year".$request['toYear']);
            $lastYearAtt = getAttendanceBetweenDates($databaseConnectionObject, $request['forUser'], $request['toYear']."-"."1-1", $endingDate, "Year".$request['toYear']);

            combineAttendance($personAttendance, $lastYearAtt, $recordsLength);

            return $personAttendance;
        }
        
        createTableIfNotCreated($databaseConnectionObject, $request['instituteId'], "Year".$request['fromYear']);
        return getAttendanceBetweenDates($databaseConnectionObject, $request['forUser'], $startingDate, $endingDate, "Year".$request['fromYear']);
    }


    // Function to Create a Class if the Specified Class does not Exists //
    function createClassIfNotCreated($databaseConnectionObject, $className, $instituteId){

        $databaseConnectionObject->select_db($instituteId);
        $query = "SELECT * FROM Classes WHERE className = ?;";
        $result = runQuery($databaseConnectionObject, $query, [$className], "s");

        // If that Class is Not Created //
        if( !($result && $result->num_rows) ){
            $query = "INSERT INTO Classes(className, fees) VALUES(?,?);";
            $result = runQuery($databaseConnectionObject, $query, [$className, 0], "si", true);
        }   
    }
    

    // Function to Read Add Persons Excel File and Will also insert into the Institute's Database //
    function readAddPersonsExcelFile($databaseConnectionObject, $request, $fileName, $tmpName){

        $databaseConnectionObject->select_db($request['instituteId']);

        // $filePath = ("InstituteFolders/". $request['instituteId'] . "/" . "temporaryDocuments/" . $fileName);

        // $machinePath = getcwd();
        // $machinePath = str_replace("Server/Utilities", $filePath, $machinePath);
        // move_uploaded_file($tmpName, $machinePath);
        
        // Reading the Excel File Data and Adding Persons in the Institute's Database //
        // $response = readData($databaseConnectionObject, $machinePath, $request['instituteId']);
        $response = readData($databaseConnectionObject, $tmpName, $request['instituteId']);

        // Removing the Excel File from the Server // 
        // unlink($machinePath);

        return $response;
    }



    function replaceThisFunctionWithAmansFileCheckFunction($filePath){

        // return true/false;
        return true;
    }


    // Function to make entry for the test to be scheduled //
    function makeEntryForOnlineTest($databaseConnectionObject, $request, $filePathHref, $filePathMachine){

        $databaseConnectionObject->select_db($request['instituteId']);
        $query = "INSERT INTO UploadedTest(uploadedBy, uploadedDateTime, subjectName, topicName, testDate, forClass, fromTime, toTime, questionGapSec, testFileLinkHref, testFileLinkMachine) VALUES(?,?,?,?,?,?,?,?,?,?,?);";

        runQuery($databaseConnectionObject, $query, [$request['loggedInUser'], date("Y-m-d"), $request['subjectName'], $request['topicName'], $request['testDate'], $request['forClass'], $request['fromTime'], $request['toTime'], $request['questionGapSec'], $filePathHref, $filePathMachine], "sssssssssss", true);
    }


    // Function to upload a test for students //
    function uploadTest($databaseConnectionObject, $request, $fileName, $tmpName){

        $databaseConnectionObject->select_db($request['instituteId']);
        $result = $message = "";

        // If file uploaded by the teacher is readed successfully //
        if( replaceThisFunctionWithAmansFileCheckFunction($tmpName) ){
            
            // Moving the file in the institute's folder/data-warehouse //
            $fileSuffixPath = ("InstituteFolders/". $request['instituteId'] . "/" . "uploadedTests/" . $request['loggedInUser'] . "__" . time() . "__" . $fileName);

            $filePathMachine = str_replace("Server/Utilities", $fileSuffixPath, getcwd());

            move_uploaded_file($tmpName, $filePathMachine);
            $filePathHref = constant("fileHrefPrefix") . $fileSuffixPath;

            // Making entry in the database //
            makeEntryForOnlineTest($databaseConnectionObject, $request, $filePathHref, $filePathMachine);
            
            $result = "Success";
            $message = "Test Created Successfully !!!";
        }
        // If any error occured or file is not up to the standards //
        else{
            $result = "Failed";
            $message = "Error While Reading The File !!!";
        }

        return array(
            "result"=>$result,
            "message"=>$message
        );
    }


    // Function to Delete the uploaded tests //
    function deleteUploadedTests($databaseConnectionObject, $request){

        $databaseConnectionObject->select_db($request['instituteId']);
        $testId = $request['testId'];

        // Getting the Test File Link //
        $testFileLink = getColumnValue($databaseConnectionObject, "SELECT testFileLinkMachine FROM UploadedTest WHERE testId = ?;", [$testId], "i", "testFileLinkMachine");

        // Deleting the test file and the entry of test from the database //
        unlink($testFileLink);
        $query = "DELETE FROM UploadedTest WHERE testId = ?;";
        runQuery($databaseConnectionObject, $query, [$testId], "i", true);
        
        // Deleting students result of this test //
        $query = "DELETE FROM testSubmission WHERE testId = ?;";
        runQuery($databaseConnectionObject, $query, [$testId], "i");
    }


    // Function to get all the uploaded tests by a teacher (For Teachers) //
    function getUploadedTests_Teachers($databaseConnectionObject, $request){

        $databaseConnectionObject->select_db($request['instituteId']);

        $query = "SELECT * FROM UploadedTest WHERE uploadedBy = ?;";
        $result = runQuery($databaseConnectionObject, $query, [$request['loggedInUser']], "s");
        $uploadedTests = array();

        while($row = $result->fetch_assoc()){
            $uploadedTests += [$row['testId']=>$row];
        }

        return $uploadedTests;
    }


    // Function to check whether the student has attempted the test or not //
    function isTestAttempted($databaseConnectionObject, $testId, $userId){

        $testResult = array();
        $isStudentAttemptedTest = false;
        $query = "SELECT * FROM testSubmission WHERE testId = ? AND submittedBy = ?;";
        $result = runQuery($databaseConnectionObject, $query, [$testId, $userId], "is");

        // If student has attempted the test //
        if( ($result && $result->num_rows) ){
            $isStudentAttemptedTest = true;
            $testResult = $result->fetch_assoc();
        }

        return ["isStudentAttemptedTest" => $isStudentAttemptedTest, "testResult" => $testResult];
    }   


    // Function to get all the uploaded tests by a teacher (For Students) //
    function getUploadedTests_Students($databaseConnectionObject, $request){

        $databaseConnectionObject->select_db($request['instituteId']);

        $todayDate = date("Y-m-d");
        $query = "SELECT * FROM UploadedTest WHERE forClass = ? AND testDate >= ?;";
        $result = runQuery($databaseConnectionObject, $query, [$request['studentClass'], $todayDate], "ss");
        $uploadedTest = array();

        while($row = $result->fetch_assoc()){

            $testAttemptedDetails = isTestAttempted($databaseConnectionObject, $row['testId'], $request['loggedInUser']);

            $row += ['isTestAttempted' => ($testAttemptedDetails['isStudentAttemptedTest'])? "true" : "false" ];
            $uploadedTest += [$row['testId']=>$row];
        }

        return $uploadedTest;
    }


    // Function to get the result of a test (For Teachers) //
    function getTestsMarks_Teachers($databaseConnectionObject, $request){

        $databaseConnectionObject->select_db($request['instituteId']);
        $query = "SELECT * FROM testSubmission WHERE testId = ?;";
        $result = runQuery($databaseConnectionObject, $query, [$request['testId']], "i");

        $testResult = array();

        while($row = $result->fetch_assoc()){
            $row += ['profilePath' => getUserProfilePath($databaseConnectionObject, $request['instituteId'], $row['submittedBy'])];
            $testResult += [$row['submittedBy']=>$row];
        }

        return $testResult;
    }


    // Function to get the result of the uploaded tests (For Students) //
    function getTestsMarks_Students($databaseConnectionObject, $request){

        $databaseConnectionObject->select_db($request['instituteId']);
        $todayDate = date("Y-m-d");
        $query = "SELECT * FROM UploadedTest WHERE forClass = ? AND testDate <= ?;";
        $result = runQuery($databaseConnectionObject, $query, [$request['studentClass'], $todayDate], "ss");

        $testResult = array();

        while($test = $result->fetch_assoc()){

            // Checking whether the student has attempted the test or not //
            $testAttemptedDetails = isTestAttempted($databaseConnectionObject, $test['testId'], $request['loggedInUser']);

            // If student has attempted the test //
            if( $testAttemptedDetails['isTestAttempted'] ){
                $testResult += [$test['testId']=>$testAttemptedDetails['testResult']];
            }
        }
        return $testResult;
    }





?>