<?php
// range is numbers (48) through capital and lower case letters (122)
$random_string = "";
$random_string_length = 5;

for ($i = 0; $i < $random_string_length; $i++) {
	$selectorRand = round( mt_rand( 3 , 1000 ) );
	$selector = $selectorRand % 3;
	
	if($selector == 0) {
		$ascii_no = round( mt_rand( 65 , 90 ) ); // generates a number within the range
		// finds the character represented by $ascii_no and adds it to the random string
		// study **chr** function for a better understanding
		$random_string .= chr( $ascii_no );
	} elseif ($selector == 1) {
		$ascii_no = round( mt_rand( 97 , 122 ) );
		$random_string .= chr( $ascii_no );
	} elseif ($selector == 2) {
		$ascii_no = round( mt_rand( 48 , 57 ) );
		$random_string .= chr( $ascii_no );
	}
	$random_string .= ' ';
}

echo trim($random_string);