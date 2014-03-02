csv-parser
==========

Parse a CSV look perform an operation look for palendromes

readfile
handle header line
foreach line in file after header:
    parse line: num operator num
    switch (operator)
        perform calculation
        if (isPalindrome(number))
            print num

