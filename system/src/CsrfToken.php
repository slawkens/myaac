<?php
/**
 * CsrfToken
 *
 * @package   MyAAC
 * @author    Znote
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2023 MyAAC
 * @link      https://my-aac.org
 */

namespace MyAAC;

class CsrfToken
{
	public static function generate(): void
	{
		$token = sha1(uniqid(time(), true));

		setSession('csrf_token', $token);
	}

	/**
	 * Displays a random token to prevent CSRF attacks.
	 *
	 * @access public
	 * @static true
	 * @return void
	 **/
	public static function create(): void {
		echo '<input type="hidden" name="csrf_token" value="' . self::get() . '" />';
	}

	/**
	 * Returns the active token, if there is one.
	 *
	 * @access public
	 * @static true
	 * @return mixed
	 **/
	public static function get(): mixed
	{
		$token = getSession('csrf_token');
		return $token ?? false;
	}

	/**
	 * Validates whether the active token is valid or not.
	 *
	 * @param string $post
	 * @access public
	 * @static true
	 * @return boolean
	 **/
	public static function isValid($post): bool
	{
		if (!setting('core.csrf_protection')) {
			return true;
		}

		// Token doesn't exist yet, return false.
		if (!self::get()) {
			return false;
		}

		return ($post == getSession('csrf_token'));
	}

	/**
	 * Destroys the active token.
	 *
	 * @access protected
	 * @static true
	 * @return void
	 **/
	protected static function reset(): void {
		unsetSession('csrf_token');
	}

	/**
	 * Displays information on both the post token and the session token.
	 *
	 * @param string $post
	 * @access public
	 * @static true
	 * @return void
	 **/
	public static function debug($post): void
	{
		echo '<pre>', var_export([
			'post' => $post,
			'token' => self::get()
		], true), '</pre>';
	}
}
