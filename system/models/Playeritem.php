<?php

namespace MyAac\Models;
use Illuminate\Database\Eloquent\Model;

class PlayerItem extends Model {

	protected $table = 'player_items';

	public function player()
	{
		return $this->belongsTo(Player::class);
	}

}
