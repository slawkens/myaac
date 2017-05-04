<?php
defined('MYAAC') or die('Direct access not allowed!');
if(isset($_POST['lang']))
{
	setcookie('locale', $_POST['lang']);
	$_COOKIE['locale'] = $_POST['lang'];
}

if(isset($_COOKIE['locale']))
{
	$locale_ = $_COOKIE['locale'];
	$lang_size = strlen($locale_);
	if(!$lang_size || $lang_size > 4 || !preg_match("/[a-z]/", $locale_)) // validate locale
		$_COOKIE['locale'] = "en";
}
else
{
	// detect locale
	$locale_s = get_browser_languages();
	if(!sizeof($locale_s))
		$locale_ = 'en';
	else
	{
		foreach($locale_s as $id => $tmp)
		{
			$tmp_file = LOCALE .  $tmp;
			if(@file_exists($tmp_file))
			{
				$locale_ = $tmp;
				break;
			}
		}
	}
	
	if(!isset($locale_))
		$locale_ = 'en';
}

require(LOCALE . 'en/main.php');
require(LOCALE . 'en/install.php');

$file_main = LOCALE . $locale_ . '/main.php';
if(!file_exists($file_main))
	$file_main = LOCALE . 'en/main.php';

$file_install = LOCALE . $locale_ . '/install.php';
if(!file_exists($file_install))
	$file_install = LOCALE . 'en/install.php';

require($file_main);
require($file_install);
?>