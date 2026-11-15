<?php

namespace MyAAC\TwoFactorAuth\Interface;

use MyAAC\Models\Account;
use MyAAC\TwoFactorAuth\TwoFactorAuth;

interface AuthGatewayInterface
{
	public function __construct(Account $account, TwoFactorAuth $twoFactorAuth);
	public function verifyCode(string $code): bool;
}
