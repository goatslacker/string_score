<?php
/*!
 * string_score.php
 * https://github.com/goatslacker/string_score
 * 2011 Josh Perez <josh@goatslacker.com>
 * MIT License: http://josh.mit-license.org
 *
 * Based on the great work by Joshaven Potter
 * http://joshaven.com/string_score
 * https://github.com/joshaven/string_score
 *
 * Copyright (C) 2009-2011 Joshaven Potter <yourtech@gmail.com>
 * Special thanks to all of the contributors listed here https://github.com/joshaven/string_score
 * MIT license: http://www.opensource.org/licenses/mit-license.php
 */

function string_score($str, $abbreviation, $fuzziness = 0) {
  if ($str === $abbreviation) {
    return 1;
  }

  if ($abbreviation === "") {
    return 0;
  }

  $abbreviation_length = strlen($abbreviation);
  $string_length = strlen($str);
  $fuzzies = 1;
  $start_of_string_bonus = 0;
  $total_character_score = 0;

  for ($i = 0; $i < $abbreviation_length; ++$i) {
    $c = substr($abbreviation, $i, 1);

    $index_c_uppercase = strpos($str, strtoupper($c));
    $index_c_lowercase = strpos($str, strtolower($c));

    $index_c_uppercase = ($index_c_uppercase === false) ? -1 : $index_c_uppercase;
    $index_c_lowercase = ($index_c_lowercase === false) ? -1 : $index_c_lowercase;

    $min_index = min($index_c_lowercase, $index_c_uppercase);
    $index_in_string = ($min_index > -1) ? $min_index : max($index_c_lowercase, $index_c_uppercase);

    if ($index_in_string === -1) {
      if ($fuzziness) {
        $fuzzies += (1 - $fuzziness);
      } else {
        return 0;
      }
    } else {
      $character_score = 0.1;
    }

    $string_index = substr($str, $index_in_string, 1);
    if ($string_index === $c) {
      $character_score += 0.1;
    }

    if ($index_in_string === 0) {
      $character_score += 0.6;

      if ($i === 0) {
        $start_of_string_bonus = 1;
      }
    } else {
      $acronym_index = substr($str, $index_in_string - 1, 1);
      if ($acronym_index === " ") {
        $character_score += 0.8;
      }
    }

    $str = substr($str, $index_in_string + 1, $string_length);

    $total_character_score += $character_score;
  }

  $abbreviation_score = $total_character_score / $abbreviation_length;

  $final_score = (($abbreviation_score * ($abbreviation_length / $string_length)) + $abbreviation_score) / 2;

  $final_score = $final_score / $fuzzies;

  if ($start_of_string_bonus && ($final_score + 0.15 < 1)) {
    $final_score += 0.15;
  }

  return $final_score;
}
