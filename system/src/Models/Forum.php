<?php

namespace MyAAC\Models;
use Illuminate\Database\Eloquent\Model;

class Forum extends Model
{
	protected $table = TABLE_PREFIX . 'forum';

	public $timestamps = false;

	protected $fillable = ['first_post', 'last_post', 'section', 'replies', 'views', 'author_aid', 'author_guid', 'post_text', 'post_topic', 'post_smile', 'post_html', 'post_date', 'last_edit_aid', 'edit_date', 'post_ip', 'sticked', 'closed'];

	protected $casts = [
		'first_post' => 'integer',
		'last_post' => 'integer',
		'section' => 'integer',
		'replies' => 'integer',
		'views' => 'integer',
		'author_aid' => 'integer',
		'author_guid' => 'integer',
		'post_smile' => 'boolean',
		'post_html' => 'boolean',
		'post_date' => 'integer',
		'last_edit_aid' => 'integer',
		'edit_date' => 'integer',
		'sticked' => 'boolean',
		'closed' => 'boolean'
	];
}
