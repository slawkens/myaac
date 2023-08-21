<?php

namespace MyAAC\Models;

use Illuminate\Database\Eloquent\Model;

class Visitor extends Model {

	protected $table = TABLE_PREFIX . 'visitors';

	public $timestamps = false;

	protected $fillable = ['ip', 'lastivist', 'page', 'user_agent'];

}
