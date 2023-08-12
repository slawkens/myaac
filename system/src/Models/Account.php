<?php

namespace MyAac\Models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model {

	protected $table = 'accounts';

	public $timestamps = false;

	public function players()
	{
		return $this->hasMany(Player::class);
	}

	public function viplist()
	{
		return $this->hasMany(AccountVipList::class);
	}

}
