<?php
if(isset($config['installed']) && $config['installed'])
	echo '<p class="warning">' . $locale['already_installed'] . '</p>';
else
{
?>
<form action="<?php echo BASE_URL; ?>install/" method="post">
	<input type="hidden" name="step" id="step" value="license" />
	<div class="input"><p><?php echo $locale['step_welcome_desc']; ?></p>
		<select name="lang">
			<?php
				foreach(get_locales() as $tmp_locale)
				{
					$lang_file_main = LOCALE . $tmp_locale . '/main.php';
					$lang_file_install = LOCALE . $tmp_locale . '/install.php';
					if(@file_exists($lang_file_main)
						&& @file_exists($lang_file_install))
					{
						require($lang_file_main);
						echo '<option value="' . $tmp_locale . '">' . $locale['name'] . '</option>';
					}
				}
			?>
		</select>
	</div>

	<?php echo next_buttons(false, true); ?>
</form>
<?php
}
?>