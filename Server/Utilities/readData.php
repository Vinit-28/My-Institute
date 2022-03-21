<?php

require_once('./excel_reader.php');

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

$all_Aadhar = array();

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



function checkEmail($str) {
    $email = trim($str);
    if((!preg_match("/^([a-z0-9\+\-]+)(\.[a-z0-9\+\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $email)) == 1)
        return 0;
    else
        return 1;
}

function checkPhone($phone) {
    if (strlen($phone) == 10) {
        for ($i = 0; $i < 10; $i++) {
            if ($phone[$i] >= '0' && $phone[$i] <= '9') {
            } else {
                return 0;
            }
        }
    } else {
        return 0;
    }
    return 1;
}


function checkAadhar($aadhar)
{
    global $all_Aadhar;
    
    if( !isset($all_Aadhar[$aadhar]) )
    {
        if(strlen($aadhar) != 12)
            return 0;
    
        for ($i = 0; $i < 12; $i++) {
            if ($aadhar[$i] >= '0' && $aadhar[$i] <= '9') {} 
            else {
                return 0;
            }
        }
        $all_Aadhar[$aadhar] = 1;
        return 1;
    }
    else
    {
        return 0;
    }
    
}

function check_name_or_city($name)
{
    if(strlen($name)<=0)
        return 0;
    for ($i = 0; $i < strlen($name); $i++) {

        if (($name[$i] >= 'a' && $name[$i] <= 'z') || ($name[$i] >= 'A' && $name[$i] <= 'Z') || ($name[$i] == " ")) {} 
        else return 0;

    }
    return 1;
}


function checkPin($pin)
{
    if (strlen($pin) != 6) 
        return 0;
    
    for ($i = 0; $i < 6; $i++) {
        if ($pin[$i] >= '0' && $pin[$i] <= '9') {
        } else {
            return 0;
        }
    }
    return 1;

}






function validate_row($array)
{

    global $instituteID;

    
    $row = array();
    foreach ($array as $key => $v) 
    {
        array_push($row, $v);
    }



        // Checking name of student !
        $name = $row[1];
        if(check_name_or_city($name) == 0)
        {
            return array(0 , 1);
        }
        
        
        
        // Checking email !
        $email = $row[2];
        if(checkEmail($email) == 0){
            return array(0,2);
        }
    
        
        
        // checking Gender of student
        $gender = strtoupper($row[3]);
        if ($gender == "M" || $gender == "F" || $gender == "O") {
        } else {
            return array(0 , 3);
        }
    
        
        
        // checking designation of student
        $designation = strtoupper($row[4]);
        if ($designation == "STUDENT" || $designation == "TEACHER") {
        } else {
            return array(0 , 4);
        }
        
    
        // Checking Class of student !
        if(trim($row[5]) == " " || trim($row[5]) == "")
        {
            return array(0 ,5);
        }
    
        
        // Checking Phone number
        $phone = $row[6];
        if(checkPhone($phone) == 0)
        {
            return array(0 , 6);
        }
        
    
    
        // Checking Aadhar card number !
        $aadhar = $row[7];
        if(checkAadhar($aadhar) == 0)
        {
            return array(0 , 7);
        }
        
    
        // Checking Address !
        if ( (trim($row[8])) == " " || (trim($row[8])) == "")
            return array(0 , 8);
    
    
    
    
        // Checking city of student
        $city = $row[9];
        if(check_name_or_city($city) == 0)
        {
            return array(0 , 9);
        }
    
    
        // Checking State of student !
        $state = $row[10];
        if(check_name_or_city($state) == 0)
        {
            return array(0 , 10);
        }
    
    
        // Checking pin of student
        $pin = $row[11];
        if(checkPin($pin)==0)
        {
            return array(0 , 11);
        }
    
    
    
        // Password should not be empty !
        if(trim($row[12]) == " " || trim($row[12]) == "")
        {
            return array(0 ,12);
        }
    
    return array(1 , 0);
}



function sheetData($index,  $sheet)
{
    if( !isset($sheet['cells'][$index]) ){
        return array(1, -1, "File End");
    } 

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
                if($result[1] == 7)
                {
                    return [
                        "result"=>"Failed",
                        "message"=>"Duplicate or Invalid " . $reason[$result[1]] . " of row number = " . $result[2],
                        "successfullInsertions"=>0
                    ];
                }
                else
                {
                    return [
                        "result"=>"Failed",
                        "message"=>"There is error in " . $reason[$result[1]] . " of row number = " . $result[2],
                        "successfullInsertions"=>0
                    ];
                }
            }
            else
            {
                
                // If the file has ended //
                if( isset($result[1]) && isset($result[2]) && $result[1] == -1 && $result[2] == "File End" ){
                    $numberOfRows = $x-1; // Reseting the total number of rows // 
                    break;
                }

                $className = $sheet['cells'][$x][6];

                // Creating a Class If Not Created // 
                createClassIfNotCreated($databaseConnectionObject, $className, $institutionId);
            }
            $x++;
        }
    
        if($result[0] == 1)
        {
            $x = 2;
            // if this block is executing then it means
            // each and every row is validated successfully !
            while ($x <= $numberOfRows) {
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