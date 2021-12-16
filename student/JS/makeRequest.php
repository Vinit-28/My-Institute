

<?php


if( isset($_POST['request']) ){


    include './excel_reader.php';
    
        function sheetData($index ,  $sheet) {
            $json = array(
                'question' => $sheet['cells'][$index][1],
                'option1' => $sheet['cells'][$index][2],
                'option2' => $sheet['cells'][$index][3],
                'option3' => $sheet['cells'][$index][4],
                'option4' => $sheet['cells'][$index][5],
                'correct_answer' => $sheet['cells'][$index][6]
            );
            return $json;
        }
    
        $allQuestions = array();

        $excel = new PhpExcelReader;

        $excel->read('./test.xls');

        $x = 1;

        $sheet = $excel->sheets[0];
        $question = array();
        $index=1;
        while($x <= $sheet['numRows']) 
        {
            array_push($question , sheetData($x , $sheet));
            $x++;
        }

        echo json_encode($question);
}




?>