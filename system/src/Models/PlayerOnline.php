<?php

namespace MyAAC\Models;
use Illuminate\Database\Eloquent\Model;

class PlayerOnline extends Model {

	protected $table = 'players_online';

	public $timestamps = false;

	protected $fillable = [
		'player_id',
	];

	public function player()
	{
		return $this->belongsTo(Player::class);
	}
}
