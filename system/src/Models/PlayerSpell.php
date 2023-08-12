<?php

namespace MyAAC\Models;
use Illuminate\Database\Eloquent\Model;

class PlayerSpell extends Model {

	protected $table = 'player_spells';

	public $timestamps = false;

	public function player()
	{
		return $this->belongsTo(Player::class);
	}
}
