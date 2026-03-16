<?php

namespace MyAAC\Models;
use Illuminate\Database\Eloquent\Model;

class AccountEMailCode extends Model {

	protected $table = TABLE_PREFIX . 'account_email_codes';

	public $timestamps = false;

	protected $fillable = ['account_id', 'code', 'created_at'];

}
