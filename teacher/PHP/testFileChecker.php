<?php


include './excel_reader.php';       

function validateFile($excel)
{
    $sheet = $excel->sheets[0];
    $firstRow = $sheet['cells'][1];

    if(
        (strtoupper($firstRow[1]) == "QUESTION") &&
        (strtoupper($firstRow[2]) == "OPTION_A") &&
        (strtoupper($firstRow[3]) == "OPTION_B") &&
        (strtoupper($firstRow[4]) == "OPTION_C") &&
        (strtoupper($firstRow[5]) == "OPTION_D") &&
        (strtoupper($firstRow[6]) == "CORRECT_OPTION")
    )
        return true;
    else
        return false;
}


function validateRows($excel)
{

    $x = 2;

    $sheet = $excel->sheets[0];
    $goOn = true;
    while(($x <= $sheet['numRows']) && $goOn) 
    {
        $y = 1;
        $row = trim($sheet['cells'][$x]);
        while($y<6)
        {
            if($row[$y] == "" || $row[$y] == " ")
            {
                $goOn = false;
                echo "There is a value error in Row = ".$x." and Column = ".chr(64+$y)." ";
                break;
            }
            $y++;
        }

        $x++;
    }
    return $goOn;

}



$excel = new PhpExcelReader;

$excel->read('./testFileSample.xls');

if(validateFile($excel))
{
    if(validateRows($excel))
    {
        $x = 2;

        $sheet = $excel->sheets[0];
        $goOn = true;
        while(($x <= $sheet['numRows']) && $goOn) 
        {
            $row = $sheet['cells'][$x];

            $questionNumber = $x-1;
            $question = $row[1];
            $option1 = $row[2];
            $option2 = $row[3];
            $option3 = $row[4];
            $option4 = $row[5];
            $correctOption = $row[6];

            $x++;
        }
    }
    else{}
}
else
{
    echo "This is not a correct File !!";
}


?>
