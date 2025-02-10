<?php

namespace MyAAC\Models;
use Illuminate\Database\Eloquent\Model;

class GuildInvite extends Model {

	protected $table = 'guild_invites';

	public $timestamps = false;

	public function player()
	{
		return $this->belongsTo(Player::class);
	}

	public function guild()
	{
		return $this->belongsTo(Guild::class);
	}

}
