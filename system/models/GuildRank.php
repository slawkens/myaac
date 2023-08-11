<?php

namespace MyAac\Models;
use Illuminate\Database\Eloquent\Model;

class GuildRank extends Model {

	protected $table = 'guild_ranks';

	public function guild()
	{
		return $this->belongsTo(Guild::class);
	}

	public function player()
	{
		return $this->belongsTo(Player::class);
	}

}
