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

$notepad_content = Notepad::get($account_logged->getId());
if (isset($_POST['content'])) {
	$_content = html_entity_decode(stripslashes($_POST['content']));
	if (!$notepad_content)
		Notepad::create($account_logged->getId(), $_content);
	else
		Notepad::update($account_logged->getId(), $_content);

	echo '<div class="success" style="text-align: center;">Saved at ' . date('H:i') . '</div>';
} else {
	if ($notepad_content !== false)
		$_content = $notepad_content;
}

$twig->display('admin.notepad.html.twig', array('content' => isset($_content) ? $_content : null));

class Notepad
{
	static public function get($account_id)
	{
		$row = ModelsNotepad::where('account_id', $account_id)->first('content');
		if ($row) {
			return $row->content;
		}

		return false;
	}

	static public function create($account_id, $content = '')
	{
		ModelsNotepad::create([
			'account_id' => $account_id,
			'content' => $content
		]);
	}

	static public function update($account_id, $content = '')
	{
		ModelsNotepad::where('account_id', $account_id)->update(['content' => $content]);
	}
}
