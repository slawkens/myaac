<?php

namespace MyAAC\TwoFactorAuth\Interface;

interface AuthGatewayInterface
{
	public function __construct(\OTS_Account $account);
	public function verifyCode(string $code): bool;
}
