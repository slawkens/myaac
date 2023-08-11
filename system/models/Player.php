<?php

namespace MyAac\Models;
use Illuminate\Database\Eloquent\Model;

class Player extends Model {

	protected $table = 'players';

	public $timestamps = false;

	public function player()
	{
		return $this->belongsTo(Player::class);
	}

	public function scopeOnline($query) {
		$query->where('online', '>', 0);
	}
}
