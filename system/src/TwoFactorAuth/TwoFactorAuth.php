<?php

namespace MyAAC\TwoFactorAuth;

use MyAAC\Models\AccountEMailCode;
use MyAAC\TwoFactorAuth\Gateway\AppAuthGateway;
use MyAAC\TwoFactorAuth\Gateway\EmailAuthGateway;
use OTPHP\TOTP;

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

		$view = 'app';
		if ($this->authType == self::TYPE_EMAIL) {
			$view = 'email';#
		}

		if (empty($code)) {
			if ($this->authType == self::TYPE_EMAIL) {
				if (!$this->hasRecentEmailCode(15 * 60)) {
					$this->resendEmailCode();
				}
			}

			define('HIDE_LOGIN_BOX', true);
			$twig->display("account/2fa/$view/login.html.twig", [
				'account_login' => $login_account,
				'password_login' => $login_password,
				'remember_me' => $remember_me,
			]);

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

		if ($this->authType == self::TYPE_APP) {
			$errors[] = 'The token is invalid!';
		}
		else {
			$errors[] = 'Invalid E-Mail code!';
		}

		$twig->display('error_box.html.twig', ['errors' => $errors]);

		$twig->display("account/2fa/$view/login.html.twig",
			[
				'account_login' => $login_account,
				'password_login' => $login_password,
				'remember_me' => $remember_me,

				'wrongCode' => true,
			]);

		return false;
	}

	public function processClientLogin($code, string &$error, &$errorCode): bool
	{
		if (!$this->isActive()) {
			return true;
		}

		if ($this->authType == self::TYPE_EMAIL) {
			$errorCode = 8;
		}

		if ($code === false) {
			$error = 'Submit a valid two-factor authentication token.';

			if ($this->authType == self::TYPE_EMAIL) {
				if (!$this->hasRecentEmailCode(15 * 60)) {
					$this->resendEmailCode();
				}
			}

			return false;
		}

		if (!$this->getAuthGateway()->verifyCode($code)) {
			$error = 'Two-factor authentication failed, token is wrong.';

			return false;
		}

		if ($this->authType === self::TYPE_EMAIL) {
			$this->deleteOldCodes();
		}

		return true;
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
		if ($this->authType == self::TYPE_EMAIL) {
			$twoFactorView = 'account/2fa/main.protected.html.twig';
			$twoFactorView2 = 'account/2fa/email/manage.connected.html.twig';
		}
		elseif ($this->authType == self::TYPE_APP) {
			$twoFactorView = 'account/2fa/app/manage.connected.html.twig';
			$twoFactorView2 = 'account/2fa/main.protected.html.twig';
		}
		else {
			$twoFactorView = 'account/2fa/app/manage.enable.html.twig';
			$twoFactorView2 = 'account/2fa/email/manage.enable.html.twig';
		}

		return [$twoFactorView, $twoFactorView2];
	}

	public function enable(int $type): void {
		$this->account->setCustomField('2fa_type', $type);
	}

	public function disable(): void
	{
		global $db;

		$this->account->setCustomField('2fa_type', self::TYPE_NONE);

		if ($db->hasColumn('accounts', 'secret')) {
			$this->account->setCustomField('secret', null);
		}

		$this->account->setCustomField('2fa_secret', '');
	}

	public function isActive(?int $authType = null): bool {
		if ($authType !== null) {
			return $this->authType === $authType;
		}

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

	public function appInitTOTP(string $secret): TOTP
	{
		$otp = TOTP::createFromSecret($secret);

		$otp->setLabel($this->account->getEmail());
		$otp->setIssuer(configLua('serverName'));

		return $otp;
	}

	public function appDisplayEnable(string $secret, ?TOTP $otp = null, array $errors = []): void
	{
		global $twig;

		if ($otp === null) {
			$otp = $this->appInitTOTP($secret);
		}

		$grCodeUri = $otp->getQrCodeUri(
			'https://api.qrserver.com/v1/create-qr-code/?data=[DATA]&size=200x200&ecc=M',
			'[DATA]'
		);

		$twig->display('account/2fa/app/enable.html.twig', [
			'grCodeUri' => $grCodeUri,
			'secret' => $secret,
			'errors' => $errors,
		]);
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
