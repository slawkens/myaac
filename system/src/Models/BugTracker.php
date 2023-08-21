<?php

namespace MyAAC\Models;

use Illuminate\Database\Eloquent\Model;

class BugTracker extends Model {

	protected $table = TABLE_PREFIX . 'bugtracker';

	public $timestamps = false;

	protected $fillable = ['account', 'type', 'status', 'text', 'id', 'subject', 'reply', 'who', 'uid', 'tag'];

}
