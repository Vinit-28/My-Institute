<?php

include './excel_reader.php';

$headings = array(
    "INSTITUTE_ID",
    "NAME",
    "EMAIL",
    "GENDER",
    "DESIGNATION",
    "CLASS",
    "PHONE",
    "AADHAR",
    "ADDRESS",
    "CITY",
    "STATE",
    "PIN",
    "PASSWORD"
);

function check_headings($sheet)
{

    global $headings;

    $x = 1;
    $accept = 1;
    while ($x <= 11) {
        if (strtoupper($sheet['cells'][1][$x]) == $headings[$x - 1]) {
        } else {
            $accept = 0;
            break;
        }
        $x++;
    }
    return $accept;
}



function checkemail($str) {
    return (!preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $str)) ? FALSE : TRUE;
}

function validate_row($array)
{

    global $instituteID;

    
    $row = array();
    foreach ($array as $key => $v) 
    {
        array_push($row, $v);
    }


    $accept = 1;

    if($instituteID != trim($row[0]) )
    {
        return array( 0 , 0);
    }



    $name = trim($row[1]);
    if(strlen($name)<=0)
        return array(0 , 1);

    for ($i = 0; $accept && $i < strlen($name); $i++) {

        if (($name[$i] >= 'a' && $name[$i] <= 'z') || ($name[$i] >= 'A' && $name[$i] <= 'Z') || ($name[$i] == " ")) {} 
        else 
        {
            return array(0 , 1);
            break;            
        }
    }


    $email = trim($row[2]);
    if(strlen($email)<=0)
        return array(0 , 2);

    if(!checkemail($email)){
        return array(0,2);
    }



    $gender = strtoupper($row[3]);
    if ($gender == "MALE" || $gender == "FEMALE" || $gender == "OTHER") {
    } else {
        return array(0 , 3);
    }



    $designation = strtoupper($row[4]);
    if ($designation == "STUDENT" || $designation == "TEACHER") {
    } else {
        return array(0 , 4);
    }
    
    if(trim($row[5]) == " " || trim($row[5]) == "")
    {
        return array(0 ,5);
    }

    $phone = $row[6];
    if (strlen($phone) == 10) {
        for ($i = 0; $accept && $i < 10; $i++) {
            if ($phone[$i] >= '0' && $phone[$i] <= '9') {
            } else {
                return array(0 , 6);
                break;
            }
        }
    } else {
        return array(0 , 6);
    }



    $aadhar = $row[7];
    if(strlen($aadhar) != 12)
        return array(0 , 7);

    for ($i = 0; $accept && $i < 12; $i++) {
        if ($aadhar[$i] >= '0' && $aadhar[$i] <= '9') 
        {} 
        else {
            return array(0 , 7);
            break;
        }
    }


    if ( (trim($row[8])) == " " || (trim($row[8])) == "")
        return array(0 , 8);





    $city = trim($row[9]);
    for ($i = 0; $accept && $i < strlen($city); $i++) {
        if (($city[$i] >= 'a' && $city[$i] <= 'z') || ($city[$i] >= 'A' && $city[$i] <= 'Z') || $city[$i] == " ") {
        } else {
            return array(0 , 9);
            break;
        }
    }

    $state = trim($row[10]);
    for ($i = 0; $accept && $i < strlen($state); $i++) {
        if (($state[$i] >= 'a' && $state[$i] <= 'z') || ($state[$i] >= 'A' && $state[$i] <= 'Z') || $state[$i] == " ") {
        } else {
            return array(0 , 10);
            break;
        
        }
    }


    $pin = $row[11];
    if (strlen($pin) == 6) {
        for ($i = 0; $accept && $i < 6; $i++) {
            if ($pin[$i] >= '0' && $pin[$i] <= '9') {
            } else {
                return array(0 , 11);
                break;
            }
        }
    } else {
        return array(0 , 11);
    }




    if(trim($row[12]) == " " || trim($row[12]) == "")
    {
        return array(0 ,12);
    }


    return array(1 , 0);
}



function sheetData($index,  $sheet)
{
    $check = validate_row($sheet['cells'][$index]);
    if ($check[0] == 1) 
    {
        return array(1);
    } 
    else 
    {
        return array(0, $check[1], $index);
    }
}













$excel = new PhpExcelReader;
$instituteID = "";


function readData($databaseConnectionObject, $filePath, $institutionId){

    global $excel;
    $excel->read($filePath);
    $sheet = $excel->sheets[0];
    $successfullInsertions = 0;

    if (check_headings($sheet) == 1) {

        $reason = array(
            "INSTITUTE_ID",
            "NAME",
            "EMAIL",
            "GENDER",
            "DESIGNATION",
            "CLASS",
            "PHONE",
            "AADHAR",
            "ADDRESS",
            "CITY",
            "STATE",
            "PIN",
            "PASSWORD"
        );


        $x = 2;
        $numberOfRows = $sheet['numRows'];

        global $instituteID;
        $instituteID = $sheet['cells'][2][1];

        $result = 0;
        while ($x <= $numberOfRows) {

            $result = sheetData($x, $sheet);
            if ($result[0] == 0) 
            {
                return [
                    "result"=>"Failed",
                    "message"=>"There is error in " . $reason[$result[1]] . " of row number = " . $result[2],
                    "successfullInsertions"=>0
                ];
            }
            else
            {
                $className = $sheet['cells'][$x][6];

                // Creating a Class If Not Created // 
                createClassIfNotCreated($databaseConnectionObject, $className, $institutionId);
            }
            $x++;
        }
        
        if($result[0] == 1)
        {
            // if this block is executing then it means
            // each and every row is validated successfully !
            while ($x <= $numberOfRows) 
            {
                // INSTITUTE_ID	NAME	EMAIL	GENDER	DESIGNATION	CLASS	PHONE	AADHAR	ADDRESS	CITY	STATE	PIN	PASSWORD
                $instituteName = $sheet['cells'][$x][1];
                $personName = $sheet['cells'][$x][2];
                $personEmail = $sheet['cells'][$x][3];
                $personGender = $sheet['cells'][$x][4];
                $personDesignation = $sheet['cells'][$x][5];
                $personClass = $sheet['cells'][$x][6];
                $personPhone = $sheet['cells'][$x][7];
                $personAadhar = $sheet['cells'][$x][8];
                $personAddress = $sheet['cells'][$x][9];
                $personCity = $sheet['cells'][$x][10];
                $personState = $sheet['cells'][$x][11];
                $personCityPinCode = $sheet['cells'][$x][12];
                $personPassword = $sheet['cells'][$x][13];
                $personId = $institutionId . "_" . $personAadhar;

                // Wrapping up the Data/Row in a Single Obkect //
                $userDetails = ["instituteId"=>$institutionId, "userId"=>$personId, "password"=>password_hash($personPassword, PASSWORD_BCRYPT), "email"=>$personEmail, "instituteName"=>$instituteName, "designation"=>$personDesignation, "name"=>$personName, "gender"=>$personGender, "phoneNo"=>$personPhone, "adharCardNo"=>$personAadhar, "address"=>$personAddress, "city"=>$personCity, "state"=>$personState, "pinCode"=>$personCityPinCode, "class"=>$personClass];

                // Inserting the Data in the Institute's DataBase //
                makeTeacherOrStudentRegistered($databaseConnectionObject, $userDetails);
                $successfullInsertions += 1;
                $x++;
            }
        }
        
    }
    return [
        "result"=>"Success",
        "message"=>"Persons Added Successfully !!!",
        "successfullInsertions"=>$successfullInsertions
    ];
}



?>