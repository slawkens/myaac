<?php

namespace MyAAC\Models;

use Illuminate\Database\Eloquent\Model;

class Monster extends Model {

	protected $table = TABLE_PREFIX . 'monsters';

	public $timestamps = false;

	protected $guarded = ['id']; // lazy dev

}
