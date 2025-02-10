<?php
/**
 * Compat classes (backward support for Gesior AAC)
 *
 * @package   MyAAC
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2022 MyAAC
 * @link      https://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');

class Account extends OTS_Account {
	public function loadById($id) {
		$this->load($id);
	}
	public function loadByName($name) {
		$this->find($name);
	}
}

class Player extends OTS_Player {
	public function loadById($id) {
		$this->load($id);
	}
	public function loadByName($name) {
		$this->find($name);
	}
}
class Guild extends OTS_Guild {
	public function loadById($id) {
		$this->load($id);
	}
	public function loadByName($name) {
		$this->find($name);
	}
}
class GuildRank extends OTS_GuildRank {}
class House extends OTS_House {}

class Cache extends \MyAAC\Cache\Cache {}
