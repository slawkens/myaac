<?php

namespace MyAAC\TwoFactorAuth\Gateway;

use MyAAC\TwoFactorAuth\Interface\AuthGatewayInterface;
use OTPHP\TOTP;

class AppAuthGateway extends BaseAuthGateway implements AuthGatewayInterface
{
	public function verifyCode(string $code): bool
	{
		$otp = $this->twoFactorAuth->initTOTP($this->account->getCustomField('2fa_secret'));

		$timestamp = time();
		$period = $otp->getPeriod();

		return $otp->verify($code, $timestamp - $period)
			|| $otp->verify($code, $timestamp)
			|| $otp->verify($code, $timestamp + $period);
	}
}
