<?php

namespace MyAAC\Models;
use Illuminate\Database\Eloquent\Model;

class PlayerDepotItem extends Model {

	protected $table = 'player_depotitems';

	public $timestamps = false;

	public function player()
	{
		return $this->belongsTo(Player::class);
	}
}
