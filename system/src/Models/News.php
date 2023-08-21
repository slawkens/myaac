<?php

namespace MyAAC\Models;
use Illuminate\Database\Eloquent\Model;

class News extends Model {

	protected $table = TABLE_PREFIX . 'news';

	public $timestamps = false;

	protected $fillable = [
		'title', 'body', 'type', 'date', 'category', 'player_id',
		'last_modified_by', 'last_modified_date', 'comments', 'article_text',
		'article_image', 'hidden'
	];

	public function player()
	{
		return $this->belongsTo(Player::class);
	}
}
