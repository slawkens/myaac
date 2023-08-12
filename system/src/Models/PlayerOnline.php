<?php

namespace MyAAC\Models;
use Illuminate\Database\Eloquent\Model;

class PlayerOnline extends Model {

	protected $table = 'players_online';

	public $timestamps = false;

	public function player()
	{
		return $this->belongsTo(Player::class);
	}
}
