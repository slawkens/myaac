<?php
/**
 * @deprecated
 * This class is deprecated and will be removed in future versions. Please use the appropriate MyAAC\Server\Items class instead.
 */
namespace MyAAC;

class Items extends Server\Items
{
	public static function load(): bool {
		parent::init();
		return true;
	}
}
