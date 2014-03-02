<?php

// set error handler so we can catch 'undefined offset' notices 
// and handle them as exceptions
set_error_handler('exceptions_error_handler');

// look for -d option as a flag (no value needed)
// if its passed set debug to true
$options = getopt("d");
define('DEBUG' , isset($options['d']) ? true: false);

$filename = 'data.txt';

// supress the error we're gonna die below
$handle = @fopen($filename, "r");
if ($handle === false){
    die ("ERROR: could not open input file {$filename}");
}

$header = false;
$output = array();
$notqualified = array();
$invalidOperators = array();
$invalidOperands = array();

// whitespace is incosistent so can't use fgetcsv
while (($line = fgets($handle)) !== FALSE) {
    // process header row
    if ($header === false){
        $header = $line;
        // header is alread in format "id: {number}" no just save it
        // process next line
        continue;
    }

    // make sure it has a proper operator
    if (($operator = parseOperator($line)) !== false){
        // split the line on the operator
        $data = explode($operator, $line);
    
        if (($operands = validateOperands($data)) !== false){
            //echo "{$operands[0]} {$operator} {$operands[1]}\n";

            // perform calculation
            switch ($operator){
                case '+':
                    $result = $operands[0] + $operands[1];
                    break;
                case '-':
                    $result = $operands[0] - $operands[1];
                    break;
                case '*':
                    $result = $operands[0] * $operands[1];
                    break;
                case '/':
                    $result = $operands[0] / $operands[1];
                    break;
            }

            // if it is a palendrome (same forward / backward)
            // then add it to the output array
            $reversed = strrev($result);
            if ($reversed == $result){
                $output[] = $result;
            } else {
                $notqualified[] = $result;
            }
        } else {
            // something wrong with one of the operands
            $invalidOperands[] = $line;
        }
    } else {
        // invalid / non existand operator
        $invalidOperators[] = $line;
    }

}
fclose($handle);

//  output 
echo $header;
echo implode("\n", $output);

if (DEBUG){
    echo "\n/* ========================= NON-Palindrome results ===================== */\n";
    echo implode("\n", $notqualified);
    echo "\n/* ========================= invalid operators ===================== */\n";
    echo implode("\n", $invalidOperators);
    echo "\n/* ========================= invalid operands ===================== */\n";
    echo implode("\n", $invalidOperands);
}

// thats all folks
exit;


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
