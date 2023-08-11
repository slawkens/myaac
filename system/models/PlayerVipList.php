<?php

namespace MyAac\Models;
use Illuminate\Database\Eloquent\Model;

class PlayerSkill extends Model {

	protected $table = 'player_viplist';

	public function player()
	{
		return $this->belongsTo(Player::class);
	}
}
