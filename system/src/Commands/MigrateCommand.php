<?php

namespace MyAAC\Commands;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class MigrateCommand extends Command
{
	use Env;

	protected function configure(): void
	{
		$this->setName('migrate')
			->setDescription('This command updates the AAC to latest migration');
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$this->init();

		$io = new SymfonyStyle($input, $output);

		$tmp = '';
		if(fetchDatabaseConfig('database_version', $tmp)) { // we got version
			$tmp = (int)$tmp;
			if ($tmp >= DATABASE_VERSION) {
				$io->success('Already on latest version.');
				return Command::SUCCESS;
			}
		}

		require SYSTEM . 'migrate.php';

		$io->success('Migrated to latest version (' . DATABASE_VERSION . ')');
		return Command::SUCCESS;
	}
}
