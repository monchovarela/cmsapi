<?php

defined('ACCESS') or die('No direct script access.');

$R->Route(array("/api/gallery/(:any)"), function ($name="") {
  include_once EXTENSIONS.'/gallery/src/controllers/index.php';
  $gallery = new Gallery_extension();
  $gallery->api($name);
});

$R->Route("/gallery/upload/(:any)", function ($name="") {
  include_once EXTENSIONS.'/gallery/src/controllers/simpleImage.php';
  include_once EXTENSIONS.'/gallery/src/controllers/index.php';
  $gallery = new Gallery_extension();
  $gallery->uploadImage($name);
});

$R->Route("/gallery/deleteimage/(:any)", function ($name="") {
  include_once EXTENSIONS.'/gallery/src/controllers/index.php';
  $gallery = new Gallery_extension();
  $file = base64_decode($name);
  $gallery->deleteImage($file);
});

$R->Route("/gallery/p/(:any)", function ($name="") {
  include_once EXTENSIONS.'/gallery/src/controllers/index.php';
  $gallery = new Gallery_extension();
  $gallery->preview($name);
});

$R->Route("/gallery/update/(:any)", function ($name="") {
  include_once EXTENSIONS.'/gallery/src/controllers/index.php';
  $gallery = new Gallery_extension();
  $gallery->update($name);
});

$R->Route("/gallery/delete/(:any)/(:num)", function ($name="",$num=0) {
  include_once EXTENSIONS.'/gallery/src/controllers/index.php';
  $gallery = new Gallery_extension();
  $gallery->delete($name,$num);
});

$R->Route("/gallery/create", function () {
  include_once EXTENSIONS.'/gallery/src/controllers/index.php';
  $gallery = new Gallery_extension();
  $gallery->newGallery();
});

$R->Route(array("/gallery","/gallery/(:num)"), function ($num = 0) {
  include_once EXTENSIONS.'/gallery/src/controllers/index.php';
  $gallery = new Gallery_extension();
  $gallery->init($num);
});
  
