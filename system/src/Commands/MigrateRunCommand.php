<?php

namespace MyAAC\Commands;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class MigrateRunCommand extends Command
{
	protected function configure(): void
	{
		$this->setName('migrate:run')
			->setDescription('This command runs the migration specified by id')
			->addArgument('id',
				InputArgument::IS_ARRAY | InputArgument::REQUIRED,
				'Id or ids of migration(s)'
			)
			->addOption('down', 'd', InputOption::VALUE_NONE, 'Down');;
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		require SYSTEM . 'init.php';

		$io = new SymfonyStyle($input, $output);

		$ids = $input->getArgument('id');

		// pre-check
		// in case one of the migrations doesn't exist - we won't execute any of them
		foreach ($ids as $id) {
			if (!$this->migrationExists($id)) {
				$io->error([
					"One of the migrations specified doesnt exist: $id",
					"Please check it and re-run the command",
					"No migration has been executed",
				]);

				return Command::FAILURE;
			}
		}

		$down = $input->getOption('down') ?? false;

		foreach ($ids as $id) {
			$this->executeMigration($id, $io, !$down);
		}

		return Command::SUCCESS;
	}

	private function migrationExists($id): bool {
		return file_exists(SYSTEM . 'migrations/' . $id . '.php');
	}

	private function executeMigration($id, $io, $_up = true): void
	{
		$db = app()->get('database');

		$db->revalidateCache();

		require SYSTEM . 'migrations/' . $id . '.php';
		if ($_up) {
			if (isset($up)) {
				$up();
			}
		}
		else {
			if (isset($down)) {
				$down();
			}
		}

		$io->success('Migration ' . $id . ' successfully executed' . ($_up ? '' : ' (downgrade)'));
	}
}
