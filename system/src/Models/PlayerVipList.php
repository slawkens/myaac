<?php

namespace MyAAC\Models;
use Illuminate\Database\Eloquent\Model;

class PlayerVipList extends Model {

	protected $table = 'player_viplist';

	public $timestamps = false;

	public function player()
	{
		return $this->belongsTo(Player::class);
	}

	public function vip()
	{
		return $this->belongsTo(Player::class, 'vip_id');
	}
}
