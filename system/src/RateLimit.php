<?php


namespace MyAAC;


class RateLimit
{
	public string $key;
	public int $max_attempts;
	public int $ttl;
	public $enabled = false;
	protected array $data = [];

	public function __construct(string $key, int $max_attempts, int $ttl)
	{
		$this->key = $key;
		$this->max_attempts = $max_attempts;
		$this->ttl = $ttl;
	}

	public function attempts(string $ip): int
	{
		if (!$this->enabled) {
			return 0;
		}

		if (isset($this->data[$ip]['attempts'])) {
			return $this->data[$ip]['attempts'];
		}

		return 0;
	}

	public function exceeded(string $ip): bool {
		if (!$this->enabled) {
			return false;
		}

		return $this->attempts($ip) >= $this->max_attempts;
	}

	public function increment(string $ip): bool
	{
		global $cache;
		if ($this->enabled && $cache->enabled()) {
			if (isset($this->data[$ip]['attempts']) && isset($this->data[$ip]['last'])) {
				$this->data[$ip]['attempts']++;
				$this->data[$ip]['last'] = time();
			} else {
				$this->data[$ip] = [
					'attempts' => 1,
					'last' => time(),
				];
			}

			$this->save();
		}

		return false;
	}

	public function reset(string $ip): void
	{
		if (!$this->enabled) {
			return;
		}

		if (isset($this->data[$ip])) {
			unset($this->data[$ip]);
		}

		$this->save();
	}

	public function save(): void
	{
		global $cache;
		if (!$this->enabled || !$cache->enabled()) {
			return;
		}

		$data = $this->data;
		$cache->set($this->key, serialize($data), $this->ttl * 60);
	}

	public function load(): void
	{
		global $cache;
		if (!$this->enabled) {
			return;
		}

		$data = [];
		if ($cache->enabled()) {
			$tmp = '';
			if ($cache->fetch($this->key, $tmp)) {
				$data = unserialize($tmp);
				$to_remove = [];
				foreach ($data as $ip => $t) {
					if (time() - $t['last'] >= ($this->ttl * 60)) {
						$to_remove[] = $ip;
					}
				}

				if (count($to_remove)) {
					foreach ($to_remove as $ip) {
						unset($data[$ip]);
					}

					$this->save();
				}
			}
		}

		$this->data = $data;
	}
}
