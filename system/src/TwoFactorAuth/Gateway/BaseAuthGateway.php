<?php

namespace MyAAC\TwoFactorAuth\Gateway;

use MyAAC\Models\Account;

use MyAAC\TwoFactorAuth\TwoFactorAuth;

class BaseAuthGateway
{
	protected Account $account;
	protected TwoFactorAuth $twoFactorAuth;

	public function __construct(Account $account, TwoFactorAuth $twoFactorAuth) {
		$this->account = $account;
		$this->twoFactorAuth = $twoFactorAuth;
	}
}
