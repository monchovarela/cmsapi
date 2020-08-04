<?php

defined('ACCESS') or die('No direct script access.');

/**
 * @author      Moncho Varela / Nakome <nakome@gmail.com>
 * @copyright   2016 Moncho Varela / Nakome <nakome@gmail.com>
 *
 * @version     0.0.1
 */
class Demo_extension
{
  /**
   * Init 
   * 
   * @return array
   */
  public function init()
  {
    if(App::isLogged())
    {
      echo App::page(array(
        'title' => 'Demo',
        'content' => App::partial(EXTENSIONS.'/demo/src/templates/index.html')
      ));
    } else App::die();
  }
}