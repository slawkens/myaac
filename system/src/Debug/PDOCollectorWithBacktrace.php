<?php

namespace MyAAC\Debug;

use DebugBar\DataCollector\PDO\PDOCollector;
use DebugBar\DataCollector\PDO\TraceablePDO;
use DebugBar\DataCollector\TimeDataCollector;

class PDOCollectorWithBacktrace extends PDOCollector
{
	protected function collectPDO(TraceablePDO $pdo, ?TimeDataCollector $timeCollector = null, $connectionName = null): array
	{
		$data = parent::collectPDO($pdo, $timeCollector, $connectionName);

		if ($pdo instanceof TraceablePDOWithBacktrace) {
			$backtraces = $pdo->getBacktraces();
			foreach ($data['statements'] as $i => &$stmt) {
				if (isset($backtraces[$i])) {
					$stmt['backtrace'] = $this->formatBacktrace($backtraces[$i]);
				}
			}
		}

		return $data;
	}

	private function formatBacktrace(array $backtrace): array
	{
		$result = [];
		foreach ($backtrace as $frame) {
			if (!isset($frame['file'], $frame['line'])) {
				continue;
			}

			if (str_contains($frame['file'], DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR)) {
				continue;
			}

			if (str_contains($frame['file'], DIRECTORY_SEPARATOR . 'Debug' . DIRECTORY_SEPARATOR)) {
				continue;
			}

			$function = isset($frame['class'])
				? $frame['class'] . ($frame['type'] ?? '::') . ($frame['function'] ?? '')
				: ($frame['function'] ?? '');

			$result[] = ($function ? $function . '() ' : '') . $frame['file'] . ':' . $frame['line'];
		}
		return $result;
	}
}
