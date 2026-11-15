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

/**
 * @property string $id
 * @property int $account_id
 * @property string $device_token_hash
 * @property string $system
 * @property \DateTime $expires_at
 * @property int $type
 * @property \DateTime $created_at
 * @property \DateTime $updated_at
 */
class AccountTwoFactorTrustedDevice extends Model {

	protected $table = TABLE_PREFIX . 'account_two_factor_trusted_devices';

	protected $fillable = ['account_id', 'device_token_hash', 'system', 'expires_at', 'type'];

	protected $casts = [
		'expires_at' => 'datetime',
	];

	protected $keyType = 'string';
	public $incrementing = false;

	public function account(): BelongsTo {
		return $this->belongsTo(Account::class);
	}

}
