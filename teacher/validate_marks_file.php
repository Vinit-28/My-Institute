<?php

include './excel_reader.php';     


$headings = array(
    "INSTITUTE_ID",
    "STUDENT_ID",	
    "STUDENT_NAME",	
    "MARKS_SCORED",	
);


function check_headings($sheet)
{

    global $headings;

    $row_number = 1;
    $accept = 1;
    while ($row_number <= 4) {
        if (strtoupper($sheet['cells'][1][$row_number]) == $headings[$row_number - 1]) {
        } else {
            $accept = 0;
            break;
        }
        $row_number++;
    }
    return $accept;
}


function checkInt($number)
{
        
    for ($i = 0; $i < strlen($number); $i++) 
    {    
        if ($number[$i] >= '0' && $number[$i] <= '9') {} 
        else 
        {
            return 0;
        }
    }
    return 1;    
}



function validate_row( $row )
{
    global $instituteID;

    if($row[1] != $instituteID)
    {
        return array(0 , 0);
    }

    if(trim($row[2]) == "")
    {
        return array(0 , 1);
    }

    if(trim($row[3]) == "")
    {
        return array(0 , 2);
    }

    if(trim($row[4]) == "")
    {
        return array(0 , 3);
    }
    
    if(trim($row[5]) == "")
    {
        return array(0 , 4);
    }
    if(trim($row[6]) == "")
    {
        return array(0 , 5);
    }
    
    if(trim($row[7]) == "" || checkInt($row[7]) == 0)
    {
        return array(0 , 6);
    }
    
    if(trim($row[8]) == "" || checkInt($row[8]) == 0)
    {
        return array(0 , 7);
    }

    return array(1 , 0);

}



function validate_student_marks_file($file_path)
{

    $excel = new PhpExcelReader;
    $excel->read($file_path);

     

    $sheet = $excel->sheets[0];
    
    if(check_headings($sheet))
    {
        $reason = array(
            "INSTITUTE_ID",
            "STUDENT_ID",	
            "STUDENT_NAME",	
            "MARKS_SCORED"
        );

        $row_number = 2;
        $numberOfRows = $sheet['numRows']-2;
        
        global $instituteID;
        if(trim($sheet['cells'][2][1]) != "")
        {
            $instituteID = $sheet['cells'][2][1];
        }
        else
        {
            // because the first column of Institute is is empty
            return array(0);
        }
        $result = 0;


        while ($row_number <= $numberOfRows) {
            $result  = validate_row($sheet['cells'][$row_number]);
            print_r($result);
            echo "<br>";
            if ($result[0] == 1) 
            {
                // return array(1);
            } 
            else 
            {
                return array(0, $result[1], $row_number);
            }

            $row_number++;
        }
        return array(1);
    }
    else
    {
        echo "Headings are not correct, please look into Sample File !";
    }


}


function main_validate_student_marks_file()
{
    $headings = $GLOBALS["headings"];
    $result = validate_student_marks_file('./MARKS.xls');
    if($result[0] == 1)
    {
        echo "Success";
    }
    else
    {
        echo "error in = ". $headings[$result[1]]." row number = ".$result[2];
    }

}


//main_validate_student_marks_file();
