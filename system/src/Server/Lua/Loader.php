<?php

namespace MyAAC\Server\Lua;

class Loader
{
	public static function load($file): bool|array
	{
		if(!@file_exists($file)){
			return false;
		}

		$result = [];
		$config_string = str_replace(array("\r\n", "\r"), "\n", file_get_contents($file));
		$lines = explode("\n", $config_string);
		if(count($lines) > 0) {
			foreach($lines as $ln => $line) {
				$line = trim($line);
				if(isset($line[0]) && ($line[0] === '{' || $line[0] === '}')) {
					// arrays are not supported yet
					// just ignore the error
					continue;
				}

				$tmp_exp = explode('=', $line, 2);
				if(str_contains($line, 'dofile')) {
					$delimiter = '"';
					if(!str_contains($line, $delimiter)) {
						$delimiter = "'";
					}

					$tmp = explode($delimiter, $line);
					$result = array_merge($result, self::load(config('server_path') . $tmp[1]));
				}
				else if(count($tmp_exp) >= 2) {
					$key = trim($tmp_exp[0]);
					if(!str_starts_with($key, '--')) {
						$value = trim($tmp_exp[1]);
						if(str_contains($value, '--')) {// found some deep comment
							$value = preg_replace('/--.*$/i', '', $value);
						}

						if(is_numeric($value))
							$result[$key] = (float) $value;
						elseif(in_array(@$value[0], array("'", '"')) && in_array(@$value[strlen($value) - 1], array("'", '"')))
							$result[$key] = substr(substr($value, 1), 0, -1);
						elseif(in_array($value, array('true', 'false')))
							$result[$key] = $value === 'true';
						elseif(@$value[0] === '{') {
							// arrays are not supported yet
							// just ignore the error
							continue;
						}
						else
						{
							foreach($result as $tmp_key => $tmp_value) { // load values defined by other keys, like: dailyFragsToBlackSkull = dailyFragsToRedSkull
								$value = str_replace($tmp_key, $tmp_value, $value);
							}

							try {
								$ret = eval("return $value;");
							}
							catch (\Throwable $e) {
								throw new \RuntimeException('ERROR: Loading config.lua file. Line: ' . ($ln + 1) . ' - Unable to parse value "' . $value . '" - ' . $e->getMessage());
							}

							if((string) $ret == '' && trim($value) !== '""') {
								throw new \RuntimeException('ERROR: Loading config.lua file. Line ' . ($ln + 1) . ' is not valid [key: ' . $key . ']');
							}
							$result[$key] = $ret;
						}
					}
				}
			}
		}

		return $result;
	}
}
