<?php

namespace MyAAC\Models;

use Illuminate\Database\Eloquent\Model;

class Changelog extends Model {

	protected $table = TABLE_PREFIX . 'changelog';

	public $timestamps = false;

	public function scopeIsPublic($query) {
		$query->where('hidden', '!=', 1);
	}
}
