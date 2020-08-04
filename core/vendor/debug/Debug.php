<?php

defined("ACCESS") or die("Script access inactive");

/**
 * Class Debug
 * 
 * @author      Moncho Varela / Nakome <nakome@gmail.com>
 * @copyright   2016 Moncho Varela / Nakome <nakome@gmail.com>
 *
 * @version     0.0.1
 */
class Debug
{

  /**
   * Log textfile
   * 
   * @param string $txt
   */
  public static function log($txt)
  {
    $json = array(
      'user' => Session::get('api_name'),
      'title' => $txt,
      'hour' => date("H::m::s"),
      'date' => date("d/m/Y")
    );

    $file = ROOT."/log.txt";

    if(file_exists($file)){
      $import = json_decode(file_get_contents($file),true);
      array_push($import, $json);
      file_put_contents($file, json_encode($import));
    }else{
      $import = array();
      array_push($import, $json);
      file_put_contents($file, json_encode($import));
    }
  }
  /**
   * Debug string
   * 
   * @param string $title
   * @param strint $arr
   */
  public static function string($title="",$str)
  {
    if(is_string($str)){
      $style = "border-radius:5px;margin:1em;padding:1em;background:#4CAF50;color:#FFEB3B";
      $str = htmlentities($str);
      return '<div style="'.$style.'">
        <b>'.$title.'</b>
        <pre style="color:#fff">'.print_r($str,1).'</pre>
      </div>';
    }else{
      self::log("Error string not exists");
      $style = "border-radius:5px;margin:1em;padding:1em;background:#F44336;color:white";
      return '<div style="'.$style.'">
        <b>'.$title.'</b>
        <pre  style="color:#FFEB3B">Error string not exists</pre>
      </div>';
    }
  }
  /**
   * Debug string
   * 
   * @param string $title
   * @param strint $arr
   */
  public static function array($title="",$arr)
  {
    if(is_array($arr)){
      $style = "border-radius:5px;margin:1em;padding:1em;background:#009688;color:#FFEB3B;";
      return '<div style="'.$style.'">
        <b>'.$title.'</b>
        <pre style="color:#fff">'.print_r($arr,1).'</pre>
      </div>';
    }else{
      self::log("Error array not exists");
      $style = "border-radius:5px;margin:1em;padding:1em;background:#F44336;color:white;";
      return '<div style="'.$style.'">
        <b>'.$title.'</b>
        <pre  style="color:#FFEB3B">Error array not exists</pre>
      </div>';
    }
  }

  /**
   * Debug number
   * 
   * @param string $title
   * @param int $number
   */
  public static function int($title="",$num)
  {
    if(is_int($num)){
      $style = "border-radius:5px;margin:1em;padding:1em;background:#9C27B0;color:#FFEB3B;";
      return '<div style="'.$style.'">
        <b>'.$title.'</b>
        <pre style="color:#fff">'.print_r($num,1).'</pre>
      </div>';
    }else{
      self::log("Error int not exists");
      $style = "border-radius:5px;margin:1em;padding:1em;background:#F44336;color:white;";
      return '<div style="'.$style.'">
          <b>'.$title.'</b>
          <pre  style="color:#FFEB3B">Error int not exists</pre>
      </div>';
    }
  }
}
