<?php

namespace MyAAC\Twig;

use Twig\Environment;
use Twig\Loader\ArrayLoader as Twig_ArrayLoader;

class EnvironmentBridge extends Environment
{
	public function display($name, array $context = []): void
	{
		global $hooks;

		$context['viewName'] = $name;
		$hooks->triggerFilter(HOOK_FILTER_TWIG_DISPLAY, $context);

		parent::display($name, $context);
	}

	public function render($name, array $context = []): string
	{
		global $hooks;

		$context['viewName'] = $name;
		$hooks->triggerFilter(HOOK_FILTER_TWIG_RENDER, $context);

		return parent::render($name, $context);
	}

	public function renderInline($content, array $context = []): string
	{
		$oldLoader = $this->getLoader();

		$twig_loader_array = new Twig_ArrayLoader(array(
			'content.html' => $content
		));

		$this->setLoader($twig_loader_array);

		$ret = $this->render('content.html', $context);

		$this->setLoader($oldLoader);

		return $ret;
	}
}
