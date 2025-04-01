<?php
require_once '../common.php';
require_once SYSTEM . 'functions.php';

const MYAAC_ADMIN = true;

$admin = new \MyAAC\App\Admin();
$admin->run();
