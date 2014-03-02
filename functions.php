<?php

/* ========================= FUNCTIONS ===================== */

function isValidOperator($operator){
    return $operator == '+' ||
        $operator == '-' ||
        $operator == '/' ||
        $operator == '*';

}

/* 
 * parse a valid operator out of an input string
 * return false or operator that was found
 * could wind up with odd results with malformed input
 *  e.g. 1234234-232*23423 -- two operators!
*/
function parseOperator($str){
    $operator = false;
    if (strpos($str, '+') !== false){
        $operator = '+';
    } elseif (strpos($str, '-') !== false){
        $operator = '-';
    } elseif (strpos($str, '*') !== false){
        $operator = '*';
    } elseif (strpos($str, '/') !== false){
        $operator = '*';
    }

    return $operator;
}

/*
 * basic function to validate that the passed in array can be used for calculations
 * conditions: 
 *  a) there are 2 operands
 *  b) they are both numeric
 * returns false or an array of whitespace free 2 operands
 */
function validateOperands($data){
    if (count($data) != 2){
        return false;
    }
    
    try {
        $operand1 = trim($data[0]);
        $operand2 = trim($data[1]);
    } catch (Exception $e){
        //$str = implode($data);
        //echo "invalid line: $str\n";
        return false;
    }
    if (is_numeric($operand1) && is_numeric($operand2)){
        return array($operand1, $operand2);
    } 

    return false;
}


function exceptions_error_handler($severity, $message, $filename, $lineno) {
  if (error_reporting() == 0) {
    return;
  }
  if (error_reporting() & $severity) {
    throw new ErrorException($message, 0, $severity, $filename, $lineno);
  }
}
