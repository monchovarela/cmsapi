<?php

defined('ACCESS') or die('No direct script access.');

$R->Route("/demo", function () {
  include_once EXTENSIONS.'/demo/src/controllers/index.php';
  $Demo = new Demo_extension();
  $Demo->init();
});
