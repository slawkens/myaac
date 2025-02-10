<?php

namespace MyAAC\Models;

use Illuminate\Database\Eloquent\Model;

class Config extends Model {

	protected $table = TABLE_PREFIX . 'config';

	public $timestamps = false;

	protected $fillable = ['name', 'value'];
}
