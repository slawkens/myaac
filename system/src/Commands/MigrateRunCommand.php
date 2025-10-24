<?php

namespace MyAAC\Commands;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class MigrateRunCommand extends Command
{
	use Env;

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
		$io = new SymfonyStyle($input, $output);

		$ids = $input->getArgument('id');

		$this->init();

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

		/**
		 * Sort according to $down option.
		 * Do we really want it?
		 * Or should we use order provided by user,
		 *      even when it's not sorted correctly?
		 * Leaving it for consideration.
		 */
		/*
		if ($down) {
			rsort($ids);
		}
		else {
			sort($ids);
		}
		*/

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
		global $db;

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
