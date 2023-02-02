<?php

trait OTS_DB_PDOQuery
{
	/**
	 * @return PDOStatement
	 */
	public function query(?string $query = null, ?int $fetchMode = null, mixed ...$fetchModeArgs)
	{
		return $this->doQuery($query, $fetchMode, ...$fetchModeArgs);
	}
}
