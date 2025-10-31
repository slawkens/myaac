<?php

namespace MyAAC\Commands;

use MyAAC\Cache\Cache;
use MyAAC\Hooks;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CacheClearCommand extends Command
{
	protected function configure(): void
	{
		$this->setName('cache:clear')
			->setDescription('This command clears the cache');
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		require SYSTEM . 'init.php';

		$io = new SymfonyStyle($input, $output);

		if (!clearCache()) {
			$io->error('Unknown error on clear cache');
			return Command::FAILURE;
		}

		$cacheEngine = config('cache_engine') == 'auto' ?
			Cache::detect() : config('cache_engine');

		if (config('env') !== 'dev' && $cacheEngine == 'apcu') {
			$io->warning('APCu cache cannot be cleared in CLI. Please visit the Admin Panel and clear there.');
		}

		$io->success('Cache cleared');
		return Command::SUCCESS;
	}
}
