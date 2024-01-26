<?php

namespace MyAAC\Commands;

use MyAAC\Hooks;
use Symfony\Component\Console\Command\Command as SymfonyCommand;

class Command extends SymfonyCommand
{
	protected Hooks $hooks;

	public function __construct() {
		parent::__construct();

		$this->hooks = new Hooks();
		$this->hooks->load();
	}
}
