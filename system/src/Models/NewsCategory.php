<?php

namespace MyAAC\Models;
use Illuminate\Database\Eloquent\Model;

class NewsCategory extends Model {

	protected $table = TABLE_PREFIX . 'news_categories';

	public $timestamps = false;

	protected $fillable = [
		'name', 'description', 'icon_id', 'hide'
	];
}
