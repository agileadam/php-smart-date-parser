<?php
date_default_timezone_set('America/New_York');
require_once('smart_date_parser.php');

$test_strings = array(
	'Eat food at 3pm',
	'Eat food at 3pm and then drink beer',
	'Eat food at 3 pm',
	'Eat food by 5:30pm',
	'Eat food by 5:30 pm',
	'Eat food in 3 hours',
	'Eat food tomorrow',
	'Eat food in 1 day',
	'Eat food in 2 days',
	'Eat food in a couple days',
	'Eat food in a couple of days',
	'Eat food in a few days',
	'Eat food in 4 weeks',
	'Eat food on Tuesday',
	'Eat food on Tue',
	'Eat food next Sunday',
	'Eat food in 2 Sundays',
	'Eat food last week',
	'Eat food next week',
	'Eat food quickly',
	'Eat food tomorrow at 9am',
	'Eat food Friday at 9am',
	'Eat food at 9am tomorrow',
	'Eat food day after tomorrow',
	'Eat food day before yesterday',
	'Eat food on July 5',
	'In 2 days, eat some food', //TODO
	'Eat food friday after next', //TODO
	'Eat food on July 5 at 10pm', //TODO
);

print 'Current date/time: ' . date('r') . "\r\n\r\n";

foreach ($test_strings as $str) {
	$date_and_text = smart_date_parse($str);
	$result_date = ($date_and_text['timestamp']) ? date('r', $date_and_text['timestamp']) : 'No time detected';
	print_r($date_and_text);
}

?>
