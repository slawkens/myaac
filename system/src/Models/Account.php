<?php

namespace MyAAC\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $premium_ends_at
 * @property integer $premend
 * @property integer $lastday
 * @property integer $premdays
 */
class Account extends Model {

	const GRATIS_PREMIUM_DAYS = 65535;

	protected $table = 'accounts';

	public $timestamps = false;

	protected $casts = [
		'lastday' => 'integer',
		'premdays' => 'integer',
		'premend' => 'integer',
		'premium_ends_at' => 'integer',
	];

	public function players()
	{
		return $this->hasMany(Player::class);
	}

	public function viplist()
	{
		return $this->hasMany(AccountVipList::class);
	}

	public function getPremiumDaysAttribute()
	{
		if(isset($this->premium_ends_at) || isset($this->premend) ||
			(isCanary() && isset($this->lastday))) {
				$col = (isset($this->premium_ends_at) ? 'premium_ends_at' : (isset($this->lastday) ? 'lastday' : 'premend'));
				$ret = ceil(($this->{$col} - time()) / (24 * 60 * 60));
				return max($ret, 0);
		}

		if($this->premdays == 0) {
			return 0;
		}

		if($this->premdays == self::GRATIS_PREMIUM_DAYS){
			return self::GRATIS_PREMIUM_DAYS;
		}

		$ret = ceil($this->premdays - ((int)date("z", time()) + (365 * (date("Y", time()) - date("Y", $this->lastday))) - date("z", $this->lastday)));
		return max($ret, 0);
	}

	public function getIsPremiumAttribute(): bool
	{
		if(isset($this->premium_ends_at) || isset($this->premend) ||
			(isCanary() && isset($this->lastday))) {
			$col = (isset($this->premium_ends_at) ? 'premium_ends_at' : (isset($this->lastday) ? 'lastday' : 'premend'));
			return $this->{$col} > time();
		}

		if($this->premdays == self::GRATIS_PREMIUM_DAYS){
			return true;
		}

		return ($this->premdays - (date("z", time()) + (365 * (date("Y", time()) - date("Y", $this->lastday))) - date("z", $this->lastday)) > 0);
	}

}
