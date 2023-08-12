<?php

namespace MyAAC\Models;

use Illuminate\Database\Eloquent\Model;

class Weapon extends Model {

	protected $table = TABLE_PREFIX . 'weapons';

	public $timestamps = false;

	protected $fillable = ['id', 'level', 'maglevel', 'vocations'];

}
