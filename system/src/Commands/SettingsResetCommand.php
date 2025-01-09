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
			->setDescription('Removes settings in database')
			->addArgument('name',
				InputArgument::OPTIONAL,
				'Name of the plugin'
			);
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		require SYSTEM . 'init.php';

		$io = new SymfonyStyle($input, $output);

		$name = $input->getArgument('name');

		$all = !$name ? 'all' : $name;
		if (!$io->confirm("Are you sure you want to reset {$all} settings in database?", false)) {
			return Command::FAILURE;
		}

		if (!$name) {
			SettingsModel::truncate();
		}
		else {
			$settingsModel = SettingsModel::where('name', $name)->first();
			if (!$settingsModel) {
				$io->warning('No settings for this plugin saved in database');
				return Command::SUCCESS;
			}

			SettingsModel::where('name', $name)->delete();
		}

		$settings = Settings::getInstance();
		$settings->clearCache();

		$io->success('Settings cleared successfully');
		return Command::SUCCESS;
	}
}
