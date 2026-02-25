<?php

namespace MyAAC\TwoFactorAuth\Gateway;

use MyAAC\Models\AccountEMailCode;
use MyAAC\TwoFactorAuth\Interface\AuthGatewayInterface;
use MyAAC\TwoFactorAuth\TwoFactorAuth;

class EmailAuthGateway extends BaseAuthGateway implements AuthGatewayInterface
{
	public function verifyCode(string $code): bool
	{
		return AccountEMailCode::where('account_id', '=', $this->account->getId())->where('code', $code)->where('created_at', '>', time() - TwoFactorAuth::EMAIL_CODE_VALID_UNTIL)->first() !== null;
	}
}

