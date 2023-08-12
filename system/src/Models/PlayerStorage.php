<?php

namespace MyAAC\Models;
use Illuminate\Database\Eloquent\Model;

class PlayerStorage extends Model {

	protected $table = 'player_storage';

	public $timestamps = false;

	public function player()
	{
		return $this->belongsTo(Player::class);
	}
}
