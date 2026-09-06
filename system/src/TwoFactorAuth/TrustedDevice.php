<?php
/**
 * @package   MyAAC
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2026 MyAAC
 * @link      https://my-aac.org
 */
namespace MyAAC\TwoFactorAuth;

use MyAAC\Models\AccountTrustedDevice;

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
	public static function register(int $accountId, int $type = 0): void
	{
		// 1. Generate secure token
		$token = bin2hex(random_bytes(32));
		$hash = hash('sha256', $token);

		// 2. Calculate expiry date (e.g., 30 days)
		$expiryTime = time() + (self::DURATION_DAYS * 24 * 60 * 60);
		$expiresAt = date('Y-m-d H:i:s', $expiryTime);
		$userAgent = self::getUserAgent();

		AccountTrustedDevice::create([
			'account_id' => $accountId,
			'device_token_hash' => $hash,
			'user_agent' => $userAgent,
			'expires_at' => $expiresAt,
			'type' => $type
		]);

		setcookie(self::COOKIE_NAME, $token, [
			'expires' => $expiryTime,
			'path' => '/',
			'httponly' => true,
			'samesite' => 'Lax',
		]);
	}

	/**
	 * Checks whether the user's current device is trusted.
	 */
	public static function isDeviceTrusted(int $accountId): bool {
		// If no cookie exists, the device is not trusted
		if (empty($_COOKIE[self::COOKIE_NAME])) {
			return false;
		}

		$token = $_COOKIE[self::COOKIE_NAME];
		$hash = hash('sha256', $token);
		$userAgent = self::getUserAgent();

		// Token und User-Agent in der Datenbank prüfen
		$trustedDevice = AccountTrustedDevice::where('account_id', $accountId)
			->where('device_token_hash', $hash)
			->where('user_agent', $userAgent)
			->where('expires_at', '>', date('Y-m-d H:i:s'))
			->exists();

		return (bool) $trustedDevice;
	}

	/**
	 * Updates the last login timestamp for the trusted device in the database.
	 */
	public static function updateLastLogin(int $accountId): void
	{
		AccountTrustedDevice::where('account_id', $accountId)
			->where('user_agent', self::getUserAgent())
			->where('expires_at', '>', date('Y-m-d H:i:s'))
			->update(['updated_at' => date('Y-m-d H:i:s')]);
	}

	/**
	 * Retrieves the trusted devices.
	 */
	public static function getAll(int $accountId): array
	{
		return AccountTrustedDevice::where('account_id', $accountId)
			->where('expires_at', '>', date('Y-m-d H:i:s'))
			->get()
			->toArray();
	}

	/**
	 * Retrieves a single trusted device by id.
	 */
	public static function get(int $accountId, int $id): ?array
	{
		$device = AccountTrustedDevice::where('account_id', $accountId)
			->where('id', $id)
			->first();

		return $device ? $device->toArray() : null;
	}

	/**
	 * Removes a single trusted device by id.
	 */
	public static function remove(int $accountId, int $id): void {
		AccountTrustedDevice::where('account_id', $accountId)
			->where('id', $id)
			->delete();
	}

	/**
	 * Removes all trusted devices for the given account.
	 */
	public static function removeAll(int $accountId): void {
		AccountTrustedDevice::where('account_id', $accountId)->delete();
	}

	/**
	 * Clears all expired trusted devices from the database.
	 */
	public static function clear(): void {
		AccountTrustedDevice::where('expires_at', '<', date('Y-m-d H:i:s'))->delete();
	}

	public static function getUserAgent(): string {
		return substr($_SERVER['HTTP_USER_AGENT'] ?? 'Unknown', 0, 255);
	}
}
