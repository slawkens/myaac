<?php
/**
 * FAQ
 *
 * @package   MyAAC
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2017 MyAAC
 * @version   0.4.2
 * @link      http://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');
$title = 'Frequently Asked Questions';

$faqs = $db->query('SELECT ' . $db->fieldName('question') . ', ' . $db->fieldName('answer') .
	' FROM ' . $db->tableName(TABLE_PREFIX . 'faq') . ' ORDER BY ' . $db->fieldName('ordering'));

if(!$faqs->rowCount())
{
?>
	There are no questions added yet.
<?php
	if(admin())
		echo ' You can add new faq questions in phpmyadmin under <b>' . TABLE_PREFIX . 'faq</b> table.';
}

echo $twig->render('faq.html.twig', array(
	'faqs' => $faqs
));
?>
