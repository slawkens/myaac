<?php

namespace MyAAC\Models;

use Illuminate\Database\Eloquent\Model;

class Spell extends Model {

	protected $table = TABLE_PREFIX . 'spells';

	public $timestamps = false;

	protected $guarded = ['id']; // lazy dev

}
