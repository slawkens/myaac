<?php
/**
 * Events system
 *
 * @package   MyAAC
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2017 MyAAC
 * @version   0.0.6
 * @link      http://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');
class Event
{
	private $_name, $_type, $_callback;

	public function __construct($name, $type, $callback) {
		$this->_name = $name;
		$this->_type = $type;
		$this->_callback = $callback;
	}

	public function execute($params)
	{
		$ret = false;
		if(is_callable($this->_callback))
		{
			$tmp = $this->_callback;
			$ret = $tmp($params);
		}

		return $ret;
	}

	public function name() {return $this->_name;}
	public function type() {return $this->_type;}
}

class Events
{
	private static $_events = array();

	public function register($event, $type = '', $callback = null) {
		if(!($event instanceof Event))
			$event = new Event($event, $type, $callback);

		self::$_events[$event->type()][] = $event;
	}

	public function trigger($type, $params = array())
	{
		$ret = true;
		if(isset(self::$_events[$type]))
		{
			foreach(self::$_events[$type] as $name => $event)
				$ret = $event->execute($params);
		}

		return $ret;
	}
}
?>
