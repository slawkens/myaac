<?php

namespace MyAAC\Models;
use Illuminate\Database\Eloquent\Model;

class ForumBoard extends Model {

	protected $table = TABLE_PREFIX . 'forum_boards';

	public $timestamps = false;

	protected $fillable = [
		'name', 'description', 'ordering',
		'guild', 'access', 'closed', 'hide',
	];
}
