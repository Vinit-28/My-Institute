<?php


    // Importing the required files //
    require "./DatabaseConfigurations.php";
    require "./UserUtilities.php";
    require "./InstituteConfigurations.php";


    // Getting the database connection object //
    $databaseConnectionObject = get_DatabaseConnectionObject("App_Database");



    // If the ROOT User has made a request //
    if( isset($_POST['request']) ){
        
        $request = json_decode($_POST['request'], true);
        

        // If the user is a Valid Person //
        if( isUserOnline($databaseConnectionObject, $request['instituteId'], $request['sessionId']) ){

            // If the request is to Add a Person(Student/Teacher) in the Institute's Database //
            if( $request['task'] == 'Add Person' ){
                
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


            // If the request is to Search a person in the Institute's Database //
            else if( $request['task'] == 'Search Person' ){
                
                $relatedPersons = searchUsers($databaseConnectionObject, $request);
                
                $response = array(
                    "result"=>"Success",
                    "relatedPersons"=>$relatedPersons 
                );
                
                // Sending the response //
                echo json_encode($response);
            }
            
            
            // If the request is to Update a Person Details in the Institute's Database //
            else if( $request['task'] == 'Update Person Details' ){
                
                updatePersonDetails($databaseConnectionObject, $request);

                $response = array(
                    "result"=>"Success",
                );
                
                // Sending the response //
                echo json_encode($response);
            }


            // If the request is to Update the Class in the Institute's Database //
            else if( $request['task'] == 'Update Classes' ){
                
                $response = array();
                $databaseConnectionObject->select_db($request['instituteId']);

                if( $request['subtask'] == "Add Class" ){

                    if( isClassExists($databaseConnectionObject, $request['className']) ){
                        $response += ["result"=>"Failed", "message"=>"Class Already Exists !!!"];
                    }
                    else{
                        addClass($databaseConnectionObject, $request);
                        $response += ["result"=>"Success", "message"=>"Class Created Successfully !!!"];
                    }
                }
                else if( $request['subtask'] == "Update Class" ){
                    updateClass($databaseConnectionObject, $request);
                    $response += ["result"=>"Success", "message"=>"Class Updated Successfully !!!"];
                }
                else if( $request['subtask'] == "Delete Classes" ){ 
                    DeleteClasses($databaseConnectionObject, $request);
                    $response += ["result"=>"Success", "message"=>"Class/Classes Deleted Successfully !!!"];
                }
                else if( $request['subtask'] == "Show Classes" ){
                    $classes = getClasses($databaseConnectionObject);
                    $response += ["result"=>"Success", "classes"=>$classes];
                }
                echo json_encode($response);
            }

            else if($request['task'] == "Upload File"){

                if( isset($_FILES['fileToBeUploaded']) ){

                    uploadFileInTheDatabase($databaseConnectionObject, $request, $_FILES['fileToBeUploaded']['name'], $_FILES['fileToBeUploaded']['tmp_name']);
                    $response =array(
                        "result"=>"Success",
                        "message"=>"File Uploaded Successfully !!!"
                    );
                }
                else{
                    $response =array(
                        "result"=>"Failed",
                        "message"=>"Please Select a File to Upload !!!"
                    );
                }
                echo json_encode($response);
            }

            else if($request['task'] == "Show Uploaded Files"){

                $uploadedFiles = getUploadedFiles($databaseConnectionObject, $request);
                $response =array(
                    "result"=>"Success",
                    "uploadedFiles"=>$uploadedFiles
                );
                echo json_encode($response);
            }

            else if($request['task'] == "Delete Uploaded Files"){

                deleteUploadedFiles($databaseConnectionObject, $request);
                
                $response =array(
                    "result"=>"Success",
                    "message"=>"File/Files Deleted Successfully !!!",
                );

                echo json_encode($response);
            }

        }
        // If the user is not a Valid Person //
        else{

            $response = array(
                "result"=>"Failed",
                "message"=>"404 NOT FOUND !!!",
            );
            
            // Sending the response //
            echo json_encode($response);
        }

    }


?>