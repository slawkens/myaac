<?php

namespace MyAAC\Models;
use Illuminate\Database\Eloquent\Model;

class AccountBan extends Model {

	protected $table = TABLE_PREFIX . 'account_bans';

	public $timestamps = false;

	protected $fillable = [
		'account_id',
		'reason', 'banned_at',
		'expires_at', 'banned_by'
	];

}
