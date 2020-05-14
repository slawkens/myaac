<?php
/**
 * Tools
 *
 * @package   MyAAC
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2019 MyAAC
 * @link      https://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');
$title = 'Config Editor';

require_once SYSTEM . 'clients.conf.php';

$message = '';
$config_options = array(
	'server_path' => array(
		'name' => 'Server Path',
		'type' => 'text',
		'desc' => 'path to the server directory (same directory where config file is located)'
	),
	'env' => array(
		'name' => 'Environment',
		'type' => 'options',
		'options' => array('prod' => 'Production', 'dev' => 'Development'),
		'desc' => 'if you use this script on your live server - set to <i>Production</i><br/>
if you want to test and debug the script locally, or develop plugins, set to <i>Development</i><br/>
<strong>WARNING</strong>: on <i>Development</i> cache is disabled, so site will be significantly slower !!!<br/>
<strong>Recommended</strong>: <i>Production</i> cause of speed (page load time is better)'
	),
	'template' => array(
		'name' => 'Template Name',
		'type' => 'options',
		'options' => '$templates',
		'desc' => 'Name of the template used by website'
	),
	'template_allow_change' => array(
		'name' => 'Template Allow Change',
		'type' => 'boolean',
		'desc' => 'Allow changing template of the website by showing a special select in the part of website'
	),
	'vocations_amount' => array(
		'name' => 'Amount of Vocations',
		'type' => 'number',
		'desc' => 'how much basic vocations your server got (without promotion)'
	),
	'client' => array(
		'name' => 'Client Version',
		'type' => 'options',
		'options' => '$clients',
		'desc' => 'what client version are you using on this OT?<br/>
used for the Downloads page and some templates aswell'
	),
	'session_prefix' => array(
		'name' => 'Session Prefix',
		'type' => 'text',
		'desc' => 'must be unique for every site on your server',
	),
	'friendly_urls' => array(
		'name' => 'Friendly URLs',
		'type' => 'boolean',
		'desc' => 'mod_rewrite is required for this, it makes links looks more elegant to eye, and also are SEO friendly (example: https://my-aac.org/guilds/Testing instead of https://my-aac.org/?subtopic=guilds&name=Testing).<br/><strong>Remember to rename .htaccess.dist to .htaccess</strong>'
	),
	'gzip_output' => array(
		'name' => 'GZIP Output',
		'type' => 'boolean',
		'desc' => 'gzip page content before sending it to the browser, uses less bandwidth but more cpu cycles'
	),
	'backward_support' => array(
		'name' => 'Gesior Backward Support',
		'type' => 'boolean',
		'desc' => 'gesior backward support (templates & pages)<br/>
allows using gesior templates and pages with myaac<br/>
might bring some performance when disabled'
	),
	'meta_description' => array(
		'name' => 'Meta Description',
		'type' => 'textarea',
		'desc' => 'description of the site in <meta>'
	),
	'meta_keywords' => array(
		'name' => 'Meta Keywords',
		'type' => 'textarea',
		'desc' => 'keywords list separated by commas'
	),
	'title_separator' => array(
		'name' => 'Title Separator',
		'type' => 'text',
		'desc' => 'Separator used in the title of the website'
	),
	'footer' => array(
		'name' => 'Footer',
		'type' => 'textarea',
		'desc' => 'For example: "<br/>Your Server &copy; 2016. All rights reserved."'
	),
	'language' => array(
		'name' => 'Language',
		'type' => 'options',
		'options' => array('en' => 'English'),
		'desc' => 'default language (currently only English available)'
	),
	'visitors_counter' => array(
		'name' => 'Visitors Counter',
		'type' => 'boolean',
		'desc' => 'Enable Visitors Counter? It will show list of online members on the website in Admin Panel'
	),
	'visitors_counter_ttl' => array(
		'name' => 'Visitors Counter TTL',
		'type' => 'number',
		'desc' => 'Time To Live for Visitors Counter. In other words - how long user will be marked as online. In Minutes'
	),
	'views_counter' => array(
		'name' => 'Views Counter',
		'type' => 'boolean',
		'desc' => 'Enable Views Counter? It will show how many times the website has been viewed by users'
	),
	'cache_engine' => array(
		'name' => 'Cache Engine',
		'type' => 'text',
		'desc' => 'cache system. by default file cache is used.<br/>
Other available options: apc, apcu, eaccelerator, xcache, file, auto, or blank to disable'
	),
	'cache_prefix' => array(
		'name' => 'Cache Prefix',
		'type' => 'text',
		'desc' => 'have to be unique if running more MyAAC instances on the same server (except file system cache)'
	),
	'database_log' => array(
		'name' => 'Database Log',
		'type' => 'boolean',
		'desc' => 'Should database queries be logged and displayed in the page source? They will be included at the end of the .html source of the page, only for Super Admin'
	),
	'database_socket' => array(
		'name' => 'Database Socket',
		'type' => 'text',
		'desc' => 'Set if you want to connect to database through socket (example: /var/run/mysqld/mysqld.sock)'
	),
	'database_persistent' => array(
		'name' => 'Database Persistent',
		'type' => 'boolean',
		'desc' => 'Use database permanent connection (like server), may speed up your site'
	),
	'outfit_images_url' => array(
		'name' => 'Outfit Images URL',
		'type' => 'text',
		'desc' => 'Set to animoutfit.php for animated outfit'
	),
	'item_images_url' => array(
		'name' => 'Item Images URL',
		'type' => 'text',
		'desc' => 'Set to images/items if you host your own items in images folder'
	),
);

if (isset($_POST['save'])) {
	$content = '<?php' . PHP_EOL . PHP_EOL .
		'// place for your configuration directives, so you can later easily update myaac' . PHP_EOL . PHP_EOL .
		"\$config['installed'] = true;";

	foreach($config_options as $key => $_config) {
		$content .= PHP_EOL . "\$config['$key'] = ";
		if (in_array($_config['type'], array('boolean', 'number'))) {
			$content .= $_POST[$key];
		}

		else if (in_array($_config['type'], array('text', 'textarea'))) {
			$content .= "'" . $_POST[$key] . "'";
		}

		else if($_config['type'] === 'options') {
			if(is_numeric($_POST[$key])) {
				$content .= $_POST[$key];
			}
			else {
				$content .= "'" . $_POST[$key] . "'";
			}
		}

		$content .= ';';
	}

	//$saved = file_put_contents(BASE . 'config.local.php', $content);
	$saved = false;
	ob_start();
	if($saved) {
		?>
		<div class="alert alert-success">
			Config has been successfully saved.
		</div>
		<?php
	}
	else {
		?>
		<div class="alert alert-error">
			<?= BASE ?>config.local.php couldn't be opened. Please copy this content and paste there:
			<br/>
			<textarea class="form-control" cols="70" rows="10"><?= $content ?></textarea>
		</div>
		<?php
	}

	$message = ob_get_clean();
}
?>

<form method="post">
	<div class="row">
		<div class="col-md-12">
			<div class="box">
				<div class="box-header">
					<h3 class="box-title">Configuration Editor</h3>
				</div>
				<div class="box-body">
					<?= $message ?>
					<button name="save" type="submit" class="btn btn-primary">Save</button>
				</div>
				<table class="table table-bordered table-striped">
					<thead>
					<tr>
						<th width="10%">Key</th>
						<th width="10%">Name</th>
						<th width="30%">Value</th>
						<th>Description</th>
					</tr>
					</thead>
					<tbody>
					<?php

					foreach($config_options as $key => $_config) {
						?>
						<tr>
							<td><label for="<?= $key ?>" class="control-label"><?= $key ?></label></td>
							<td><label for="<?= $key ?>" class="control-label"><?= $_config['name'] ?></label></td>
							<td>
								<?php
								if ($_config['type'] === 'boolean') {
									$_config['type'] = 'options';
									$_config['options'] = array('true' => 'Yes', 'false' => 'No');
								}

								else if (in_array($_config['type'], array('text', 'number'))) {
									echo '<input class="form-control" type="' . $_config['type'] . '" name="' . $key . '" value="' . config($key) . '" id="' . $key . '"/>';
								}

								else if($_config['type'] === 'textarea') {
									echo '<textarea class="form-control" name="' . $key . '" id="' . $key . '">' . config($key) . '</textarea>';
								}

								if ($_config['type'] === 'options') {
									if ($_config['options'] === '$templates') {
										$templates = array();
										foreach (get_templates() as $value) {
											$templates[$value] = $value;
										}

										$_config['options'] = $templates;
									}

									else if($_config['options'] === '$clients') {

										$clients = array();
										foreach((array)config('clients') as $client) {

											$client_version = (string)($client / 100);
											if(strpos($client_version, '.') === false)
												$client_version .= '.0';

											$clients[$client] = $client_version;
										}

										$_config['options'] = $clients;
									}

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