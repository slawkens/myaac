<?php

namespace MyAAC;

class Hook
{
	private $_name, $_type, $_file;

	public function __construct($name, $type, $file) {
		$this->_name = $name;
		$this->_type = $type;
		$this->_file = $file;
	}

	public function execute($params)
	{
		global $db, $config, $template_path, $ots, $content, $twig;

		if(is_callable($this->_file))
		{
			$params['db'] = $db;
			$params['config'] = $config;
			$params['template_path'] = $template_path;
			$params['ots'] = $ots;
			$params['content'] = $content;
			$params['twig'] = $twig;

			$tmp = $this->_file;
			$ret = $tmp($params);
		}
		else {
			extract($params);

			$ret = include BASE . $this->_file;
		}

		return !isset($ret) || $ret == 1 || $ret;
	}

	public function executeFilter(...$args) {
		return include BASE . $this->_file;
	}

	public function name() {return $this->_name;}
	public function type() {return $this->_type;}
	public function file() {return $this->_file;}
}
