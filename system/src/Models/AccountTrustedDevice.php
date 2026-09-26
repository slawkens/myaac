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

	protected $table = TABLE_PREFIX . 'account_2fa_trusted_devices';

	protected $fillable = ['account_id', 'device_token_hash', 'user_agent', 'expires_at', 'type'];

	protected $casts = [
		'expires_at' => 'datetime',
	];

	public function account(): BelongsTo {
		return $this->belongsTo(Account::class);
	}

}
