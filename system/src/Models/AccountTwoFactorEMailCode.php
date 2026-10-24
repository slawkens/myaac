<?php

namespace MyAAC\Models;
use Illuminate\Database\Eloquent\Model;

class AccountTwoFactorEMailCode extends Model {

	protected $table = TABLE_PREFIX . 'account_2fa_email_codes';

	protected $fillable = ['account_id', 'code'];

}
