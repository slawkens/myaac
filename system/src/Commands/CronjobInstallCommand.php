<?php

namespace MyAAC\Commands;

use GO\Scheduler;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CronjobInstallCommand extends Command
{
	protected function configure(): void
	{
		$this->setName('cronjob:install')
			->setDescription('This command automatically registers into your crontab');
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		require SYSTEM . 'init.php';

		$io = new SymfonyStyle($input, $output);

		if (MYAAC_OS !== 'LINUX') {
			$io->error('This script can be run only on linux.');
			return 2;
		}

		$job = '* * * * * /usr/bin/php ' . BASE . SELF_NAME . ' cronjob >> ' . SYSTEM . 'logs/cron.log 2>&1';

		if ($this->cronjobExists($job)) {
			$io->info('MyAAC cronjob already installed.');
			return Command::FAILURE;
		}

		exec('crontab -l', $content);

		$content = implode(' ', $content);
		$content .= PHP_EOL . $job;

		file_put_contents(CACHE . 'cronjob', $content . PHP_EOL);
		exec('crontab ' . CACHE. 'cronjob');

		$io->success('Installed crontab successfully.');
		return Command::SUCCESS;
	}

	private function cronjobExists($command): bool
	{
		exec('crontab -l', $crontab);

		if(is_array($crontab)) {
			$crontab = array_flip($crontab);

			if(isset($crontab[$command])){
				return true;
			}
		}

		return false;
	}
}
