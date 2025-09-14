<?php

namespace MyAAC\TwoFactorAuth;

use MyAAC\Models\AccountEMailCode;
use MyAAC\TwoFactorAuth\Gateway\AppAuthGateway;
use MyAAC\TwoFactorAuth\Gateway\EmailAuthGateway;

class TwoFactorAuth
{
	const TYPE_NONE = 0;
	const TYPE_EMAIL = 1;
	const TYPE_APP = 2;
	// maybe later
	//const TYPE_SMS = 3;

	const EMAIL_CODE_VALID_UNTIL = 24 * 60 * 60;

	private static self $instance;

	private \OTS_Account $account;
	private int $authType;
	private EmailAuthGateway|AppAuthGateway $authGateway;

	public function __construct(\OTS_Account|int $account) {
		if (is_int($account)) {
			$this->account = new \OTS_Account();
			$this->account->load($account);
		}
		else {
			$this->account = $account;
		}

		$this->authType = (int)$this->account->getCustomField('2fa_type');
		$this->setAuthGateway($this->authType);
	}

	public static function getInstance($account = null): self
	{
		if (!isset(self::$instance)) {
			self::$instance = new self($account);
		}

		return self::$instance;
	}

	public function process($login_account, $login_password, $remember_me, $code): bool
	{
		global $twig;

		if (!$this->isActive()) {
			return true;
		}

		if (empty($code)) {
			if ($this->authType == self::TYPE_EMAIL) {
				if (!$this->hasRecentEmailCode(15 * 60)) {
					$this->resendEmailCode();
					//success('Resent email.');
				}

				define('HIDE_LOGIN_BOX', true);
				$twig->display('account.2fa.email.login.html.twig', [
					'account_login' => $login_account,
					'password_login' => $login_password,
					'remember_me' => $remember_me,
				]);
			}
			else {
				echo 'Two Factor App Auth';
			}

			return false;
		}

		if ($this->getAuthGateway()->verifyCode($code)) {
			if ($this->authType === self::TYPE_EMAIL) {
				$this->deleteOldCodes();
			}

			return true;
		}

		if (setting('core.mail_enabled')) {
			$mailBody = $twig->render('mail.account.2fa.email-code.wrong-attempt.html.twig');

			if (!_mail($this->account->getEMail(), configLua('serverName') . ' - Failed Two-Factor Authentication Attempt', $mailBody)) {
				error('An error occurred while sending email. For Admin: More info can be found in system/logs/mailer-error.log');
			}
		}

		define('HIDE_LOGIN_BOX', true);

		$errors[] = 'Invalid email code!';
		$twig->display('error_box.html.twig', ['errors' => $errors]);

		$twig->display('account.2fa.email.login.html.twig',
			[
				'account_login' => $login_account,
				'password_login' => $login_password,
				'remember_me' => $remember_me,

				'wrongCode' => true,
			]);

		return false;
	}

	public function setAuthGateway(int $authType): void
	{
		if ($authType === self::TYPE_EMAIL) {
			$this->authGateway = new EmailAuthGateway($this->account);
		}
		else if ($authType === self::TYPE_APP) {
			$this->authGateway = new AppAuthGateway($this->account);
		}
	}

	public function getAccountManageViews(): array
	{
		$twoFactorView = 'account.2fa.protected.html.twig';
		if ($this->authType == self::TYPE_EMAIL) {
			$twoFactorView2 = 'account.2fa.email.activated.html.twig';
		}
		elseif ($this->authType == self::TYPE_APP) {
			$twoFactorView2 = 'account.2fa.app.activated.html.twig';
		}
		else {
			$twoFactorView = 'account.2fa.connect.html.twig';
			$twoFactorView2 = 'account.2fa.email.activate.html.twig';
		}

		return [$twoFactorView, $twoFactorView2];
	}

	public function enable(int $type): void {
		$this->account->setCustomField('2fa_type', $type);
	}

	public function disable(): void {
		$this->account->setCustomField('2fa_type', self::TYPE_NONE);
	}

	public function isActive(): bool {
		return $this->authType != self::TYPE_NONE;
	}

	public function getAuthType(): int {
		return $this->authType;
	}

	public function getAuthGateway(): AppAuthGateway|EmailAuthGateway  {
		return $this->authGateway;
	}

	public function hasRecentEmailCode($since = self::EMAIL_CODE_VALID_UNTIL): bool {
		return AccountEMailCode::where('account_id', '=', $this->account->getId())->where('created_at', '>', time() - $since)->first() !== null;
	}

	public function deleteOldCodes(): void {
		AccountEMailCode::where('account_id', '=', $this->account->getId())->delete();
	}

	public function resendEmailCode(): void
	{
		global $twig;

		$newCode = generateRandomString(6, true, false, true);
		AccountEMailCode::create([
			'account_id' => $this->account->getId(),
			'code' => $newCode,
			'created_at' => time(),
		]);

		$mailBody = $twig->render('mail.account.2fa.email-code.html.twig', [
			'code' => $newCode,
		]);

		if (!_mail($this->account->getEMail(), configLua('serverName') . ' - Requested Authentication Email Code', $mailBody)) {
			error('An error occurred while sending email. For Admin: More info can be found in system/logs/mailer-error.log');
		}
	}
}
