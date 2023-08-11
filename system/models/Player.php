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

	public function storages()
	{
		return $this->hasMany(PlayerStorage::class);
	}

	public function items()
	{
		return $this->hasMany(PlayerItem::class);
	}

	public function deaths()
	{
		return $this->hasMany(PlayerDeath::class);
	}

	public function houses()
	{
		return $this->hasMany(House::class, 'owner');
	}

	public function skills()
	{
		return $this->hasMany(PlayerSkill::class);
	}

	public function viplist()
	{
		return $this->hasMany(PlayerVipList::class);
	}

	public function scopeOnline($query) {
		$query->where('online', '>', 0);
	}
}
