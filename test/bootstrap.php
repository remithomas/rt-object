<?php
error_reporting(-1);
chdir(__DIR__.'/..');
$loader = require 'vendor/autoload.php';
$loader->add('Remithomas\RtObject\Test', __DIR__);