<?php

namespace MyAAC\Models;

use Illuminate\Database\Eloquent\Model;

class BoostedCreature extends Model {

	protected $table = 'boosted_creature';

	protected $casts = [
		'raceid' => 'integer',
	];

	public $timestamps = false;
}
