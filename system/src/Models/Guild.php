<?php

namespace MyAAC\Models;
use Illuminate\Database\Eloquent\Model;

class Guild extends Model {

	protected $table = 'guilds';

	public $timestamps = false;

	public function owner()
	{
		global $db;
		$column = 'ownerid';
		if($db->hasColumn('guilds', 'owner_id')) {
			$column = 'owner_id';
		}

		return $this->belongsTo(Player::class, $column);
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
