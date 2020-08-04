<?php

defined('ACCESS') or die('No direct script access.');

$R->Route(array("/api/pages/(:any)","/api/pages/(:any)/(:any)"), function ($category = "",$name="") {
  include_once EXTENSIONS.'/pages/src/controllers/index.php';
  $pages = new Pages_extension();
  $pages->api($category,$name);
});

$R->Route(array("/pages","/pages/(:num)"), function ($num = 0) {
  include_once EXTENSIONS.'/pages/src/controllers/index.php';
  $pages = new Pages_extension();
  $pages->init($num);
});
  
$R->Route(array("/pages/search/(:any)"), function ($name = "") {
  include_once EXTENSIONS.'/pages/src/controllers/index.php';
  $pages = new Pages_extension();
  $pages->search($name);
});

$R->Route(array("/pages/edit/(:num)"), function ($num = 0) {
  include_once EXTENSIONS.'/pages/src/controllers/index.php';
  $pages = new Pages_extension();
  if(App::isAdmin()) $pages->edit($num); else App::die();
});

$R->Route(array("/pages/del/(:num)"), function ($num = 0) {
  include_once EXTENSIONS.'/pages/src/controllers/index.php';
  $pages = new Pages_extension();
  if(App::isAdmin()) $pages->del($num); else App::die();
});

$R->Route(array("/pages/rename/(:num)"), function ($num = 0) {
  include_once EXTENSIONS.'/pages/src/controllers/index.php';
  $pages = new Pages_extension();
  if(App::isAdmin()) $pages->rename($num); else App::die();

});

$R->Route(array("/pages/new"), function () {
  include_once EXTENSIONS.'/pages/src/controllers/index.php';
  $pages = new Pages_extension();
  if(App::isAdmin()) $pages->new(); else App::die();
});

$R->Route(array("/preview/pages/(:any)"), function ($name="") {
  include_once EXTENSIONS.'/pages/src/controllers/index.php';
  $pages = new Pages_extension();
  $pages->preview($name);
});

