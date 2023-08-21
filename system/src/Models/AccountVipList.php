<?php

namespace MyAAC\Models;
use Illuminate\Database\Eloquent\Model;

class AccountVipList extends Model {

	protected $table = 'account_viplist';

	public $timestamps = false;

	public function account()
	{
		return $this->belongsTo(Account::class);
	}

	public function player()
	{
		return $this->belongsTo(Player::class);
	}
}
