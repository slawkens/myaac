<?php

namespace MyAAC\Models;
use Illuminate\Database\Eloquent\Model;

class PlayerKillers extends Model {

	protected $table = 'player_killers';

	public $timestamps = false;

	public function player()
	{
		return $this->belongsTo(Player::class);
	}
}
