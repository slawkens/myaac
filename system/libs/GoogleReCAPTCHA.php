<?php

class GoogleReCAPTCHA
{
	private static $errorMessage = '';
	private static $errorType;

	const ERROR_MISSING_RESPONSE = 1;
	const ERROR_INVALID_ACTION = 2;
	const ERROR_LOW_SCORE = 3;
	const ERROR_NO_SUCCESS = 4;

	public static function verify($action = '')
	{
		if (!isset($_POST['g-recaptcha-response']) || empty($_POST['g-recaptcha-response'])) {
			self::$errorType = self::ERROR_MISSING_RESPONSE;
			self::$errorMessage = "Please confirm that you're not a robot.";
			return false;
		}

		$recaptchaApiUrl = 'https://www.google.com/recaptcha/api/siteverify';
		$secretKey = config('recaptcha_secret_key');

		$recaptchaResponse = $_POST['g-recaptcha-response'];
		$ip = $_SERVER['REMOTE_ADDR'];
		$params = 'secret='.$secretKey.'&response='.$recaptchaResponse.'&remoteip='.$ip;

		if (function_exists('curl_version')) {
			$curl_connection = curl_init($recaptchaApiUrl);

			curl_setopt($curl_connection, CURLOPT_CONNECTTIMEOUT, 5);
			curl_setopt($curl_connection, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl_connection, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($curl_connection, CURLOPT_FOLLOWLOCATION, 0);
			curl_setopt($curl_connection, CURLOPT_POSTFIELDS, $params);

			$response = curl_exec($curl_connection);
			curl_close($curl_connection);
		} else {
			$response = file_get_contents($recaptchaApiUrl . '?' . $params);
		}

		$json = json_decode($response);
		//log_append('recaptcha.log', 'recaptcha_score: ' . $json->score . ', action:' . $json->action);
		if (!isset($json->action) || $json->action !== $action) {
			self::$errorType = self::ERROR_INVALID_ACTION;
			self::$errorMessage = 'Google ReCaptcha returned invalid action.';
			return false;
		}

		if (!isset($json->score) || $json->score < config('recaptcha_min_score')) {
			self::$errorType = self::ERROR_LOW_SCORE;
			self::$errorMessage = 'Your Google ReCaptcha score was too low.';
			return false;
		}

		if (!isset($json->success) || !$json->success) {
			self::$errorType = self::ERROR_NO_SUCCESS;
			self::$errorMessage = "Please confirm that you're not a robot.";
			return false;
		}

		return true;
	}

	/**
	 * @return string
	 */
	public static function getErrorMessage() {
		return self::$errorMessage;
	}

	/**
	 * @return int
	 */
	public static function getErrorType() {
		return self::$errorType;
	}
}
