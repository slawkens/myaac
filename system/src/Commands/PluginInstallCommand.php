<?php

namespace MyAAC\Commands;

use MyAAC\Plugins;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class PluginInstallCommand extends Command
{
	protected function configure(): void
	{
		$this->setName('plugin:install')
			->setDescription('This command installs plugin')
			->addArgument('pathToPluginZip', InputArgument::REQUIRED, 'Path to zip file (plugin) that you want to install');
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		require SYSTEM . 'init.php';

		$io = new SymfonyStyle($input, $output);

		$pathToFile = $input->getArgument('pathToPluginZip');

		$ext = strtolower(pathinfo($pathToFile, PATHINFO_EXTENSION));
		if($ext !== 'zip') {// check if it is zipped/compressed file
			$io->error('Please install only .zip files');
			return 2;
		}

		if(!file_exists($pathToFile)) {
			$io->error('File ' . $pathToFile . ' does not exist');
			return 3;
		}

		if(!Plugins::install($pathToFile)){
			$io->error(Plugins::getError());
			return 4;
		}

		foreach(Plugins::getWarnings() as $warning) {
			$io->warning($warning);
		}

		$info = Plugins::getPluginJson();
		$io->success((isset($info['name']) ? $info['name'] . ' p' : 'P') . 'lugin has been successfully installed.');
		return Command::SUCCESS;
	}
}
