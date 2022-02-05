<?php


include './excel_reader.php';       

function validateFile($excel)
{
    $sheet = $excel->sheets[0];
    $firstRow = $sheet['cells'][1];

    if(
        (strtoupper($firstRow[1]) == "STUDENT ID") &&
        (strtoupper($firstRow[2]) == "STUDENT NAME") &&
        (strtoupper($firstRow[3]) == "MARKS")
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
        $row = $sheet['cells'][$x];
        while($y<3)
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


// function sheetData($index ,  $sheet) {
//         $array = array(
//             $sheet['cells'][$index][1], // this is the cell number [rowNumber][1] 
//             $sheet['cells'][$index][2], // this is the cell number [rowNumber][2] 
//             $sheet['cells'][$index][3], // this is the cell number [rowNumber][3] 
//             );


//         return $array;
// }


$excel = new PhpExcelReader;

$excel->read('./test.xls');

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

            $studentId = $row[1];
            $studentName = $row[2];
            $studentMarks = $row[3];            
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
