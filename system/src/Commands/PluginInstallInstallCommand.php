<?php

namespace MyAAC\Commands;

use MyAAC\Plugins;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class PluginInstallInstallCommand extends Command
{
	protected function configure(): void
	{
		$this->setName('plugin:install:install')
			->setDescription('This command executes the "install" part of the plugin')
			->addArgument('plugin', InputArgument::REQUIRED, 'Plugin name');
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		require SYSTEM . 'init.php';

		$io = new SymfonyStyle($input, $output);

		$pluginName = $input->getArgument('plugin');
		if(!Plugins::executeInstall($pluginName)) {
			$io->error(Plugins::getError());
			return 2;
		}

		foreach(Plugins::getWarnings() as $warning) {
			$io->warning($warning);
		}

		$info = Plugins::getPluginJson($pluginName);
		$io->success('Script for install ' . (isset($info['name']) ? $info['name'] . ' p' : 'P') . 'lugin has been successfully executed.');
		return Command::SUCCESS;
	}
}
