<?php

namespace MyAAC\TwoFactorAuth\Gateway;

use MyAAC\TwoFactorAuth\Interface\AuthGatewayInterface;

class AppAuthGateway extends BaseAuthGateway implements AuthGatewayInterface
{
	public function verifyCode(string $code): bool
	{
		return true;
	}
}
