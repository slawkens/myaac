<?php

namespace MyAac\Models;
use Illuminate\Database\Eloquent\Model;

class Guild extends Model {

	protected $table = 'guilds';

	public $timestamps = false;

	public function owner()
	{
		return $this->belongsTo(Player::class, 'ownerid');
	}

}
