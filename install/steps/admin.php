<?php
defined('MYAAC') or die('Direct access not allowed!');

?>
<form action="<?php echo BASE_URL; ?>install/" method="post" autocomplete="off">
	<input type="hidden" name="step" id="step" value="finish" />
	<table>
<?php
require(BASE . 'install/includes/config.php');
if(!$error) {
	require(BASE . 'install/includes/database.php');

	foreach(array(USE_ACCOUNT_NAME ? 'account' : 'account_id', 'password') as $value)
		echo '
	<tr>
		<td>
			<label for="vars_' . $value . '">
				<span>' . $locale['step_admin_' . $value] . '</span>
			</label>
			<br>
			<input type="text" name="vars[' . $value . ']" id="vars_' . $value . '"' . (isset($_SESSION['var_' . $value]) ? ' value="' . $_SESSION['var_' . $value] . '"' : '') . '/>
		</td>
		<td>
			<em>' . $locale['step_admin_' . $value . '_desc'] . '</em>
		</td>
	</tr>';
}
	?>
	</table>
	<?php echo next_buttons(true, $error ? false : true);
	?>
</form>