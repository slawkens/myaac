<?php

namespace MyAAC\Models;
use Illuminate\Database\Eloquent\Model;

class ServerConfig extends Model {

	protected $table = 'server_config';

	public $timestamps = false;

	protected $fillable = ['config', 'value'];

}
