<?php

namespace MyAAC\TwoFactorAuth;

use MyAAC\Models\AccountEMailCode;
use MyAAC\TwoFactorAuth\Gateway\AppAuthGateway;
use MyAAC\TwoFactorAuth\Gateway\EmailAuthGateway;
use MyAAC\TwoFactorAuth\Interface\AuthGatewayInterface;

class TwoFactorAuth
{
	const TYPE_NONE = 0;
	const TYPE_EMAIL = 1;
	const TYPE_APP = 2;

	const EMAIL_CODE_VALID_UNTIL = 24 * 60 * 60;

	private \OTS_Account $account;
	private int $authType;
	private EmailAuthGateway|AppAuthGateway $authGateway;

	public function __construct(\OTS_Account $account) {
		$this->account = $account;

		$this->authType = (int)$this->account->getCustomField('2fa_type');
		if ($this->authType === self::TYPE_EMAIL) {
			$this->authGateway = new EmailAuthGateway($account);
		}
		else if ($this->authType === self::TYPE_APP) {
			$this->authGateway = new AppAuthGateway($account);
		}
	}

	public function process()
	{
		global $twig;

		if ($this->authType == TwoFactorAuth::TYPE_EMAIL) {
			if (!$this->authGateway->hasRecentEmailCode()) {
				$this->authGateway->resendEmailCode();
				success('Resent email.');
			}

			define('HIDE_LOGIN_BOX', true);
			$twig->display('account.2fa.email-code.login.html.twig');
			return false;
		}

		return true;
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
}
