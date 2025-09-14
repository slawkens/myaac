<?php
/**
 * Menus
 *
 * @package   MyAAC
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2019 MyAAC
 * @link      https://my-aac.org
 */

use MyAAC\Cache\Cache;
use MyAAC\Models\Menu;
use MyAAC\Plugins;

defined('MYAAC') or die('Direct access not allowed!');
$title = 'Menus';

csrfProtect();

if (!hasFlag(FLAG_CONTENT_MENUS) && !superAdmin()) {
	echo 'Access denied.';
	return;
}

$pluginThemes = Plugins::getThemes();

if (isset($_POST['template'])) {
	$template = $_POST['template'];

	if (isset($_POST['save'])) {
		$post_menu = $_POST['menu'] ?? [];
		$post_menu_link = $_POST['menu_link'] ?? [];
		$post_menu_blank = $_POST['menu_blank'] ?? [];
		$post_menu_color = $_POST['menu_color'] ?? [];
		if (count($post_menu) != count($post_menu_link)) {
			echo 'Menu count is not equal menu links. Something went wrong when sending form.';
			return;
		}

		Menu::where('template', $template)->delete();
		foreach ($post_menu as $category => $menus) {
			foreach ($menus as $i => $menu) {
				if (empty($menu)) // don't save empty menu item
					continue;

				try {
					Menu::create([
						'template' => $template,
						'name' => $menu,
						'link' => $post_menu_link[$category][$i],
						'blank' => $post_menu_blank[$category][$i] == 'on' ? 1 : 0,
						'color' => str_replace('#', '', $post_menu_color[$category][$i]),
						'category' => $category,
						'ordering' => $i
					]);
				} catch (PDOException $error) {
					warning('Error while adding menu item (' . $menu . '): ' . $error->getMessage());
				}
			}
		}

		onTemplateMenusChange();
		success('Saved at ' . date('H:i'));
	}

	$path = TEMPLATES . $template;

	if (isset($pluginThemes[$template])) {
		$path = BASE . $pluginThemes[$template];
	}

	$path .= '/config.php';

	if (file_exists($path)) {
		require_once $path;
	} else {
		echo 'Cannot find template config.php file.';
		return;
	}

	if (!isset($config['menu_categories'])) {
		echo "No menu categories set in template config.php.<br/>This template doesn't support dynamic menus.";
		return;
	}

	if (isset($_GET['reset_colors'])) {
		foreach ($config['menu_categories'] as $id => $options) {
			$color = $options['default_links_color'] ?? ($config['menu_default_links_color'] ?? ($config['menu_default_color'] ?? '#ffffff'));
			Menu::where('template', $template)->where('category', $id)->update(['color' => str_replace('#', '', $color)]);
		}

		onTemplateMenusChange();
		success('Colors has been reset at ' . date('H:i'));
	}

	if (isset($_GET['reset_menus'])) {
		$configMenus = config('menus');
		if (isset($configMenus)) {
			Plugins::installMenus($template, config('menus'), true);

			onTemplateMenusChange();
			success('Menus has been reset at ' . date('H:i'));
		}
		else {
			error("This template don't support reinstalling menus.");
		}
	}

	$title = 'Menus - ' . $template;

	$canResetColors = isset($config['menu_default_color']) || isset($config['menu_default_links_color']);
	foreach ($config['menu_categories'] as $id => $options) {
		if (isset($options['default_links_color'])) {
			$canResetColors = true;
		}
	}

	$twig->display('admin.menus.header.html.twig', [
		'template' => $template,
		'canResetColors' => $canResetColors
	]);
	?>
	<?php
	$menus = Menu::query()
		->select('name', 'link', 'blank', 'color', 'category', 'ordering')
		->where('enabled', 1)
		->where('template', $template)
		->orderBy('ordering')
		->get()
		->groupBy('category')
		->toArray();

	$last_id = array();
	?>
	<form method="post" id="menus-form" action="?p=menus">
		<?php csrf(); ?>
		<input type="hidden" name="template" value="<?php echo $template ?>"/>
		<button type="submit" name="save" class="btn btn-info">Save</button><br/><br/>
		<div class="row">
			<?php foreach ($config['menu_categories'] as $id => $cat): ?>
				<div class="col-md-12 col-lg-6">
					<div class="card card-info card-outline">
						<div class="card-header">
							<h5 class="m-0"><?php echo $cat['name'] ?> <i class="far fa-plus-square add-button" id="add-button-<?php echo $id ?>"></i></h5>
						</div>
						<div class="card-body">
							<ul class="sortable" id="sortable-<?php echo $id ?>">
								<?php
								if (isset($menus[$id])) {
									$i = 0;
									foreach ($menus[$id] as $menu):
										$color = (empty($menu['color']) ? ($cat['default_links_color'] ?? ($config['menu_default_links_color'] ?? ($config['menu_default_color'] ?? '#ffffff'))) : '#' . $menu['color']);
										?>
										<li class="ui-state-default" id="list-<?php echo $id ?>-<?php echo $i ?>"><label>Name:</label> <input type="text" name="menu[<?php echo $id ?>][]" value="<?php echo escapeHtml($menu['name']); ?>"/>
											<label>Link:</label> <input type="text" name="menu_link[<?php echo $id ?>][]" value="<?php echo $menu['link'] ?>"/>
											<input type="hidden" name="menu_blank[<?php echo $id ?>][]" value="0"/>
											<label><input class="blank-checkbox" type="checkbox" <?php echo($menu['blank'] == 1 ? 'checked' : '') ?>/><span title="Open in New Window">New Window</span></label>
											<input class="color-picker" type="text" name="menu_color[<?php echo $id ?>][]" value="<?php echo $color; ?>"/>
											<a class="remove-button" id="remove-button-<?php echo $id ?>-<?php echo $i ?>"><i class="fas fa-trash"></a></i></li>
										<?php $i++; $last_id[$id] = $i;
									endforeach;
								} ?>
							</ul>
						</div>
					</div>
				</div>
			<?php endforeach ?>
		</div>
		<div class="row pb-2">
			<div class="col-md-12">
				<button type="submit" name="save" class="btn btn-info">Save</button>
				<?php
				echo '<button type="button" class="btn btn-danger float-right" value="Cancel" onclick="window.location = \'' . ADMIN_URL . '?p=menus\';"><i class="fas fa-cancel"></i> Cancel</button>';
				?>
			</div>
		</div>
	</form>
	<?php
	$twig->display('admin.menus.js.html.twig', array(
		'menus' => $menus,
		'last_id' => $last_id,
	));
	?>
	<?php
} else {
	$templates = Menu::select('template')->distinct()->get()->toArray();
	foreach ($templates as $key => $value) {
		$path = TEMPLATES . $value['template'];

		if (isset($pluginThemes[$value['template']])) {
			$path = BASE . $pluginThemes[$value['template']];
		}

		if (!file_exists($path . '/config.php')) {
			unset($templates[$key]);
		}
	}

	$twig->display('admin.menus.form.html.twig', array(
		'templates' => $templates
	));
}

function onTemplateMenusChange(): void
{
	$cache = Cache::getInstance();
	if ($cache->enabled()) {
		$cache->delete('template_menus');
	}
}
