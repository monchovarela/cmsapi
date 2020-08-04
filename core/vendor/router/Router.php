<?php

defined("ACCESS") or die("Script access inactive");

/**
 * Router.
 *
 * @author Moncho Varela / Nakome <nakome@gmail.com>
 *
 * @link http://monchovarela.es
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class Router
{

  private $routes = array();

  /**
   * The Name of Panel.
   *
   * @var string
   */
  public $appName = 'Generator';

  /**
   * The version of Panel.
   *
   * @var string
   */
  public $version = '1.0.0';

  /**
   * Get routes.
   *
   *  <code>
   *       $m = new Router();
   *       $m->Route($patterns, $callback)
   *  </code>
   *
   * @param  patterns $patterns  links
   * @param  callback  $callback function
   */
  public function Route($patterns, $callback)
  {
      if (!is_array($patterns)) {
          $patterns = array($patterns);
      }
      foreach ($patterns as $pattern) {
          $pattern = trim($pattern, '/');
          $pattern = str_replace(
        array('\(', '\)', '\|', '\:any', '\:num', '\:all', '#'),
        array('(', ')', '|', '[^/]+', '\d+', '.*?', '\#'),
        preg_quote($pattern, '/'));
          $this->routes['#^'.$pattern.'$#'] = $callback;
      }
  }

  /**
   * Execute routes routes.
   *
   *  <code>
   *       $m->lauch()
   *  </code>
   */
  public function launch()
  {
      $url = $_SERVER['REQUEST_URI'];
      $base = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
      if (strpos($url, $base) === 0) {
          $url = substr($url, strlen($base));
      }
      $url = trim($url, '/');
      foreach ($this->routes as $pattern => $callback) {
          if (preg_match($pattern, $url, $params)) {
              array_shift($params);

              return call_user_func_array($callback, array_values($params));
          }
      }
        // Page not found
        if ($this->is404(Url::base())) {
            $this->set404();
        }
        // end flush
        ob_end_flush();
        exit;
  }

  /**
   * 404 html file
   *
   * @return include or die
   */
  public static function set404()
  {
      App::die();
  }

  /**
   * Determines if 404.
   *
   * @param string $url the url
   *
   * @return bool  True if 404, False otherwise
   */
  public function is404($url)
  {
      $handle = curl_init($url);
      curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
      /* Get the HTML or whatever is linked in $url. */
      $response = curl_exec($handle);
      /* Check for 404 (file not found). */
      $httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
      curl_close($handle);
      /* If the document has loaded successfully without any redirection or error */
      if ($httpCode >= 200 && $httpCode < 300) {
          return false;
      } else {
          return true;
      }
  }
}
