<?php

namespace MyAAC\Models;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model {

	protected $table = TABLE_PREFIX . 'menu';

	public $timestamps = false;

	protected $fillable = ['template', 'name', 'link', 'blank', 'color', 'category', 'ordering', 'enabled'];

}
