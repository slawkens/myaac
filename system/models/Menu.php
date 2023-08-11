<?php

namespace MyAac\Models;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model {

	protected $table = TABLE_PREFIX . 'menu';

	public $timestamps = false;

	protected $guarded = ['id']; // lazy dev

}
