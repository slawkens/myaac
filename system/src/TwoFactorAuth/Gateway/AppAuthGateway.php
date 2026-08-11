<?php

namespace MyAAC\TwoFactorAuth\Gateway;

use MyAAC\TwoFactorAuth\Interface\AuthGatewayInterface;
use OTPHP\TOTP;

class AppAuthGateway extends BaseAuthGateway implements AuthGatewayInterface
{
	public function verifyCode(string $code): bool
	{
		$otp = TOTP::createFromSecret($this->account->getCustomField('2fa_secret'));

		$otp->setLabel($this->account->getEmail());
		$otp->setIssuer(configLua('serverName'));

		$timestamp = time();
		$period = $otp->getPeriod();

		return $otp->verify($code, $timestamp - $period)
			|| $otp->verify($code, $timestamp)
			|| $otp->verify($code, $timestamp + $period);
	}
}
