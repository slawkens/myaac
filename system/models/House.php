<?php

namespace MyAac\Models;
use Illuminate\Database\Eloquent\Model;

class House extends Model {

	protected $table = 'houses';

	public function owner()
	{
		return $this->belongsTo(Player::class, 'owner');
	}
}
