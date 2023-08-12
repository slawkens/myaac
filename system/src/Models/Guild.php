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

	public function members()
	{
		return $this->belongsToMany(Player::class, 'guild_membership')->withPivot('rank_id', 'nick');
	}

	public function invites()
	{
		return $this->belongsToMany(Player::class, 'guild_invites');
	}

}
