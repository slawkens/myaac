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
		echo ' You can add new faq questions in phpmyadmin under ' . TABLE_PREFIX . 'faq table.';
}
?>
<table border="0" cellspacing="0" cellpadding="0" width="100%">
	<tr bgcolor="<?php echo $config['vdarkborder']; ?>">
		<td class="white">
			<b>FAQ</b>
		</td>
		<td align="right">
			<a href="#" onclick="toggleAll(); return false;">Toggle all</a>
		</td>
	</tr>
<?php
$i = 0;
foreach($faqs as $faq): ?>
	<tr bgcolor="<?php echo getStyle(++$i); ?>">
		<td colspan="2" style="cursor: pointer;" onclick="toggleVisibility('faq_<?php echo $i; ?>'); return false;">
			<b><?php echo $faq['question']; ?></b>
			<div id="faq_<?php echo $i; ?>" style="display: none;"><?php echo $faq['answer']; ?></div>
		</td>
	</tr>
<?php endforeach; ?>
</table>

<script type="text/javascript">
	var expanded = false;

	function toggleVisibility(id)
	{
		var tmp = document.getElementById(id);
		if(tmp)
			tmp.style.display = tmp.style.display == 'none' ? '' : 'none';
	}

	function toggleAll()
	{
		for(i = 1; i < <?php echo $i + 1; ?>; i++)
		{
			document.getElementById('faq_' + i).style.display = expanded ? 'none' : '';
		}

		expanded = !expanded;
	}
</script>
