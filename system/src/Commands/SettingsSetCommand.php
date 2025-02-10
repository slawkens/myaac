<?php

namespace MyAAC\Commands;

use MyAAC\Models\Settings as SettingsModel;
use MyAAC\Settings;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class SettingsSetCommand extends Command
{
	protected function configure(): void
	{
		$this->setName('settings:set')
			->setDescription('Updates the setting specified by argument in database')
			->addArgument('key',
				InputArgument::REQUIRED,
				'Setting name/key'
			)
			->addArgument('value',
				InputArgument::REQUIRED,
				'New value'
			);
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		require SYSTEM . 'init.php';

		$io = new SymfonyStyle($input, $output);

		$key = $input->getArgument('key');
		$value = $input->getArgument('value');

		$settings = Settings::getInstance();
		$settings->clearCache();
		$settings->load();

		$setting = $settings[$key];
		if (!isset($setting['value'])) {
			$io->warning('Settings with this key does not exists');
			return Command::FAILURE;
		}

		// format plugin_name.key
		// example: core.template
		$explode = explode('.', $key);

		$settings->updateInDatabase($explode[0], $explode[1], $value);
		$settings->clearCache();

		$io->success("Setting $key successfully updated");
		return Command::SUCCESS;
	}
}
