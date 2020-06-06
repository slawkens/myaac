<?php
/**
 * Menus
 *
 * @package   MyAAC
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2019 MyAAC
 * @link      https://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');
$title = 'Options';

$plugin = $_GET['plugin'];
if (!isset($plugin) || empty($plugin)) {
	error('Please select plugin name from left Panel.');
	return;
}

$pluginOptions = Plugins::getPluginOptions($plugin);
if (!$pluginOptions) {
	error('This plugin does not exist or does not have options defined.');
	return;
}

$message = '';
$optionsFile = require BASE . $pluginOptions;
if (!is_array($optionsFile)) {
	return;
}

$name = $optionsFile['name'];
$options = $optionsFile['options'];

if (isset($_POST['save'])) {
	foreach ($options as $key => $_config) {
		// TODO:
		// Save functionality
		// Check if exist, then INSERT or UPDATE

		/*$query = $db->query(
			sprintf('SELECT `value` FROM `%s` WHERE `name` = %s AND `key` = %s',
			TABLE_PREFIX . 'options_' . $table,
			$name,
			$key)
		);*/
	}
}

$optionsValues = [];
$optionsTypes = ['bool', 'double', 'int', 'text', 'varchar'];
foreach($optionsTypes as $type) {
	$query = 'SELECT `key`, `value` FROM `' . TABLE_PREFIX . 'options_' . $type . '` WHERE `name` = ' . $db->quote($name) . ';';
	$query = $db->query($query);

	$optionsValues = $optionsValues + $query->fetchAll();
}

//var_dump($optionsValues);
?>

<form method="post">
	<div class="row">
		<div class="col-md-12">
			<div class="box">
				<div class="box-header">
					<h3 class="box-title">Plugin Options - <?= $plugin ?></h3>
				</div>
				<div class="box-body">
					<?= $message ?>
					<button name="save" type="submit" class="btn btn-primary">Save</button>
				</div>
				<table class="table table-bordered table-striped">
					<thead>
					<tr>
						<th style="width: 10%">Key</th>
						<th style="width: 10%">Name</th>
						<th style="width: 30%">Value</th>
						<th>Description</th>
					</tr>
					</thead>
					<tbody>
					<?php

					foreach($options as $key => $_config) {
						?>
						<tr>
							<td><label for="<?= $key ?>" class="control-label"><?= $key ?></label></td>
							<td><label for="<?= $key ?>" class="control-label"><?= $_config['name'] ?></label></td>
							<td>
								<?php
								if ($_config['type'] === 'boolean') {
									$_config['type'] = 'options';
									$_config['options'] = ['true' => 'Yes', 'false' => 'No'];
								}

								else if (in_array($_config['type'], ['varchar', 'number'])) {
									echo '<input class="form-control" type="' . $_config['type'] . '" name="' . $key . '" value="' . (config($key) === null ? $_config['default'] : config($key)) . '" id="' . $key . '"/>';
								}

								else if($_config['type'] === 'textarea') {
									echo '<textarea class="form-control" name="' . $key . '" id="' . $key . '">' . config($key) . '</textarea>';
								}

								if ($_config['type'] === 'options') {
									echo '<select class="form-control" name="' . $key . '" id="' . $key . '">';
									foreach ($_config['options'] as $value => $option) {
										if($value === 'true') {
											$selected = config($key) === true;
										}
										else if($value === 'false') {
											$selected = config($key) === false;
										}
										else {
											$selected = config($key) == $value;
										}

										echo '<option value="' . $value . '" ' . ($selected ? 'selected' : '') . '>' . $option . '</option>';
									}
									echo '</select>';
								}
								?>
							</td>
							<td>
								<div class="well">
									<?= $_config['desc'] ?>
								</div>
							</td>
						</tr>
						<?php
					}
					?>
					</tbody>
				</table>
				<div class="box-footer">
					<button name="save" type="submit" class="btn btn-primary">Save</button>
				</div>
			</div>
		</div>
	</div>
</form>