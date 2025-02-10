<?php

namespace MyAAC\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $type
 * @property int $where
 * @property string $body
 * @property int $player_id
 * @property int $date
 */
class Changelog extends Model {

	protected $table = TABLE_PREFIX . 'changelog';

	public $timestamps = false;

	public function scopeIsPublic($query) {
		$query->where('hide', '!=', 1);
	}
}
