<?php

namespace MyAac\Models;
use Illuminate\Database\Eloquent\Model;

class PlayerSkill extends Model {

	protected $table = 'player_viplist';

	public $timestamps = false;

	public function player()
	{
		return $this->belongsTo(Player::class);
	}
}
