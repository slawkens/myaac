<?php

declare(strict_types=1);

namespace MyAAC\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

final class TypeCastingExtension extends AbstractExtension
{
	/** @return array<int, TwigFilter> */
	public function getFilters(): array
	{
		return [
			new TwigFilter('int', function ($value) {
				return (int)$value;
			}),
			new TwigFilter('float', function ($value) {
				return (float)$value;
			}),
			new TwigFilter('string', function ($value) {
				return (string)$value;
			}),
			new TwigFilter('bool', function ($value) {
				return (bool)$value;
			}),
			new TwigFilter('array', function (object $value) {
				return (array)$value;
			}),
			new TwigFilter('object', function (array $value) {
				return (object)$value;
			}),
		];
	}
}
