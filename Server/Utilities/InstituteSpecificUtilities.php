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
        if( isUserOnline($databaseConnectionObject, $request['loggedInUser'], $request['sessionId']) ){

            $authority = getColumnValue($databaseConnectionObject, "SELECT * FROM AppUsers WHERE userId = ?", [$request['loggedInUser']], "s", "authority");

            // If the request is to Add a Person(Student/Teacher) in the Institute's Database //
            if( $request['task'] == 'Add Person' && $authority == "root" ){
                
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
            else if( $request['task'] == 'Search Person' && ($authority == "root" || $authority == "teacher" ) ){
                
                $relatedPersons = searchUsers($databaseConnectionObject, $request);
                
                $response = array(
                    "result"=>"Success",
                    "relatedPersons"=>$relatedPersons 
                );
                
                // Sending the response //
                echo json_encode($response);
            }
            
            
            // If the request is to Update a Person Details in the Institute's Database //
            else if( $request['task'] == 'Update Person Details' && ($authority == "root" || $authority == "teacher" ) ){
                
                updatePersonDetails($databaseConnectionObject, $request);

                $response = array(
                    "result"=>"Success",
                    "message"=>"Person Details Updated Successfully !!!"
                );
                
                // Sending the response //
                echo json_encode($response);
            }


            // If the request is to Update the Class in the Institute's Database //
            else if( $request['task'] == 'Update Classes' && $authority == "root" ){
                
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

            // If the request is to Upload Files in the Institute's Database //
            else if($request['task'] == "Upload File" && ($authority == "root" || $authority == "teacher" ) ){

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

            // If the request is to Get the Uploaded Files from the Institute's Database //
            else if($request['task'] == "Show Uploaded Files" ){

                $uploadedFiles = getUploadedFiles($databaseConnectionObject, $request);
                $response =array(
                    "result"=>"Success",
                    "uploadedFiles"=>$uploadedFiles
                );
                echo json_encode($response);
            }

            // If the request is to Delete Uploaded File/Files From the Institute's Database //
            else if($request['task'] == "Delete Uploaded Files" && ($authority == "root" || $authority == "teacher" ) ){

                deleteUploadedFiles($databaseConnectionObject, $request);
                
                $response =array(
                    "result"=>"Success",
                    "message"=>"File/Files Deleted Successfully !!!",
                );

                echo json_encode($response);
            }

            // If the request is to Create a Live Class //
            else if($request['task'] == "Create Live Class" && ($authority == "root" || $authority == "teacher" ) ){

                $databaseConnectionObject->select_db($request['instituteId']);
                createLiveClass($databaseConnectionObject, $request);
                
                $response =array(
                    "result"=>"Success",
                    "message"=>"Live Class Created Successfully !!!",
                );

                echo json_encode($response);
            }

            // If the request is to Get all the Hosted Classes by the User //
            else if($request['task'] == "Get Live Classes" ){

                $databaseConnectionObject->select_db($request['instituteId']);
                $liveClasses = getLiveClasses($databaseConnectionObject, $request);
                
                $response =array(
                    "result"=>"Success",
                    "liveClasses"=>$liveClasses,
                );

                echo json_encode($response);
            }

            // If the request is to Delete the Hosted Classes by the User //
            else if($request['task'] == "Delete Live Classes" && ($authority == "root" || $authority == "teacher" ) ){

                $databaseConnectionObject->select_db($request['instituteId']);
                deleteLiveClasses($databaseConnectionObject, $request);

                $response =array(
                    "result"=>"Success",
                    "message"=>"Live Class/Classes Deleted successfully !!!",
                );

                echo json_encode($response);
            }
            
            // If request is to get the Institute Data //
            else if($request['task'] == "Get Institute Data" && $authority == "root" ){

                $databaseConnectionObject->select_db("App_Database");
                $instituteData = getInstituteData($databaseConnectionObject, $request);
                $response =array(
                    "result"=>"Success",
                    "instituteData"=>$instituteData
                );

                echo json_encode($response);
            }

            // If request is to Update the User Profile //
            else if($request['task'] == "Update My Profile" ){

                updateMyProfile($databaseConnectionObject, $request, $authority);
                
                // If Profile Image has to be changed //
                if( isset($_FILES['profileImg']) ){
                    updateProfileImage($databaseConnectionObject, $_FILES['profileImg']['name'], $_FILES['profileImg']['tmp_name'], $authority, $request['loggedInUser'], $request['instituteId']);
                }

                $response =array(
                    "result"=>"Success",
                    "message"=>"Profile Updated Successfully !!!"
                );
                echo json_encode($response);
            }

            // If request is not valid //
            else{
                $response = array(
                    "Not able to fulfill your request !!!"
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