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
  //TODO set to user's timezone
  date_default_timezone_set('America/New_York');

  $default_look_forward = TRUE; //TODO make use of this

  // If a date is matched and it is preceded by these words, the prefix word will be removed
  // from the output string (as it's part of the date match)
  $prefix_exclude = array('at', 'by', 'on');
  $date_valword_exclude = array('a', 'i', 'eat');
  $prefix_modifiers = array('next', 'last');
  $time_units = array('year', 'years', 'month', 'months', 'forthnight', 'forthnights', 'fortnight', 'fortnights', 'week', 'weeks', 'day', 'days', 'hour', 'hours', 'minute', 'minutes', 'min', 'mins', 'second', 'seconds', 'sec', 'secs');

  // Remove spaces to create single-words that strtotime can handle (e.g., "8:30 pm" => "8:30pm")
  // This will save having to look ahead and behind any matching words in some cases
  $full_str = preg_replace('/(\d{1,2}:?\d{0,2}) ([ap]m)/i', '$1$2', $full_str);
  $full_str = str_replace('in a few', 'in 2', $full_str);
  $full_str = str_replace('in a couple of', 'in 3', $full_str);
  $full_str = str_replace('in a couple', 'in 3', $full_str);

  $parse_array = explode(" ", $full_str);
  $date_val = '';
  $remove_from_text = '';
  $text_arr = array();
  $word_count = count($parse_array);

  for ($i = 0; $i < $word_count; $i++) {
    // Finally, add the actual date field
    if (!in_array(strtolower($parse_array[$i]), $date_valword_exclude)) {
      if (strtotime($parse_array[$i]) > 0 || in_array(strtolower($parse_array[$i]), $time_units)) {
        // If date word is preceded by a modifier, add this to the date
        // Examples: "next monday", "last friday"
        if (in_array(strtolower($parse_array[$i-1]), $prefix_modifiers)) {
          $remove_from_text = "{$parse_array[$i-1]} {$parse_array[$i]}";
          $date_val = "{$parse_array[$i-1]} {$parse_array[$i]}";
        }

        // If date word is preceded by "in n" where n is a number, add this to the date
        // This is similar to the time units check below, but will catch additional words
        // Examples: "in 2 mondays", "in 3 fridays"
        if (is_numeric($parse_array[$i-1])) {
          if (strtolower($parse_array[$i-2]) == 'in') {
            $remove_from_text = "{$parse_array[$i-2]} {$parse_array[$i-1]} {$parse_array[$i]}";
            $date_val = "{$parse_array[$i-1]} {$parse_array[$i]}";
          }
        }

        // If a "should be removed" prefix is detected, set it to be removed
        // from the final text string and exclude it from the date array
        if (in_array(strtolower($parse_array[$i-1]), $prefix_exclude)) {
          if ($remove_from_text != '') {
            $remove_from_text = "{$parse_array[$i-1]} {$remove_from_text}";
          }
          else {
            $remove_from_text = "{$parse_array[$i-1]} {$parse_array[$i]}";
          }
          $date_val = ($date_val != '') ? $date_val : $parse_array[$i];
        }

        // If we haven't set any text to be removed, we haven't had to match
        // any modifiers, so just process the value as is
        if ($remove_from_text == '') {
          $remove_from_text = $parse_array[$i];
          $date_val = $parse_array[$i];
        }
      }
    }
  }

  // Build the results and return them
  if ($date_val != '') {
    $data['timestamp'] = strtotime($date_val);

    $data['text_str'] = $full_str;
    if ($remove_from_text != '') {
      $data['text_str'] = str_replace($remove_from_text, '', $data['text_str']);
      $data['text_str'] = rtrim(ucfirst($data['text_str']));
    }
  }
  else {
    // There weren't any dates, so return the string as it was passed
    $data['text_str'] = $full_str;
  }

  return $data;
}
