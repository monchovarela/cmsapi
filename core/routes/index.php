<?php

defined('ACCESS') or die('No direct script access.');



// new panel
$R = new Router();

// start session
Session::start();

// create dir if not exists
if (!is_dir(EXTENSIONS))
{
  @mkdir(EXTENSIONS);
}

// http://stackoverflow.com/questions/14680121/require-just-files-in-scandir-array
$extensions = array_filter(scandir(EXTENSIONS),function ($item) {
  return $item[0] !== '.';
});

// loop extensions
foreach ($extensions as $ext)
{
  // find json file on extensions
  $jsonFile = EXTENSIONS.'/'.$ext.'/config.json';
  // if exists json file load plugin
  if (file_exists($jsonFile))
  {
    // get contents of file
    $extensionsFile = file_get_contents($jsonFile);
  }
  // convert to json
  $json = json_decode($extensionsFile, true);
  // loop to read json
  if (!is_array($json)) continue;
  
  // loop json file
  foreach ($json as $obj)
  {
    $name = $obj['filename'];
    $enabled = $obj['enabled'];
    $routesFile = EXTENSIONS.'/'.$name.'/src/routes.php';
    if ($enabled)
    {
        if (file_exists($routesFile))
        {
            require $routesFile;
        }
    }
  }
}

$R->launch();



