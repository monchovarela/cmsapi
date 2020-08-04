<?php

defined("ACCESS") or die("Script access inactive");

/**
 * Class Message
 * 
 * @author      Moncho Varela / Nakome <nakome@gmail.com>
 * @copyright   2016 Moncho Varela / Nakome <nakome@gmail.com>
 *
 * @version     0.0.1
 */
class Message
{
  /**
   * Get message.
   */
  public static function get()
  {
    //Top of file
    if (Session::get('msg')) {
        $msg = Session::get('msg');
        Session::delete('msg');
    }
    if (isset($msg)) {
        echo '<script type="text/javascript">message("'.$msg.'");</script>';
    }
  }

  /**
   * Set message.
   *
   * @param array $msg   The message
   */
  public static function set($msg)
  {
    Session::set('msg', $msg);
  }
}