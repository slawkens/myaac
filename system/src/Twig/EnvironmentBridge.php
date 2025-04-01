<?php

namespace MyAAC\Twig;

use Twig\Environment;

class EnvironmentBridge extends Environment
{
	public function display($name, array $context = []): void
	{
		$context['viewName'] = $name;

		$hooks = app()->get('hooks');
		$context = $hooks->triggerFilter(HOOK_FILTER_TWIG_DISPLAY, $context);

		parent::display($name, $context);
	}

	public function render($name, array $context = []): string
	{
		$context['viewName'] = $name;

		$hooks = app()->get('hooks');
		$context = $hooks->triggerFilter(HOOK_FILTER_TWIG_RENDER, $context);

		return parent::render($name, $context);
	}
}
