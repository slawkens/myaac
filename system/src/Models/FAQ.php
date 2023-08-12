<?php

namespace MyAAC\Models;

use Illuminate\Database\Eloquent\Model;

class FAQ extends Model {

	protected $table = TABLE_PREFIX . 'faq';

	public $timestamps = false;

	protected $fillable = ['question', 'answer', 'ordering', 'hidden'];
}
