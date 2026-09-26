<?php

namespace MyAAC\TwoFactorAuth\Interface;

use MyAAC\TwoFactorAuth\TwoFactorAuth;

interface AuthGatewayInterface
{
	public function __construct(\OTS_Account $account, TwoFactorAuth $twoFactorAuth);
	public function verifyCode(string $code): bool;
}
