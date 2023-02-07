<?php

require_once __DIR__ . '/../../common.php';
require_once SYSTEM . 'functions.php';
require_once SYSTEM . 'init.php';
require_once SYSTEM . 'hooks.php';

$hooks = new Hooks();
$hooks->load();

use GO\Scheduler;

// Create a new scheduler
$scheduler = new Scheduler();

$hooks->trigger(HOOK_CRONJOB, ['scheduler' => $scheduler]);

// Let the scheduler execute jobs which are due.
$scheduler->run();
