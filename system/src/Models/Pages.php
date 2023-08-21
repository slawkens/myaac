<?php

namespace MyAAC\Models;
use Illuminate\Database\Eloquent\Model;

class Pages extends Model {

	protected $table = TABLE_PREFIX . 'pages';

	public $timestamps = false;

	protected $fillable = ['name', 'title', 'body', 'date', 'player_id', 'php', 'enable_tinymce', 'access', 'hidden'];

	protected $casts = [
		'player_id' => 'integer',
		'enable_tinymce' => 'integer',
		'access' => 'integer',
		'hidden' => 'integer',
	];

	public function player()
	{
		return $this->belongsTo(Player::class);
	}

	public function scopeIsPublic($query) {
		$query->where('hidden', '!=', 1);
	}

}
