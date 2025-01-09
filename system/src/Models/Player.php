<?php

namespace MyAAC\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property int $level
 * @property int $vocation
 * @property int $online
 * @property int $looktype
 * @property int $lookhead
 * @property int $lookbody
 * @property int $looklegs
 * @property int $lookfeet
 * @property int $lookaddons
 * @property string $outfit_url
 * @property hasOne $onlineTable
 */
class Player extends Model {

	protected $table = 'players';

	public $timestamps = false;

	protected $casts = [
		'worldid' => 'integer',
		'sex' => 'integer',
		'level' => 'integer',
		'vocation' => 'integer',
		'promotion' => 'integer',
		'looktype' => 'integer',
		'lookhead' => 'integer',
		'lookbody' => 'integer',
		'looklegs' => 'integer',
		'lookfeet' => 'integer',
		'lookaddons' => 'integer',
		'isreward' => 'integer',
	];

	public function scopeOrderBySkill($query, $value)
	{
		global $db;
		$query->when($db->hasColumn('players', 'skill_fist'), function ($query) {

		});
	}

	public function getVocationNameAttribute()
	{
		$vocation = $this->vocation;
		if (isset($this->promotion)) {
			$vocation *= $this->promotion;
		}

		return config('vocations')[$vocation] ?? 'Unknown';
	}

	public function getIsDeletedAttribute()
	{
		if (isset($this->deleted)) {
			return $this->deleted !== 0;
		}

		if (isset($this->deletion)) {
			return $this->deletion !== 0;
		}

		return false;
	}

	public function scopeNotDeleted($query) {
		global $db;

		$column =  'deleted';
		if($db->hasColumn('players', 'deletion')) {
			$column = 'deletion';
		}

		$query->where($column, 0);
	}

	public function scopeWithOnlineStatus($query) {
		global $db;
		$query->when($db->hasTable('players_online'), function ($query) {
			$query->with('onlineTable');
		});
	}

	public function getOutfitUrlAttribute() {
		return setting('core.outfit_images_url') . '?id=' . $this->looktype . (!empty($this->lookaddons) ? '&addons=' . $this->lookaddons : '') . '&head=' . $this->lookhead . '&body=' . $this->lookbody . '&legs=' . $this->looklegs . '&feet=' . $this->lookfeet;
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
		return $this->hasOne(PlayerOnline::class);
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

	public function kills()
	{
		return $this->hasMany(PlayerKillers::class);
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
