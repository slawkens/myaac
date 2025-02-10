<?php

namespace MyAAC\Models;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property string $title
 * @property int $php
 * @property int $hide
 */
class Pages extends Model {

	protected $table = TABLE_PREFIX . 'pages';

	public $timestamps = false;

	protected $fillable = ['name', 'title', 'body', 'date', 'player_id', 'php', 'enable_tinymce', 'access', 'hide'];

	protected $casts = [
		'player_id' => 'integer',
		'enable_tinymce' => 'integer',
		'access' => 'integer',
		'hide' => 'integer',
	];

	public function player()
	{
		return $this->belongsTo(Player::class);
	}

	public function scopeIsPublic($query) {
		$query->where('hide', '!=', 1);
	}

}
