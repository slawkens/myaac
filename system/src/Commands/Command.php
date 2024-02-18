<?php

namespace MyAAC\Commands;

use MyAAC\Hooks;
use Symfony\Component\Console\Command\Command as SymfonyCommand;

class Command extends SymfonyCommand
{
	public function __construct() {
		parent::__construct();
	}
}
