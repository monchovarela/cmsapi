<?php

defined('ACCESS') or die('No direct script access.');

$R->Route(array("/ext/toggle/(:any)"), function ($name = "") {
  include_once EXTENSIONS.'/login/src/controllers/index.php';
  $login = new Login_extension();
  if(App::isAdmin()) $login->toggleExtension($name); else App::die();
});

$R->Route(array("/users/edit/(:num)"), function ($num = 0) {
  include_once EXTENSIONS.'/login/src/controllers/index.php';
  $login = new Login_extension();
  if(App::isAdmin()) $login->userEdit($num); else App::die();
});

$R->Route(array("/users/del/(:num)"), function ($num = 0) {
  include_once EXTENSIONS.'/login/src/controllers/index.php';
  $login = new Login_extension();
  if(App::isAdmin()) $login->userDel($num); else App::die();
});

$R->Route(array("/users/add"), function () {
  include_once EXTENSIONS.'/login/src/controllers/index.php';
  $login = new Login_extension();
  if(App::isAdmin()) $login->userAdd(); else App::die();
});

$R->Route(array("/users"), function () {
  include_once EXTENSIONS.'/login/src/controllers/index.php';
  $login = new Login_extension();
  if(App::isAdmin()) $login->users(); else App::die();
});

$R->Route(array("/settings"), function () {
  include_once EXTENSIONS.'/login/src/controllers/index.php';
  $login = new Login_extension();
  if(App::isAdmin()) $login->settings(); else App::die();
});

$R->Route(array("/logout"), function () {
  include_once EXTENSIONS.'/login/src/controllers/index.php';
  $login = new Login_extension();
  $login->logout();
});
  
$R->Route("/", function () {
  include_once EXTENSIONS.'/login/src/controllers/index.php';
  $login = new Login_extension();
  $login->init();
});