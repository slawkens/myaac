<?php
/**
 * FAQ
 *
 * @package   MyAAC
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2019 MyAAC
 * @link      https://my-aac.org
 */

use MyAAC\Models\FAQ as ModelsFAQ;

defined('MYAAC') or die('Direct access not allowed!');
$title = 'Frequently Asked Questions';

$canEdit = hasFlag(FLAG_CONTENT_FAQ) || superAdmin();
if($canEdit)
{
	if(!empty($action))
	{
		if($action == 'delete' || $action == 'edit' || $action == 'hide' || $action == 'moveup' || $action == 'movedown')
			$id = $_REQUEST['id'];

		if(isset($_REQUEST['question']))
			$question = $_REQUEST['question'];

		if(isset($_REQUEST['answer']))
			$answer = stripslashes($_REQUEST['answer']);

		$errors = array();

		if($action == 'add') {
			if(FAQ::add($question, $answer, $errors))
				$question = $answer = '';
		}
		else if($action == 'delete') {
			FAQ::delete($id, $errors);
		}
		else if($action == 'edit')
		{
			if(isset($id) && !isset($question)) {
				$faq = FAQ::get($id);
				$question = $faq['question'];
				$answer = $faq['answer'];
			}
			else {
				FAQ::update($id, $question, $answer);
				$action = $question = $answer = '';
			}
		}
		else if($action == 'hide') {
			FAQ::toggleHidden($id, $errors);
		}
		else if($action == 'moveup') {
			FAQ::move($id, -1, $errors);
		}
		else if($action == 'movedown') {
			FAQ::move($id, 1, $errors);
		}

		if(!empty($errors))
			$twig->display('error_box.html.twig', array('errors' => $errors));
	}

	$twig->display('faq.form.html.twig', array(
		'link' => getLink('faq/' . ($action == 'edit' ? 'edit' : 'add')),
		'action' => $action,
		'id' => isset($id) ? $id : null,
		'question' => isset($question) ? $question : null,
		'answer' => isset($answer) ? $answer : null
	));
}

$faqs = ModelsFAQ::select('id', 'question', 'answer')->when(!$canEdit, function ($query) {
	$query->where('hidden', '!=', 1);
})->orderBy('ordering');

if ($canEdit) {
	$faqs->addSelect(['hidden', 'ordering']);
}

$faqs = $faqs->get()->toArray();
if(!count($faqs))
{
	?>
	There are no questions added yet.
	<?php
}

$last = count($faqs);
$twig->display('faq.html.twig', array(
	'faqs' => $faqs,
	'last' => $last,
	'canEdit' => $canEdit
));

class FAQ
{
	static public function add($question, $answer, &$errors)
	{
		if(isset($question[0]) && isset($answer[0]))
		{
			$row = ModelsFAQ::where('question', $question)->first();
			if(!$row)
			{
				$ordering = ModelsFAQ::max('ordering') ?? 0;
				ModelsFAQ::create([
					'question' => $question,
					'answer' => $answer,
					'ordering' => $ordering
				]);
			}
			else
				$errors[] = 'FAQ with this question already exists.';
		}
		else
			$errors[] = 'Please fill all inputs.';

		return !count($errors);
	}

	static public function get($id) {
		return ModelsFAQ::find($id)->toArray();
	}

	static public function update($id, $question, $answer) {
		ModelsFAQ::where('id', $id)->update([
			'question' => $question,
			'answer' => $answer
		]);
	}

	static public function delete($id, &$errors)
	{
		if(isset($id))
		{
			$row = ModelsFAQ::find($id);
			if($row)
				$row->delete();
			else
				$errors[] = 'FAQ with id ' . $id . ' does not exists.';
		}
		else
			$errors[] = 'id not set';

		return !count($errors);
	}

	static public function toggleHidden($id, &$errors)
	{
		if(isset($id))
		{
			$row = ModelsFAQ::find($id);
			if ($row) {
				$row->hidden = ($row->hidden == 1 ? 0 : 1);
				if (!$row->save()) {
					$errors[] = 'Fail during toggle hidden FAQ.';
				}
			} else {
				$errors[] = 'FAQ with id ' . $id . ' does not exists.';
			}
		}
		else
			$errors[] = 'id not set';

		return !count($errors);
	}

	static public function move($id, $i, &$errors)
	{
		global $db;
		$row = ModelsFAQ::find($id);
		if($row)
		{
			$ordering = $row->ordering + $i;
			$old_record = ModelsFAQ::where('ordering', $ordering)->first();
			if($old_record) {
				$old_record->ordering = $row->ordering;
				$old_record->save();
			}

			$row->ordering = $ordering;
			$row->save();
		}
		else
			$errors[] = 'FAQ with id ' . $id . ' does not exists.';

		return !count($errors);
	}
}
