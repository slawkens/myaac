<?php

namespace MyAac\Models;
use Illuminate\Database\Eloquent\Model;

class Pages extends Model {

	protected $table = TABLE_PREFIX . 'pages';

	public function scopeIsPublic($query) {
		$query->where('hidden', '!=', 1);
	}

}
