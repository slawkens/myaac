<?php

namespace MyAAC\TwoFactorAuth\Gateway;

use MyAAC\TwoFactorAuth\Interface\AuthGatewayInterface;
use OTPHP\TOTP;

class AppAuthGateway extends BaseAuthGateway implements AuthGatewayInterface
{
	public function verifyCode(string $code): bool
	{
		$otp = TOTP::createFromSecret($this->account->getCustomField('secret'));

		$otp->setLabel($this->account->getEmail());
		$otp->setIssuer(configLua('serverName'));

		return $otp->verify($code);
	}
}
