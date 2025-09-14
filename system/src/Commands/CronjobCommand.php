<?php

namespace MyAAC\Commands;

use GO\Scheduler;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CronjobCommand extends Command
{
	protected function configure(): void
	{
		$this->setName('cronjob')
			->setDescription('This command runs cron tasks');
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		require SYSTEM . 'init.php';

		// Create a new scheduler
		$scheduler = new Scheduler();

		$hooks->trigger(HOOK_CRONJOB, ['scheduler' => $scheduler]);

		// Let the scheduler execute jobs which are due.
		$scheduler->run();

		return Command::SUCCESS;
	}
}
