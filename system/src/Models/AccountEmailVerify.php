<?php

namespace MyAAC\Models;
use Illuminate\Database\Eloquent\Model;

class AccountEmailVerify extends Model
{

	protected $table = TABLE_PREFIX . 'account_emails_verify';

	public $timestamps = false;

	protected $fillable = ['account_id', 'hash', 'sent_at'];

}
