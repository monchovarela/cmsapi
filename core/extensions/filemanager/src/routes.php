<?php

defined('ACCESS') or die('No direct script access.');


// api url
$R->Route("/api/filemanager/(:any)", function ($name) {
  include_once EXTENSIONS.'/filemanager/src/controllers/index.php';
  $filemanager = new Filemanager_extension();
  $filemanager->api($name);
});

// delete file
$R->Route("/filemanager/delete/(:any)", function ($name="") {
  include_once EXTENSIONS.'/filemanager/src/controllers/index.php';
  $filemanager = new Filemanager_extension();
  $filemanager->deleteFile($name);
});

// rename file
$R->Route("/filemanager/rename", function ($old="",$new="") {
  include_once EXTENSIONS.'/filemanager/src/controllers/index.php';
  $filemanager = new Filemanager_extension();
  $filemanager->renameFile($old,$new);
});

// upload file
$R->Route("/filemanager/upload", function () {
  include_once EXTENSIONS.'/filemanager/src/controllers/index.php';
  $filemanager = new Filemanager_extension();
  $filemanager->uploadFile();
});
// root url
$R->Route(array("/filemanager","/filemanager/(:num)"), function ($num = 0) {
  include_once EXTENSIONS.'/filemanager/src/controllers/index.php';
  $filemanager = new Filemanager_extension();
  $filemanager->init($num);
});
  
