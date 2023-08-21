<?php

namespace MyAAC\Models;
use Illuminate\Database\Eloquent\Model;

class Settings extends Model {

	protected $table = TABLE_PREFIX . 'settings';

	public $timestamps = false;

	protected $fillable = ['name', 'key', 'value'];

}
