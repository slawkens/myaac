<?php
/**
 * Visitors class
 *
 * @package   MyAAC
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2019 MyAAC
 * @link      https://my-aac.org
 */

use MyAAC\Models\Visitor;

defined('MYAAC') or die('Direct access not allowed!');

class Visitors
{
	private $sessionTime; // time session will live
	private $data; // cached data
	private $cacheEnabled;
	private $cache;

	public function __construct($sessionTime = 10)
	{
		$this->cache = Cache::getInstance();

		$this->cacheEnabled = $this->cache->enabled();
		if($this->cacheEnabled)
		{
			$tmp = '';
			if($this->cache->fetch('visitors', $tmp))
				$this->data = unserialize($tmp);
			else
				$this->data = array();
		}

		$this->sessionTime = $sessionTime;
		$this->cleanVisitors();

		$ip = $_SERVER['REMOTE_ADDR'];
		$userAgentShortened = substr($_SERVER['HTTP_USER_AGENT'] ?? 'unknown', 0, 255);

		if($this->visitorExists($ip))
			$this->updateVisitor($ip, $_SERVER['REQUEST_URI'], $userAgentShortened);
		else
			$this->addVisitor($ip, $_SERVER['REQUEST_URI'], $userAgentShortened);
	}

	public function __destruct()
	{
		if($this->cacheEnabled)
			$this->cache->set('visitors', serialize($this->data), 120);
	}

	public function visitorExists($ip)
	{
		if($this->cacheEnabled) {
			return isset($this->data[$ip]);
		}

		return Visitor::where('ip', $ip)->exists();
	}

	private function cleanVisitors()
	{
		if($this->cacheEnabled)
		{
			$timeNow = time();
			foreach($this->data as $ip => $details)
			{
				if($timeNow - (int)$details['lastvisit'] > $this->sessionTime * 60)
					unset($this->data[$ip]);
			}

			return;
		}

		Visitor::where('lastvisit', '<', (time() - $this->sessionTime * 60))->delete();
	}

	private function updateVisitor($ip, $page, $userAgent)
	{
		if($this->cacheEnabled) {
			$this->data[$ip] = array('page' => $page, 'lastvisit' => time(), 'user_agent' => $userAgent);
			return;
		}

		Visitor::where('ip', $ip)->update(['lastvisit' => time(), 'page' => $page, 'user_agent' => $userAgent]);
	}

	private function addVisitor($ip, $page, $userAgent)
	{
		if($this->cacheEnabled) {
			$this->data[$ip] = array('page' => $page, 'lastvisit' => time(), 'user_agent' => $userAgent);
			return;
		}

		Visitor::create(['ip' => $ip, 'lastvisit' => time(), 'page' => $page, 'user_agent' => $userAgent]);
	}

	public function getVisitors()
	{
		if($this->cacheEnabled) {
			foreach($this->data as $ip => &$details)
				$details['ip'] = $ip;

			return $this->data;
		}

		return Visitor::orderByDesc('lastvisit')->get()->toArray();
	}

	public function getAmountVisitors()
	{
		if($this->cacheEnabled) {
			return count($this->data);
		}

		return Visitor::count();
	}

	public function show() {
		echo $this->getAmountVisitors();
	}
}
?>
