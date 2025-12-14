<?php

namespace MyAAC\Admin;

use GuzzleHttp\Client;

class Plugins
{
	private string $api_base_uri = 'https://plugins.my-aac.org/api/';

	public function getLatestVersions(): array
	{
		$client = new Client([
			// Base URI is used with relative requests
			'base_uri' => $this->api_base_uri,
			// You can set any number of default request options.
			'timeout'  => 3.0,
		]);

		$plugins = get_plugins(true);
		foreach ($plugins as &$plugin) {
			if (str_contains($plugin, 'disabled.')) {
				$plugin = str_replace('disabled.', '', $plugin);
			}
		}

		try {
			$response = $client->get('get-latest-versions', [
				'json' => ['plugins' => $plugins],
			]);
		}
		catch (\Exception $e) {
			error('API Error. Please try again later.');
			return [];
		}

		$statusCode = $response->getStatusCode();
		if ($statusCode != 200) {
			throw new \Exception('Error getting info from plugins repository. Please try again later.');
		}

		$data = $response->getBody();
		return json_decode($data, true);
	}

	public function setApiBaseUri(string $uri): void {
		$this->api_base_uri = $uri;
	}
}
