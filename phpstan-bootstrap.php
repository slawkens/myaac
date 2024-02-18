<?php

require __DIR__ . '/system/libs/pot/OTS.php';
$ots = POT::getInstance();

require __DIR__ . '/system/libs/pot/InvitesDriver.php';
require __DIR__ . '/system/libs/rfc6238.php';
require __DIR__ . '/common.php';

const ACTION = '';
const PAGE = '';
const URI = '';
define('SELF_NAME', basename(__FILE__));
