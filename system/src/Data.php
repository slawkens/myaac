<?php
/**
 * Data class
 *
 * @package   MyAAC
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2019 MyAAC
 * @link      https://my-aac.org
 */

namespace MyAAC;

class Data
{
	private $table = '';

	public function __construct($table) {
		$this->table = $table;
	}

	public function get($where)
	{
		$db = app()->get('database');
		return $db->select($this->table, $where);
	}

	public function add($data)
	{
		$db = app()->get('database');
		return $db->insert($this->table, $data);
	}

	public function delete($data, $where)
	{
		$db = app()->get('database');
		return $db->delete($this->table, $data, $where);
	}

	public function update($data, $where)
	{
		$db = app()->get('database');
		return $db->update($this->table, $data, $where);
	}
}
