<?php

namespace MyAAC\Models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model {

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
		if(isset($this->premium_ends_at) || isset($this->premend)) {
			$col = isset($this->premium_ends_at) ? 'premium_ends_at' : 'premend';
			$ret = ceil(($this->{$col}- time()) / (24 * 60 * 60));
			return $ret > 0 ? $ret : 0;
		}

		if($this->premdays == 0) {
			return 0;
		}

		global $config;
		if(isset($config['lua']['freePremium']) && getBoolean($config['lua']['freePremium'])) return -1;

		if($this->premdays == 65535){
			return 65535;
		}

		$ret = ceil($this->premdays - (date("z", time()) + (365 * (date("Y", time()) - date("Y", $this->lastday))) - date("z", $this->lastday)));
		return $ret > 0 ? $ret : 0;
	}

	public function getIsPremiumAttribute()
	{
		global $config;
        if(isset($config['lua']['freePremium']) && getBoolean($config['lua']['freePremium'])) return true;

	    if(isset($this->premium_ends_at)) {
		    return $this->premium_ends_at > time();
	    }

		if(isset($this->premend)) {
			return $this->premend > time();
		}

		return ($this->premdays - (date("z", time()) + (365 * (date("Y", time()) - date("Y", $this->lastday))) - date("z", $this->lastday)) > 0);
	}

}
