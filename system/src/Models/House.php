<?php

namespace MyAAC\Models;
use Illuminate\Database\Eloquent\Model;

class House extends Model {

	protected $table = 'houses';

	public $timestamps = false;

	public function owner()
	{
		return $this->belongsTo(Player::class, 'owner');
	}
}
