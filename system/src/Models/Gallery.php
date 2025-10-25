<?php

namespace MyAAC\Models;

use Illuminate\Database\Eloquent\Model;

class Gallery extends Model {

	protected $table = TABLE_PREFIX . 'gallery';

	public $timestamps = false;

	protected $fillable = [
		'comment', 'image', 'thumb',
		'author', 'ordering', 'hide',
	];

}
