<?php

namespace MyAac\Models;
use Illuminate\Database\Eloquent\Model;

class GuildMembership extends Model {

	protected $table = 'guild_membership';

	public function player()
	{
		return $this->belongsTo(Player::class);
	}

	public function guild()
	{
		return $this->belongsTo(Guild::class);
	}

	public function rank()
	{
		return $this->belongsTo(GuildRank::class, 'rank_id');
	}

}
