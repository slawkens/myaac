<?php

namespace MyAAC\Commands;

use MyAAC\Models\Settings as SettingsModel;
use MyAAC\Plugins;
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
				'Setting key in format name.key'
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

		// format settings_name.key
		// example: core.template
		$explode = explode('.', $key);

		// find by plugin name
		foreach (Plugins::getAllPluginsSettings() as $_key => $setting) {
			if ($setting['pluginFilename'] === $explode[0]) {
				$explode[0] = $_key;
				$key = implode('.', $explode);
			}
		}

		$settings = Settings::getInstance();
		$settings->clearCache();
		$settings->load();

		$setting = $settings[$key];
		if (!isset($setting['value'])) {
			$io->warning('Settings with this key does not exists');
			return Command::FAILURE;
		}

		$settings->updateInDatabase($explode[0], $explode[1], $value);
		$settings->clearCache();

		$io->success("Setting $key successfully updated");
		return Command::SUCCESS;
	}
}
