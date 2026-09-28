<?php

namespace MyAAC\TwoFactorAuth\Gateway;

use MyAAC\TwoFactorAuth\TwoFactorAuth;

class BaseAuthGateway
{
	protected \OTS_Account $account;
	protected TwoFactorAuth $twoFactorAuth;

	public function __construct(\OTS_Account $account, TwoFactorAuth $twoFactorAuth) {
		$this->account = $account;
		$this->twoFactorAuth = $twoFactorAuth;
	}
}
