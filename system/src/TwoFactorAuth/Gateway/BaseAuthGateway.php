<?php

namespace MyAAC\TwoFactorAuth\Gateway;

class BaseAuthGateway
{
	protected \OTS_Account $account;

	public function __construct(\OTS_Account $account) {
		$this->account = $account;
	}
}
