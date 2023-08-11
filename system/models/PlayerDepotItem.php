<?php

namespace MyAac\Models;
use Illuminate\Database\Eloquent\Model;

class PlayerDepotItem extends Model {

	protected $table = 'player_depotitems';

	public function player()
	{
		return $this->belongsTo(Player::class);
	}
}
