csv-parser
==========

Parse a CSV, perform an operation, and look for palendromes

readfile
handle header line
foreach line in file after header:
    parse line: num operator num
    switch (operator)
        perform calculation
        if (isPalindrome(number))
            print num

notes:
lies :) data is malformed -- therefore can't use parsecsv 

