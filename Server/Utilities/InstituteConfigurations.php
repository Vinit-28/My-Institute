
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

?>