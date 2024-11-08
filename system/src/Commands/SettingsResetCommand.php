<?php

namespace MyAAC\Commands;

use MyAAC\Models\Settings as SettingsModel;
use MyAAC\Settings;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class SettingsResetCommand extends Command
{
	protected function configure(): void
	{
		$this->setName('settings:reset')
			->setDescription('Removes all settings in database');
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		require SYSTEM . 'init.php';

		$io = new SymfonyStyle($input, $output);

		if (!$io->confirm('Are you sure you want to reset all settings in database?', false)) {
			return Command::FAILURE;
		}

		SettingsModel::truncate();

		$settings = Settings::getInstance();
		$settings->clearCache();

		$io->success('Setting cleared successfully');
		return Command::SUCCESS;
	}
}
