<?php

namespace MyAAC\Models;
use Illuminate\Database\Eloquent\Model;

class GuildRank extends Model {

	protected $table = 'guild_ranks';

	public $timestamps = false;

	protected $fillable = ['guild_id', 'name', 'level'];

	public function guild()
	{
		return $this->belongsTo(Guild::class);
	}

}
