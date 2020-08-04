<?php

/**
 * Class Template
 *
 * @author    Moncho Varela / Nakome <nakome@gmail.com>
 * @copyright 2016 Moncho Varela / Nakome <nakome@gmail.com>
 *
 * @version 0.0.1
 *
 */
class Template
{
  /**
   * Constructor
   */
  public function __construct()
  {
    // tags
    $this->tags = array(
      // date
      '{date}' => '<?php echo date("d-m-Y");?>',

      // year
      '{Year}' => '<?php echo date("Y");?>',

      // site url
      '{Site_url}' => '<?php echo Url::base();?>',
      '{Site_current}' => '<?php echo Url::current();?>',

      // comment
      //{* comment *}
      '{\*(.*?)\*}' => '<?php echo "\n";?>',

      // confitional
      '{If: ([^}]*)}' => '<?php if ($1): ?>',
      '{Else}' => '<?php else: ?>',
      '{Elseif: ([^}]*)}' => '<?php elseif ($1): ?>',
      '{\/If}' => '<?php endif; ?>',

      // {loop: $array} {/loop}
      '{Loop: ([^}]*) as ([^}]*)=>([^}]*)}' => '<?php $counter = 0; foreach (%%$1 as $2=>$3): ?>',
      '{Loop: ([^}]*) as ([^}]*)}' => '<?php $counter = 0; foreach (%%$1 as $key => $2): ?>',
      '{Loop: ([^}]*)}' => '<?php $counter = 0; foreach (%%$1 as $key => $value): ?>',
      '{\/Loop}' => '<?php $counter++; endforeach; ?>',

      // vars
      // {?= 'hello world' ?}
      '{\?(\=){0,1}([^}]*)\?}' => '<?php if(strlen("$1")) echo $2; else $2; ?>',

      // {? 'hello world' ?}
      '{(\$[a-zA-Z\-\._\[\]\'"0-9]+)}' => '<?php echo %%$1; ?>',

      // encode & decode
      '{(\$[a-zA-Z\-\._\[\]\'"0-9]+)\|encode}' => '<?php echo base64_encode(%%$1); ?>',
      '{(\$[a-zA-Z\-\._\[\]\'"0-9]+)\|decode}' => '<?php echo base64_decode(%%$1); ?>',

      // capitalize
      '{(\$[a-zA-Z\-\._\[\]\'"0-9]+)\|capitalize}' => '<?php echo ucfirst(%%$1); ?>',

      // lowercase
      '{(\$[a-zA-Z\-\._\[\]\'"0-9]+)\|lower}' => '<?php echo strtolower(%%$1); ?>',

      // short
      '{(\$[a-zA-Z\-\._\[\]\'"0-9]+)\|short}' => '<?php echo Text::short(%%$1,30); ?>',

      // {$page.content|e}
      '{(\$[a-zA-Z\-\._\[\]\'"0-9]+)\|e}' => '<?php echo htmlspecialchars(%%$1, ENT_QUOTES | ENT_HTML5, "UTF-8"); ?>',
      
      // {$page.content|parse}
      '{(\$[a-zA-Z\-\._\[\]\'"0-9]+)\|parse}' => '<?php echo html_entity_decode(%%$1, ENT_QUOTES); ?>',
      
      // md5
      '{(\$[a-zA-Z\-\._\[\]\'"0-9]+)\|md5}' => '<?php echo md5(%%$1); ?>',

      // sha1
      '{(\$[a-zA-Z\-\._\[\]\'"0-9]+)\|sha1}' => '<?php echo sha1(%%$1); ?>',

      // include
      '{Include: (.+?\.[a-z]{2,4})}' => '<?php include_once(ROOT."/$1"); ?>',

      // Partial
      '{Partial: (.+?\.[a-z]{2,4})}' => '<?php include_once(ROOT."/core/views/$1"); ?>',

      // Action
      '{Action: ([a-zA-Z\-\._\[\]\'"0-9]+)}' => '<?php Action::run(\'$1\'); ?>',

      // segments
      '{Segment: ([^}]*)}' => '<?php if (Url::segments(0) == "$1"): ?>',
      '{\/Segment}' => '<?php endif; ?>',

      // Assets
      '{Assets: (.+?\.[a-z]{2,4})}' => '<?php echo Url::base()."/public/assets/$1" ?>',

    );
    $this->tmp =  ROOT.'/tmp/';
    if (!file_exists($this->tmp)) {
      mkdir($this->tmp);
    }
  }

