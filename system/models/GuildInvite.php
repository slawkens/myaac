<?php

namespace MyAac\Models;
use Illuminate\Database\Eloquent\Model;

class GuildInvites extends Model {

	protected $table = 'guild_invites';

	public function player()
	{
		return $this->belongsTo(Player::class);
	}

	public function guild()
	{
		return $this->belongsTo(Guild::class);
	}

}
