<?php

namespace MyAAC\Commands;

use MyAAC\Models\Config;
use POT;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class MigrateToCommand extends Command
{
	use Env;

	protected function configure(): void
	{
		$this->setName('migrate:to')
			->setDescription('This command migrates to specific version of database')
			->addArgument('version',
				InputArgument::REQUIRED,
				'Version number'
			);
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$io = new SymfonyStyle($input, $output);

		$versionDest = $input->getArgument('version');

		if (!$versionDest || $versionDest > DATABASE_VERSION || $versionDest < 1) {
			$io->error('Please enter a valid version number');
			return Command::FAILURE;
		}

		$this->init();

		$currentVersion = Config::where('name', 'database_version')->first()->value;
		if ($currentVersion > $versionDest) {
			// downgrade
			for ($i = $currentVersion; $i > $versionDest; $i--) {
				echo $i . ' ';
				$this->executeMigration($i, false);
			}
		}
		else if ($currentVersion < $versionDest) {
			// upgrade
			for ($i = $currentVersion + 1; $i <= $versionDest; $i++) {
				echo $i . ' ';
				$this->executeMigration($i, true);
			}
		}
		else {
			$io->success('Nothing to be done');
			return Command::SUCCESS;
		}

		$upgrade = ($currentVersion < $versionDest ? 'Upgrade' : 'Downgrade');
		$io->success("Migration ({$upgrade}) to version {$versionDest} has been completed");

		return Command::SUCCESS;
	}

	private function executeMigration($id, $_up): void
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

		updateDatabaseConfig('database_version', ($_up ? $id : $id - 1));
	}
}
