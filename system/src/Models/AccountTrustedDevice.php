<?php
/**
 * @package   MyAAC
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2026 MyAAC
 * @link      https://my-aac.org
 */
namespace MyAAC\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use MyAAC\Models\Account;

class AccountTrustedDevice extends Model {

	protected $table = TABLE_PREFIX . 'account_trusted_devices';

	protected $fillable = ['account_id', 'device_token_hash', 'user_agent', 'expires_at', 'type'];

	protected $casts = [
		'expires_at' => 'datetime',
	];

	public function account(): BelongsTo {
		return $this->belongsTo(Account::class);
	}

}

/**
 * TODO: Implement proper migration handling for the account_trusted_devices table.
 */
global $db;
if (!$db->hasTable(TABLE_PREFIX . 'account_trusted_devices')) {
	$db->query("
		CREATE TABLE " . TABLE_PREFIX . "account_trusted_devices (
			`id` int AUTO_INCREMENT PRIMARY KEY,
			`account_id` int NOT NULL,
			`device_token_hash` varchar(64) NOT NULL,
			`user_agent` varchar(255) NOT NULL,
			`expires_at` timestamp NOT NULL,
			`type` tinyint NOT NULL,
			`created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
			`updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			INDEX(account_id),
			INDEX(device_token_hash)
		);
	");
}
