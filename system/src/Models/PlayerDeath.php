<?php

namespace MyAAC\Models;
use Illuminate\Database\Eloquent\Model;

class PlayerDeath extends Model {

	protected $table = 'player_deaths';

	public $timestamps = false;

	public function player()
	{
		return $this->belongsTo(Player::class);
	}

	public function killer()
	{
		return $this->belongsTo(Player::class, 'killed_by');
	}

	public function scopeUnjustified($query) {
		$query->where('unjustified', 1);
	}
}