  /**
   * Callback
   *
   * @param mixed $variable the var
   *
   * @return mixed
   */
  public function callback($variable)
  {
      if (!is_string($variable) && is_callable($variable)) {
          return $variable();
      }
      return $variable;
  }
  /**
   *  Set var
   *
   * @param string $name  the key
   * @param string $value the value
   *
   * @return mixed
   */
  public function set($name, $value)
  {
      $this->data[$name] = $value;
      return $this;
  }
  /**
   * Append data in array
   *
   * @param string $name  the key
   * @param string $value the value
   *
   * @return null
   */
  public function append($name, $value)
  {
      $this->data[$name][] = $value;
  }
  /**
   * Parse content
   *
   * @param string $content the content
   *
   * @return string
   */
  private function _parse($content)
  {
      // replace tags with PHP
      foreach ($this->tags as $regexp => $replace) {
          if (strpos($replace, 'self') !== false) {
              $content = preg_replace_callback('#'.$regexp.'#s', $replace, $content);
          } else {
              $content = preg_replace('#'.$regexp.'#', $replace, $content);
          }
      }
      // replace variables
      if (preg_match_all('/(\$(?:[a-zA-Z0-9_-]+)(?:\.(?:(?:[a-zA-Z0-9_-][^\s]+)))*)/', $content, $matches)) {
          for ($i = 0; $i < count($matches[1]); $i++) {
              // $a.b to $a["b"]
              $rep = $this->_replaceVariable($matches[1][$i]);
              $content = str_replace($matches[0][$i], $rep, $content);
          }
      }
      // remove spaces betweend %% and $
      $content = preg_replace('/\%\%\s+/', '%%', $content);
      // call cv() for signed variables
      if (preg_match_all('/\%\%(.)([a-zA-Z0-9_-]+)/', $content, $matches)) {
          for ($i = 0; $i < count($matches[2]); $i++) {
              if ($matches[1][$i] == '$') {
                  $content = str_replace($matches[0][$i], 'self::callback($'.$matches[2][$i].')', $content);
              } else {
                  $content = str_replace($matches[0][$i], $matches[1][$i].$matches[2][$i], $content);
              }
          }
      }
      return $content;
  }
  /**
   * Run file
   *
   * @param string $file    the file
   * @param int    $counter the counter
   *
   * @return string
   */
  private function _run($file, $counter = 0)
  {
      $pathInfo = pathinfo($file);
      $tmpFile = $this->tmp.$pathInfo['basename'];
      if (!is_file($file)) {
          echo "Template '$file' not found.";
      } else {
          $content = file_get_contents($file);
          if ($this->_searchTags($content) && ($counter < 3)) {
              file_put_contents($tmpFile, $content);
              $content = $this->_run($tmpFile, ++$counter);
          }
          file_put_contents($tmpFile, $this->_parse($content));
          extract($this->data, EXTR_SKIP);
          ob_start();
          include $tmpFile;
          if(!DEV_MODE) unlink($tmpFile);
          return ob_get_clean();
      }
  }
  /**
   * Draw file
   *
   * @param string $file the file
   *
   * @return string
   */
  public function draw($file)
  {
      $result = $this->_run($file);
      return $result;
  }
  /**
   *  Comment
   *
   * @param string $content the content
   *
   * @return null
   */
  public function comment($content)
  {
      return null;
  }
  /**
   *  Search Tags
   *
   * @param string $content the content
   *
   * @return boolean
   */
  private function _searchTags($content)
  {
      foreach ($this->tags as $regexp  => $replace) {
          if(preg_match('#'.$regexp.'#sU', $content, $matches))
              return true;
      }
      return false;
  }
  /**
   * Dot notation
   *
   * @param string $var the var
   *
   * @return string
   */
  private function _replaceVariable($var)
  {
      if (strpos($var, '.') === false) {
          return $var;
      }
      return preg_replace('/\.([a-zA-Z\-_0-9]*(?![a-zA-Z\-_0-9]*(\'|\")))/', "['$1']", $var);
  }
}

