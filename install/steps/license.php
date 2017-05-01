<form action="<?php echo BASE_URL; ?>install/" method="post">
	<input type="hidden" name="step" id="step" value="requirements" />
	<textarea rows="10" cols="80" readonly="1"><?php echo file_get_contents(BASE . 'LICENSE'); ?></textarea>

	<?php echo next_buttons();
	?>
</form>
