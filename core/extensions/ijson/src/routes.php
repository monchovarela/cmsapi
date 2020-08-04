<?php

defined('ACCESS') or die('No direct script access.');

$R->Route(array("/api/ijson/(:any)"), function ($name="") {
  include_once EXTENSIONS.'/ijson/src/controllers/index.php';
  $Ijson = new Ijson_extension();
  $Ijson->api($name);
});

$R->Route(array("/ijson/search/(:any)"), function ($name = "") {
  include_once EXTENSIONS.'/ijson/src/controllers/index.php';
  $Ijson = new Ijson_extension();
  $Ijson->search($name);
});

$R->Route(array("/ijson/edit/(:num)"), function ($num = 0) {
  include_once EXTENSIONS.'/ijson/src/controllers/index.php';
  $Ijson = new Ijson_extension();
  if(App::isAdmin()) $Ijson->edit($num); else App::die();
});

$R->Route(array("/ijson/del/(:num)"), function ($num = 0) {
  include_once EXTENSIONS.'/ijson/src/controllers/index.php';
  $Ijson = new Ijson_extension();
  if(App::isAdmin()) $Ijson->del($num); else App::die();
});

$R->Route(array("/ijson/rename/(:num)"), function ($num = 0) {
  include_once EXTENSIONS.'/ijson/src/controllers/index.php';
  $Ijson = new Ijson_extension();
  if(App::isAdmin()) $Ijson->rename($num); else App::die();
});

$R->Route(array("/ijson/new"), function () {
  include_once EXTENSIONS.'/ijson/src/controllers/index.php';
  $Ijson = new Ijson_extension();
  if(App::isAdmin()) $Ijson->new(); else App::die();
});

$R->Route(array("/ijson","/ijson/(:num)"), function ($num = 0) {
  include_once EXTENSIONS.'/ijson/src/controllers/index.php';
  $Ijson = new Ijson_extension();
  $Ijson->init($num);
});

