
<?php
include './excel_reader.php';
// This is for checking test paper for teachers only 
// Checking only !

function validateFile($excel)
{
    $sheet = $excel->sheets[0];
    $firstRow = $sheet['cells'][1];

    if(
        (count($firstRow) == 6) &&
        (strtoupper($firstRow[1]) == "QUESTION") &&
        (strtoupper($firstRow[2]) == "OPTION_A") &&
        (strtoupper($firstRow[3]) == "OPTION_B") &&
        (strtoupper($firstRow[4]) == "OPTION_C") &&
        (strtoupper($firstRow[5]) == "OPTION_D") &&
        (strtoupper($firstRow[6]) == "CORRECT_ANSWER")
    )
        return true;
    else
        return false;
}


function validateRows($excel)
{
    $x = 2;
    $sheet = $excel->sheets[0];
    $goOn = array("success"=>true);

    while(($x <= $sheet['numRows']) && $goOn['success']) 
    {
        $y = 1;
        // $row = trim($sheet['cells'][$x]);
        $row = $sheet['cells'][$x];
        while($y<6)
        {
            if(count($row) < 6 || $row[$y] == "" || $row[$y] == " ")
            {
                $goOn = ["success"=>false , "message"=>"There is a value error in Row = ".$x." and Column = ".chr(64+$y)." "];
                break;
            }
            $y++;
        }

        $x++;
    }
    return $goOn;
}


function testFileChecker($path)
{
    $excel = new PhpExcelReader;
    
    $excel->read($path);
    
    $result = array();
    if(validateFile($excel))
    {
        $result = validateRows($excel);
    }
    else
    {
        $result = array("success"=>false , "message" =>"This is not a correct file ! Have a look at pattern file." );
    }

    return $result;
}


// teacher test file reader ends here !





// This is for getting test paper for students only 
// Not checking while file reading ! 

function getTestRow($index ,  $sheet) {


        $array = array(
            "question" => $sheet['cells'][$index][1], 
            "option1"=>$sheet['cells'][$index][2], 
            "option2"=>$sheet['cells'][$index][3], 
            "option3"=>$sheet['cells'][$index][4], 
            "option4"=>$sheet['cells'][$index][5], 
            "answer"=>$sheet['cells'][$index][6] 
        );

        return $array;
}

function getTestPaper($path)
{
    $excel = new PhpExcelReader;
    
    $excel->read($path);
    
    $x = 2; 
    
    $sheet = $excel->sheets[0];
    
    $testPaper = array();
    
    while($x <= $sheet['numRows']) 
    {
        $question = getTestRow($x , $sheet);
        $testPaper += ["question".($x+1) => $question];
    
        $x++;
    }
    
    return $testPaper;

}


// Student test file reader ends here ! //

?>