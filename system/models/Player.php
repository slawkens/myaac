<?php

namespace MyAac\Models;
use Illuminate\Database\Eloquent\Model;

class Player extends Model {

	protected $table = 'players';

	public $timestamps = false;

	public function account()
	{
		return $this->belongsTo(Account::class);
	}

	public function scopeOnline($query) {
		$query->where('online', '>', 0);
	}
}
