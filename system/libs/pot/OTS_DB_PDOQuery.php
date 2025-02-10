<?php

if (PHP_VERSION_ID >= 80000) {
	trait OTS_DB_PDOQuery
	{
		/**
		 * @return PDOStatement
		 */
		public function query(?string $query = null, ?int $fetchMode = null, mixed ...$fetchModeArgs): PDOStatement
		{
			return $this->doQuery($query, $fetchMode, ...$fetchModeArgs);
		}
	}
} else {
	trait OTS_DB_PDOQuery
	{
		/**
		 * @return PDOStatement
		 */
		public function query()
		{
			return $this->doQuery(...func_get_args());
		}
	}
}
