<?php

namespace MyAAC\Models;
use Illuminate\Database\Eloquent\Model;

class Notepad extends Model {

	protected $table = TABLE_PREFIX . 'notepad';

	public $timestamps = false;

	protected $fillable = [
		'account_id', 'content'
	];

	public function account()
	{
		return $this->belongsTo(Account::class);
	}
}
