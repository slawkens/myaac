<?php

namespace MyAAC\Models;
use Illuminate\Database\Eloquent\Model;

class Player extends Model {

	protected $table = 'players';

	public $timestamps = false;

	protected $casts = [
		'worldid' => 'integer',
		'sex' => 'integer',
		'level' => 'integer',
		'vocation' => 'integer',
		'looktype' => 'integer',
		'lookhead' => 'integer',
		'lookbody' => 'integer',
		'looklegs' => 'integer',
		'lookfeet' => 'integer',
		'lookaddons' => 'integer',
		'isreward' => 'integer',
	];

	public function scopeNotDeleted($query) {
		global $db;

		$column =  'deleted';
		if($db->hasColumn('players', 'deletion')) {
			$column = 'deletion';
		}

		$query->where($column, '!=', 0);
	}

	public function scopeWithOnlineStatus($query) {
		global $db;
		$query->when($db->hasTable('players_online'), function ($query) {
			$query->with('onlineTable');
		});
	}

	public function getOnlineStatusAttribute()
	{
		global $db;
		if ($db->hasColumn('players', 'online')) {
			return $this->online;
		}

		if ($db->hasTable('players_online')) {
			return $this->onlineTable != null;
		}

		return false;
	}

	public function onlineTable()
	{
		return $this->belongsTo(PlayerOnline::class);
	}

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
