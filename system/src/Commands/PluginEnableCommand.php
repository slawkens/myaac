<?php

namespace MyAAC\Commands;

use MyAAC\Plugins;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class PluginEnableCommand extends Command
{
	protected function configure(): void
	{
		$this->setName('plugin:enable')
			->setAliases(['plugin:activate'])
			->setDescription('This command enables plugin')
			->addArgument('plugin-name', InputArgument::REQUIRED, 'Plugin that you want to enable');
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		require SYSTEM . 'init.php';

		$io = new SymfonyStyle($input, $output);

		$pluginName = $input->getArgument('plugin-name');

		if (!Plugins::enable($pluginName)) {
			$io->error('Error while enabling plugin ' . $pluginName . ': ' . Plugins::getError());
			return 2;
		}

		$io->success('Successfully enabled plugin ' . $pluginName);
		return Command::SUCCESS;
	}
}
