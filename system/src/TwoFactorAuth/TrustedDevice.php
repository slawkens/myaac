<?php
/**
 * @package   MyAAC
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2026 MyAAC
 * @link      https://my-aac.org
 */
namespace MyAAC\TwoFactorAuth;

use MyAAC\Models\AccountTwoFactorTrustedDevice;

class TrustedDevice
{
	const COOKIE_NAME = 'trusted_device';
	const DURATION_DAYS = 30;

	const TYPE_UNKNOWN = 0;
	const TYPE_WEBSITE = 1;
	const TYPE_CLIENT = 2;

	/**
	 * Registers a new trusted device after successful 2FA.
	 */
	public static function register(int $accountId, int $type = 0, ?string $token = null, ?string $userAgent = null): void
	{
		// 1. Generate secure token if not provided
		if ($token === null) {
			$token = bin2hex(random_bytes(32));
		}

		$hash = hash('sha256', $token);

		// 2. Calculate expiry date (e.g., 30 days)
		$expiryTime = time() + (self::DURATION_DAYS * 24 * 60 * 60);
		$expiresAt = \DateTime::createFromFormat('U', $expiryTime);

		if ($userAgent === null) {
			$userAgent = self::getUserAgent();
		}

		$twoFactorDevice = new AccountTwoFactorTrustedDevice();
		$twoFactorDevice->id = uuidv7();
		$twoFactorDevice->account_id = $accountId;
		$twoFactorDevice->device_token_hash = $hash;
		$twoFactorDevice->system = $userAgent;
		$twoFactorDevice->expires_at = $expiresAt;
		$twoFactorDevice->type = $type;
		$twoFactorDevice->save();

		setcookie(self::COOKIE_NAME, $token, [
			'expires' => $expiryTime,
			'path' => '/',
			'httponly' => true,
			'samesite' => 'Lax',
		]);
	}

	public static function isClientTrusted(int $accountId, string $token, string $operatingSystem): bool {
		return self::isDeviceTrusted($accountId, $token, $operatingSystem);
	}

	public static function isBrowserTrusted(int $accountId): bool
	{
		// If no cookie exists, the device is not trusted
		if (empty($_COOKIE[self::COOKIE_NAME])) {
			return false;
		}

		$token = $_COOKIE[self::COOKIE_NAME];
		$userAgent = self::getUserAgent();

		return self::isDeviceTrusted($accountId, $token, $userAgent);
	}

	/**
	 * Checks whether the user's current device is trusted.
	 */
	public static function isDeviceTrusted(int $accountId, string $token, string $userAgent): bool
	{
		$hash = hash('sha256', $token);

		// Token und User-Agent in der Datenbank prüfen
		$trustedDevice = AccountTwoFactorTrustedDevice::where('account_id', $accountId)
			->where('device_token_hash', $hash)
			->where('system', $userAgent)
			->where('expires_at', '>', date('Y-m-d H:i:s'))
			->exists();

		return (bool) $trustedDevice;
	}

	/**
	 * Updates the last login timestamp for the trusted device in the database.
	 */
	public static function updateLastLogin(int $accountId, ?string $token, ?string $system = null): void
	{
		if ($token === null) {
			$token = $_COOKIE[self::COOKIE_NAME];
		}

		$hash = hash('sha256', $token);

		if ($system === null) {
			$system = self::getUserAgent();
		}

		$now = date('Y-m-d H:i:s');

		AccountTwoFactorTrustedDevice::where('account_id', $accountId)
			->where('system', $system)
			->where('device_token_hash', $hash)
			->where('expires_at', '>', $now)
			->update(['updated_at' => $now]);
	}

	/**
	 * Retrieves the trusted devices.
	 */
	public static function getAll(int $accountId): array
	{
		return AccountTwoFactorTrustedDevice::where('account_id', $accountId)
			->where('expires_at', '>', date('Y-m-d H:i:s'))
			->get()
			->toArray();
	}

	/**
	 * Retrieves a single trusted device by id.
	 */
	public static function get(int $accountId, string $id): ?array
	{
		$device = AccountTwoFactorTrustedDevice::where('account_id', $accountId)
			->where('id', $id)
			->first();

		return $device ? $device->toArray() : null;
	}

	/**
	 * Removes a single trusted device by id.
	 */
	public static function remove(int $accountId, string $id): void {
		AccountTwoFactorTrustedDevice::where('account_id', $accountId)
			->where('id', $id)
			->delete();
	}

	/**
	 * Removes all trusted devices for the given account.
	 */
	public static function removeAll(int $accountId): void {
		AccountTwoFactorTrustedDevice::where('account_id', $accountId)->delete();
	}

	/**
	 * Clears all expired trusted devices from the database.
	 */
	public static function clear(): void {
		AccountTwoFactorTrustedDevice::where('expires_at', '<', date('Y-m-d H:i:s'))->delete();
	}

	public static function getUserAgent(): string {
		return substr($_SERVER['HTTP_USER_AGENT'] ?? 'Unknown', 0, 255);
	}
}
