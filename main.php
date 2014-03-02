<?php

require './functions.php';

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


