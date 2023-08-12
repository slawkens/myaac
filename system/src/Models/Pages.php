<?php

namespace MyAAC\Models;
use Illuminate\Database\Eloquent\Model;

class Pages extends Model {

	protected $table = TABLE_PREFIX . 'pages';

	public $timestamps = false;

	public function scopeIsPublic($query) {
		$query->where('hidden', '!=', 1);
	}

}
