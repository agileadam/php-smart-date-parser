<?php
/**
 * Open this file in your browser to see
 * the results as each entry is parsed
 */

date_default_timezone_set('America/New_York');
require_once('smart_date_parser.php');

$test_strings = array(
  'Eat food friday after next', //TODO
  'In 2 days, eat some food', //TODO
  'Eat food at 15:00',
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
  'Eat food day after tomorrow',
  'Eat food day before yesterday',
  'Eat food on July 5',
  'Eat food on July 5 at 10pm',
  'Eat food on July 5 at 10pm with Suzy',
  'Eat food at 10pm on July 5',
  'Eat food at 9am tomorrow',
  'Eat food at 9am Tuesday',
  'Eat food at 9am next Tuesday',
);
?>

<html>
<head>
<style type="text/css">
th { text-align: left; padding-left: 30px; }
td { text-align: left; padding-left: 30px; }
</style>
</head>
<body>
<?php print 'Current date/time: ' . date('r') . "\r\n\r\n"; ?>
<table>
  <tr>
  <th style="padding-left: 0px;">orig_str</th>
  <th>token_str</th>
  <th>datetime_str</th>
  <th>timestamp</th>
  <th>result_date</th>
  </tr>
  <?php
  foreach ($test_strings as $str) {
    $date_and_text = smart_date_parse($str);
    print '<tr>';
    print '<td style="padding-left: 0px;">' . $str . '</td>';
    print '<td>' . $date_and_text['token_str'] . '</td>';
    print '<td>' . $date_and_text['datetime_str'] . '</td>';
    print '<td>' . $date_and_text['timestamp'] . '</td>';
    $result_date = ($date_and_text['timestamp']) ? date('r', $date_and_text['timestamp']) : 'No time detected';
    print '<td>' . $result_date . '</td>';
    print '</tr>';
  }
  ?>
</table>
</body>
</html>
