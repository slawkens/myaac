<?php

namespace MyAAC\Models;
use Illuminate\Database\Eloquent\Model;

class PlayerSkill extends Model {

	protected $table = 'player_skills';

	public $timestamps = false;

	public function player()
	{
		return $this->belongsTo(Player::class);
	}
}
