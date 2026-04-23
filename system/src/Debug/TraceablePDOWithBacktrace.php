<?php

namespace MyAAC\Debug;

use DebugBar\DataCollector\PDO\TraceablePDO;
use DebugBar\DataCollector\PDO\TracedStatement;

class TraceablePDOWithBacktrace extends TraceablePDO
{
	/** @var array[] */
	protected array $backtraces = [];

	public function addExecutedStatement(TracedStatement $stmt): void
	{
		$this->backtraces[] = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
		parent::addExecutedStatement($stmt);
	}

	/**
	 * @return array[]
	 */
	public function getBacktraces(): array
	{
		return $this->backtraces;
	}
}
