<?php
/**
 * Notepad
 *
 * @package   MyAAC
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2019 MyAAC
 * @link      https://my-aac.org
 */

use MyAAC\Models\Notepad as ModelsNotepad;

defined('MYAAC') or die('Direct access not allowed!');
$title = 'Notepad';

csrfProtect();

$_content = '';
$notepad = ModelsNotepad::where('account_id', accountLogged()->getId())->first();
if (isset($_POST['content'])) {
	$_content = html_entity_decode(stripslashes($_POST['content']));
	if (!$notepad) {
		ModelsNotepad::create([
			'account_id' => accountLogged()->getId(),
			'content' => $_content
		]);
	}
	else {
		ModelsNotepad::where('account_id', accountLogged()->getId())->update(['content' => $_content]);
	}

	success('Saved at ' . date('H:i'));
} else {
	if ($notepad)
		$_content = $notepad->content;
}

$twig->display('admin.notepad.html.twig', ['content' => $_content]);
