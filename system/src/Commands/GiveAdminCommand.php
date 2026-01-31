<?php

namespace MyAAC\Commands;

use MyAAC\Plugins;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class GiveAdminCommand extends Command
{
	protected function configure(): void
	{
		$this->setName('give:admin')
			->setDescription('This command adds super admin privileges to selected user')
			->addArgument('account', InputArgument::REQUIRED, 'Account E-Mail, name or id');
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		require SYSTEM . 'init.php';

		$io = new SymfonyStyle($input, $output);

		$account = new \OTS_Account();

		$accountParam = $input->getArgument('account');
		if (str_contains($accountParam, '@')) {
			$account->findByEMail($accountParam);
		}
		else {
			if (USE_ACCOUNT_NAME || USE_ACCOUNT_NUMBER) {
				$account->find($accountParam);
			}
			else {
				$account->load($accountParam);
			}
		}

		if (!$account->isLoaded()) {
			$io->error('Cannot find account mit supplied parameter: ' . $accountParam);
			return self::FAILURE;
		}

		$account->setCustomField('web_flags', 3);
		$io->success('Successfully added admin privileges to ' . $accountParam . ' (E-Mail: ' . $account->getEMail() . ')');
		return self::SUCCESS;
	}
}
