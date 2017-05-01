<?php
$clients = array(
	710,
	740,
	750,
	760,
	770,
	780,
	7920,
	800,
	810,
	821,
	822,
	831,
	840,
	841,
	842,
	850,
	852,
	853,
	854,
	855,
	857,
	860,
	870,

	900,
	910,
	920,
	930,
	940,
	942,
	944,
	946,
	950,
	952,
	953,
	954,
	960,
	970,
	980,

	1000,
	1010,
	1021,
	1031,
	1034,
	1041,
	1050,
	1053,
	1054,
	1058,
	1075,
	1077,
	1079,
	1080,
	1090,
	1093,
	1094,
	1095,
	1096,
	1097,
	1098,
);

?>
<form action="<?php echo BASE_URL; ?>install/" method="post" autocomplete="off">
	<input type="hidden" name="step" id="step" value="database" />
	<table>
<?php
	foreach(array('server_path', 'account', 'password', 'mail_admin', 'mail_address') as $value)
		echo '
	<tr>
		<td>
			<label for="vars_' . $value . '">
				<span>' . $locale['step_config_' . $value] . '</span>
			</label>
			<br>
			<input type="text" name="vars[' . $value . ']" id="vars_' . $value . '"' . (isset($_SESSION['var_' . $value]) ? ' value="' . $_SESSION['var_' . $value] . '"' : '') . '/>
		</td>
		<td>
			<em>' . $locale['step_config_' . $value . '_desc'] . '</em>
		</td>
	</tr>';

echo '
	<tr>
		<td>
			<label for="vars_client">
				<span>' . $locale['step_config_client'] . '</span>
			</label>
			<br>
			<select name="vars[client]" id="vars_client">';
				//$i = 0;
				foreach($clients as $client) {
					$client_version = (string)($client / 100);
					if(strpos($client_version, '.') == false)
						$client_version .= '.0';
					echo '<option value="' . $client . '">' . $client_version . '</option>';
				}
			
		echo '
		</td>
		<td>
			<em>' . $locale['step_config_client_desc'] . '</em>
		</td>
	</tr>';
	?>
	</table>
<?php
	echo next_buttons(true, true);
?>
</form>