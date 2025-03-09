<?php

namespace MyAAC\Services;

use MyAAC\Cache\Cache;
use MyAAC\UsageStatistics;

/*
 * anonymous usage statistics
 * sent only when user agrees
 */
class AnonymousStatisticsService
{
	public function checkReport(): void
	{
		if(!setting('core.anonymous_usage_statistics')) {
			return;
		}

		$report_time = 30 * 24 * 60 * 60; // report one time per 30 days
		$should_report = true;

		$cache = app()->get('cache');

		$value = '';
		if($cache->enabled() && $cache->fetch('last_usage_report', $value)) {
			$should_report = time() > (int)$value + $report_time;
		}
		else {
			$value = '';
			if(fetchDatabaseConfig('last_usage_report', $value)) {
				$should_report = time() > (int)$value + $report_time;
				if($cache->enabled()) {
					$cache->set('last_usage_report', $value, 60 * 60);
				}
			}
			else {
				registerDatabaseConfig('last_usage_report', time() - ($report_time - (7 * 24 * 60 * 60))); // first report after a week
				$should_report = false;
			}
		}

		if($should_report) {
			UsageStatistics::report();

			updateDatabaseConfig('last_usage_report', time());
			if($cache->enabled()) {
				$cache->set('last_usage_report', time(), 60 * 60);
			}
		}
	}
}
