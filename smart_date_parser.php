<?php
/**
 * @author Adam Courtemanche
 *
 * Idea and original code from
 * @see http://goo.gl/n1yZg
 *
 * @see http://www.php.net/manual/en/datetime.formats.relative.php
 */
function smart_date_parse($full_str) {
  $date_valword_exclude = array('a', 'i', 'eat');
  $time_units = array('year', 'years', 'month', 'months', 'forthnight', 'forthnights', 'fortnight', 'fortnights', 'week', 'weeks', 'day', 'days', 'hour', 'hours', 'minute', 'minutes', 'min', 'mins', 'second', 'seconds', 'sec', 'secs');
  $months = 'january|february|march|april|may|june|july|august|september|october|november|december';
  $months .= '|jan|feb|mar|apr|may|jun|jul|aug|sep|oct|nov|dec';
  $days = 'yesterday|tomorrow|monday|mon|tuesday|tue|wednesday|wed|thursday|thur|friday|fri|saturday|sat|sunday|sun';

  // These replacements will save having to look ahead and behind any matching words in some cases
  $full_str = str_replace('in a few', 'in 2', $full_str);
  $full_str = preg_replace('/in a couple( of)?/i', 'in 3', $full_str);
  // Remove spaces to create single-words that strtotime can handle (e.g., "8:30 pm" => "8:30pm")
  $full_str = preg_replace('/(\d{1,2}:?\d{0,2}) ([ap]m)/i', '$1$2', $full_str);

  $parse_array = explode(' ', $full_str);
  $word_count = count($parse_array);
  $words = array();
  for ($i = 0; $i < $word_count; $i++) {
    if ((strtotime($parse_array[$i]) > 0 || in_array(strtolower($parse_array[$i]), $time_units)) && !in_array(strtolower($parse_array[$i]), $date_valword_exclude)) {
      $words[] = '!t_' . $parse_array[$i];
    }
    else {
      $words[] = $parse_array[$i];
    }
  }

  // Build the token string that we can work with
  $token_str = implode(' ', $words);

  // Convert "3 am" to "3am" and "3:30 pm" to "3:30pm"
  $token_str = preg_replace('/(\d{1,2}:?\d{0,2}) ([ap]m)/i', '$1$2', $token_str);

  // Handle on month + day
  // e.g., "on !t_July 5" to "!t_July !t_5"
  $token_str = preg_replace('/on !t_(' . $months . ') ([0-9A-Z]*)/i', '!t_$1 !t_$2', $token_str);

  // Remove prefixes "Eat food at !t_3pm" to "Eat food !t_3pm"
  $token_str = preg_replace('/(at|by|on|in) !t_/i', '!t_', $token_str);

  // Remove "in" and make number a flagged string
  // e.g., "in 10 !t_days" to "!t_10 !t_days"
  $token_str = preg_replace('/in (\d*) !t_/i', '!t_$1 !t_', $token_str);

  // Make modifier prefix a flagged string
  // e.g., "next !t_week" to "!t_next !t_week"
  $token_str = preg_replace('/(next|last|previous|this) !t_/i', '!t_$1 !t_', $token_str);

  // Handle "X after X" (where X's are strtotime matches)
  // e.g., "!t_day after !t_tomorrow" to "!t_tomorrow !t_+1 !t_day"
  $token_str = preg_replace('/(!t_[0-9A-Z]*) after (!t_[0-9A-Z]*)/i', '$2 !t_+1 $1', $token_str);

  // Handle "X before X" (where X's are strtotime matches)
  // e.g., "!t_day before !t_tomorrow" to "!t_tomorrow !t_-1 !t_day"
  $token_str = preg_replace('/(!t_[0-9A-Z]*) before (!t_[0-9A-Z]*)/i', '$2 !t_-1 $1', $token_str);
  
  // Handle "July 5 5pm" (this is not strtotime-ready; it must be "5pm July 5")
  // e.g., "!t_July $!t_5 !t_10am" to "!t_10am !t_July !t_5"
  $token_str = preg_replace('/!t_(' . $months . ') (!t_[0-9A-Z]*) (!t_[0-9A-Z:]*)/i', '$3 !t_$1 $2', $token_str);

  // Handle "5pm tomorrow" and "5pm next Monday" (these are not strtotime-ready; they must be "next Monday 5pm")
  // If this has "next|last|previous|this" then shift that word to the beginning
  // e.g., "$!t_5pm !t_tomorrow" to "!t_tomorrow !t_5pm"
  // e.g., "$!t_5pm !t_next !t_Monday" to "!t_next !t_Monday !t_5pm"
  $token_str = preg_replace('/(!t_\d{1,2}:?\d{0,2}[ap]m) ((!t_next|last|previous|this) )?!t_(' . $days . ')/i', '$3 !t_$4 $1', $token_str);

  // Using arrays instead of appending to strings in case we need to do more with the values
  $datetime_arr = array();
  $text_arr = array();

  // Build the two arrays (separating date from text)
  foreach (explode(' ', $token_str) as $word) {
    if (strpos($word, '!t_') === 0) {
      $datetime_arr[] = substr($word, 3);
    }
    else {
      $text_arr[] = $word;
    }
  }

  // Finalize the output that we will return
  $data['text_str'] = rtrim(ucfirst(implode(' ', $text_arr)));
  if ($datetime_arr[0]) {
    $data['datetime_str'] = implode(' ', $datetime_arr);
    $data['timestamp'] = strtotime($data['datetime_str']);
    if ($data['timestamp'] <= 0) {
      $data['timestamp'] = FALSE; // Date/time values present, but couldn't get valid timestamp
      $data['error'] = TRUE; // Date/time values present, but couldn't get valid timestamp
    }
  }
  else {
    $data['datetime_str'] = '';
    $data['timestamp'] = 0;
  }

  $data['token_str'] = $token_str; // TODO TEMPORARY
  $data['result_date'] = date('r', $data['timestamp']); // TODO TEMPORARY

  return $data;
}
