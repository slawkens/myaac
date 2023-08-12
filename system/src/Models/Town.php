<?php

namespace MyAAC\Models;

use Illuminate\Database\Eloquent\Model;

class Town extends Model {

	protected $table = 'towns';

	public $timestamps = false;

	protected $fillable = ['id', 'name', 'posx', 'posy', 'posz'];

}
