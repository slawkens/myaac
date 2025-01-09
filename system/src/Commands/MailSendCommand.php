<?php

namespace MyAAC\Commands;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class MailSendCommand extends Command
{
	protected function configure(): void
	{
		$this->setName('mail:send')
			->setDescription('This command sends E-Mail to single user. Message can be provided as follows: ' . PHP_EOL
				. '  echo "Hello World" | php sa email:send --subject="This is the subject" test@test.com')
			->addArgument('recipient', InputArgument::REQUIRED, 'Email, Account Name, Account id or Player Name')
			->addOption('subject', 's', InputOption::VALUE_REQUIRED, 'Subject');
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		require SYSTEM . 'init.php';

		$io = new SymfonyStyle($input, $output);

		if (!setting('core.mail_enabled')) {
			$io->error('Mailing is not enabled on this server');
			return Command::FAILURE;
		}

		$email_account_name = $input->getArgument('recipient');
		$subject = $input->getOption('subject');
		if (!$subject) {
			$io->error('Please specify subject via -s or --subject="" option');
			return 2;
		}

		$message = file_get_contents('php://stdin');

		if(!str_contains($email_account_name, '@')) {
			$account = new \OTS_Account();
			if(USE_ACCOUNT_NAME) {
				$account->find($email_account_name);
			}
			else {
				$account->load($email_account_name);
			}

			if($account->isLoaded()) {
				$email_account_name = $account->getEMail();
			}
			else {
				$player = new \OTS_Player();
				$player->find($email_account_name);
				if($player->isLoaded()) {
					$email_account_name = $player->getAccount()->getEMail();
				}
				else {
					$io->error('Cannot find player or account with name: ' . $email_account_name);
					return 3;
				}
			}
		}

		if(!\Validator::email($email_account_name)) {
			$io->error('Invalid E-Mail format');
			return 4;
		}

		if(strlen($subject) > 255) {
			$io->error('Subject max length is 255 characters');
			return 5;
		}

		if(!_mail($email_account_name, $subject, $message)) {
			$io->error('An error occurred while sending email. More info can be found in system/logs/mailer-error.log');
			return 6;
		}

		$io->success('Mail sent to ' . $email_account_name . '.');
		return Command::SUCCESS;
	}
}
