<?php

namespace MyAac\Models;
use Illuminate\Database\Eloquent\Model;

class ServerRecord extends Model {

	protected $table = 'server_record';

	public $timestamps = false;

	protected $fillable = ['record', 'timestamp'];

}
