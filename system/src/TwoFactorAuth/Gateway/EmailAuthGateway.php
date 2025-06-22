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

	public function hasRecentEmailCode(): bool {
		return AccountEMailCode::where('account_id', '=', $this->account->getId())->where('created_at', '>', time() - TwoFactorAuth::EMAIL_CODE_VALID_UNTIL)->first() !== null;
	}

	public function deleteOldCodes(): void {
		AccountEMailCode::where('account_id', '=', $this->account->getId())->delete();
	}

	public function resendEmailCode(): void
	{
		global $twig;

		$newCode = generateRandomString(6, true, false, true);
		AccountEMailCode::create([
			'account_id' => $this->account->getId(),
			'code' => $newCode,
			'created_at' => time(),
		]);

		$mailBody = $twig->render('mail.account.2fa.email-code.html.twig', [
			'code' => $newCode,
		]);

		if (!_mail($this->account->getEMail(), configLua('serverName') . ' - Requested Authentication Email Code', $mailBody)) {
			error('An error occurred while sending email. For Admin: More info can be found in system/logs/mailer-error.log');
		}
	}
}

