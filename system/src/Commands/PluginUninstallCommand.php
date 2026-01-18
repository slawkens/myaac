<?php

namespace MyAAC\Commands;

use MyAAC\Plugins;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class PluginUninstallCommand extends Command
{
	protected function configure(): void
	{
		$this->setName('plugin:uninstall')
			->setAliases(['plugin:remove', 'plugin:delete'])
			->setDescription('This command uninstalls plugin')
			->addArgument('plugin-name', InputArgument::REQUIRED, 'Plugin that you want to uninstall');
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		require SYSTEM . 'init.php';

		$io = new SymfonyStyle($input, $output);

		$pluginName = $input->getArgument('plugin-name');

		if (!Plugins::uninstall($pluginName)) {
			$io->error('Error while uninstalling plugin ' . $pluginName . ': ' . Plugins::getError());
			return 2;
		}

		foreach(Plugins::getWarnings() as $warning) {
			$io->warning($warning);
		}

		$io->success('Successfully uninstalled plugin ' . $pluginName);
		return Command::SUCCESS;
	}
}
