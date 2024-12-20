<?php

namespace MyAAC\Commands;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CacheClearCommand extends Command
{
	protected function configure(): void
	{
		$this->setName('cache:clear')
			->setDescription('This command clears the cache');
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		require SYSTEM . 'init.php';

		$io = new SymfonyStyle($input, $output);

		if (!clearCache()) {
			$io->error('Unknown error on clear cache');
			return Command::FAILURE;
		}

		$io->success('Cache cleared');
		return Command::SUCCESS;
	}
}
