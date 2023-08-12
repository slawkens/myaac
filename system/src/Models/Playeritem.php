<?php

namespace MyAAC\Models;
use Illuminate\Database\Eloquent\Model;

class PlayerItem extends Model {

	protected $table = 'player_items';

	public $timestamps = false;

	public function player()
	{
		return $this->belongsTo(Player::class);
	}

}
