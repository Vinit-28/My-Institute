
<?php


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
        $studentData += [$result->fetch_assoc()];
        return $studentData;
    }


    // Function to Upload New Assignment //
    function uploadNewAssignment($databaseConnectionObject, $request, $fileName, $tmpName){

        $databaseConnectionObject->select_db($request['instituteId']);

        $filePath = ("InstituteFolders/". $request['instituteId'] . "/" . "uploadedAssignments/" . $request['loggedInUser'] . $request['uploadedDateTime'] . $fileName);

        $machinePath = getcwd();
        $machinePath = str_replace("Server/Utilities", $filePath, $machinePath);
        move_uploaded_file($tmpName, $machinePath);

        $query = "INSERT INTO UploadedAssignments(uploadedBy, subjectName, assignmentTitle, assignmentDescription, 	assignmentDeadline, uploadedDateTime, assignmentVisibility, assignmentFileLinkHref, assignmentFileLinkMachine) Values(?,?,?,?,?,?,?,?,?);";
        
        runQuery($databaseConnectionObject, $query, [$request['uploadedBy'], $request['subjectName'], $request['assignmentTitle'], $request['assignmentDescription'], $request['assignmentDeadline'], $request['uploadedDateTime'], $request['assignmentVisibility'], "http://localhost/" . $filePath, $machinePath ], "sssssssss", true);
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
            $newFilePath = ("InstituteFolders/". $request['instituteId'] . "/" . "uploadedAssignments/" . $request['loggedInUser'] . $request['uploadedDateTime'] . $fileName);

            $machinePath = getcwd();
            $machinePath = str_replace("Server/Utilities", $newFilePath, $machinePath);
            move_uploaded_file($tmpName, $machinePath);
            
            $query =  "UPDATE UploadedAssignments SET assignmentFileLinkMachine = ?, assignmentFileLinkHref = ? WHERE assignmentId = ? ;";
            runQuery($databaseConnectionObject, $query, [$machinePath, ("http://localhost/" . $newFilePath), $request['assignmentId']], "ssi", true);
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

        $databaseConnectionObject->select_db($request['instituteId']);
        $query = "SELECT * FROM AssignmentSubmissions WHERE assignmentId=?;";
        $result = runQuery($databaseConnectionObject, $query, [$request['assignmentId']], "i");
        $submissions = array();
        $counter=1;
        while($row = $result->fetch_assoc()){
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

        $filePath = ("InstituteFolders/". $request['instituteId'] . "/" . "AssignmentsSubmissions/" . $request['loggedInUser'] . $request['submittedDateTime'] . $fileName);

        $machinePath = getcwd();
        $machinePath = str_replace("Server/Utilities", $filePath, $machinePath);
        move_uploaded_file($tmpName, $machinePath);


        $query = "INSERT INTO AssignmentSubmissions(submittedBy, submittedDateTime, assignmentId, submittedFileLinkHref, submittedFileLinkMachine) VALUES(?,?,?,?,?);";

        runQuery($databaseConnectionObject, $query, [$request['loggedInUser'], $request['submittedDateTime'], $request['assignmentId'], "http://localhost/" . $filePath, $machinePath ], "ssiss", true);
    }
    

?>