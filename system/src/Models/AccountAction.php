<?php

namespace MyAAC\Models;
use Illuminate\Database\Eloquent\Model;

class AccountAction extends Model {

	protected $table = TABLE_PREFIX . 'account_actions';

	public $timestamps = false;

	protected $fillable = ['account_id', 'ip', 'ipv6', 'date', 'action'];

}
