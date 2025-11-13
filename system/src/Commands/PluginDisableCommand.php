<?php

namespace MyAAC\Commands;

use MyAAC\Plugins;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class PluginDisableCommand extends Command
{
	protected function configure(): void
	{
		$this->setName('plugin:disable')
			->setAliases(['plugin:deactivate'])
			->setDescription('This command disables plugin')
			->addArgument('plugin-name', InputArgument::REQUIRED, 'Plugin that you want to disable');
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		require SYSTEM . 'init.php';

		$io = new SymfonyStyle($input, $output);

		$pluginName = $input->getArgument('plugin-name');

		if (!Plugins::disable($pluginName)) {
			$io->error('Error while disabling plugin ' . $pluginName . ': ' . Plugins::getError());
			return 2;
		}

		$io->success('Successfully disabled plugin ' . $pluginName);
		return Command::SUCCESS;
	}
}
