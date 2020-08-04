<?php

defined('ACCESS') or die('No direct script access.');

$R->Route(
  array(
    "/api/articles/(:any)",
    "/api/articles/(:any)/(:any)"
  ), function ($category = "",$name="") {
  include_once EXTENSIONS.'/articles/src/controllers/index.php';
  $articles = new Articles_extension();
  $articles->api($category,$name);
});

$R->Route(array("/articles","/articles/(:num)"), function ($num = 0) {
  include_once EXTENSIONS.'/articles/src/controllers/index.php';
  $articles = new Articles_extension();
  $articles->init($num);
});

$R->Route("/articles/upload", function() {
  include_once EXTENSIONS.'/articles/src/controllers/index.php';
  $articles = new Articles_extension();
  if(App::isAdmin()) $articles->upload(); else App::die();
});

$R->Route(array("/articles/search/(:any)"), function ($name = "") {
  include_once EXTENSIONS.'/articles/src/controllers/index.php';
  $articles = new Articles_extension();
  $articles->search($name);
});

$R->Route(array("/articles/edit/(:num)"), function ($num = 0) {
  include_once EXTENSIONS.'/articles/src/controllers/index.php';
  $articles = new Articles_extension();
  if(App::isAdmin()) $articles->edit($num); else App::die();
});

$R->Route(array("/articles/del/(:num)"), function ($num = 0) {
  include_once EXTENSIONS.'/articles/src/controllers/index.php';
  $articles = new Articles_extension();
  if(App::isAdmin()) $articles->del($num); else App::die();
});

$R->Route(array("/articles/rename/(:num)"), function ($num = 0) {
  include_once EXTENSIONS.'/articles/src/controllers/index.php';
  $articles = new Articles_extension();
  if(App::isAdmin()) $articles->rename($num); else App::die();

});

$R->Route(array("/articles/new"), function () {
  include_once EXTENSIONS.'/articles/src/controllers/index.php';
  $articles = new Articles_extension();
  if(App::isAdmin()) $articles->new(); else App::die();
});

$R->Route(array("/preview/articles/(:any)"), function ($name="") {
  include_once EXTENSIONS.'/articles/src/controllers/index.php';
  $articles = new Articles_extension();
  $articles->preview($name);
});