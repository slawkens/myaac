<?php

namespace MyAAC\Models;

use Illuminate\Database\Eloquent\Model;

class AdminPermission extends Model {

	protected $table = TABLE_PREFIX . 'admin_permissions';

	protected $fillable = ['account_id', 'page', 'operations'];
}
