<?php

defined("ACCESS") or die("Script access inactive");


/**
 * Class Text
 * 
 * @author      Moncho Varela / Nakome <nakome@gmail.com>
 * @copyright   2016 Moncho Varela / Nakome <nakome@gmail.com>
 *
 * @version     0.0.1
 */
class Text
{


  /**
   * Capitalize
   * 
   * @param string $str
   */
  public static function capitalize(string $str)
  {
    return ucfirst($str);
  }

  /**
   * Lowercase
   * 
   * @param string $str
   */
  public static function lowercase(string $str)
  {
    return strtolower($str);
  }

  /**
   * Uppercase
   * 
   * @param string $str
   */
  public static function uppercase(string $str)
  {
    return strtoupper($str);
  }

  /**
   * Short text
   * 
   * @param string $text 
   * @param number $chars_limit
   */
  public static function short(string $text, int $chars_limit)
  {
    // Check if length is larger than the character limit
    if (strlen($text) > $chars_limit)
    {
        // If so, cut the string at the character limit
        $new_text = substr($text, 0, $chars_limit);
        // Trim off white space
        $new_text = trim($new_text);
        // Add at end of text ...
        return $new_text . "...";
    }else{
        return $text;
    }
  }

  /**
   * Convert text to slug
   * 
   * @param string $str
   */
  public static function slug($str)
  {
    $slug = preg_replace('/[^A-Za-z0-9-]+/', '-', $str);  
    return $slug;  
  }
}