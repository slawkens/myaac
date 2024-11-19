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

		$this->initEnv();

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

	private function initEnv()
	{
		global $config;
		if (!isset($config['installed']) || !$config['installed']) {
			throw new \RuntimeException('MyAAC has not been installed yet or there was error during installation. Please install again.');
		}

		if(empty($config['server_path'])) {
			throw new \RuntimeException('Server Path has been not set. Go to config.php and set it.');
		}

		// take care of trailing slash at the end
		if($config['server_path'][strlen($config['server_path']) - 1] !== '/')
			$config['server_path'] .= '/';

		$config['lua'] = load_config_lua($config['server_path'] . 'config.lua');

		// POT
		require_once SYSTEM . 'libs/pot/OTS.php';
		$ots = POT::getInstance();
		$eloquentConnection = null;

		require_once SYSTEM . 'database.php';
	}
}
