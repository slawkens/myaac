<?php
/**
 * Menus
 *
 * @package   MyAAC
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2017 MyAAC
 * @version   0.6.6
 * @link      http://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');
$title = 'Menus';

if(!hasFlag(FLAG_CONTENT_MENUS) && !superAdmin())
{
	echo 'Access denied.';
	return;
}

if(isset($_REQUEST['template'])) {
	$template = $_REQUEST['template'];
	
	if(isset($_REQUEST['menu'])) {
		$post_menu = $_REQUEST['menu'];
		$post_menu_link = $_REQUEST['menu_link'];
		if(count($post_menu) != count($post_menu_link)) {
			echo 'Menu count is not equal menu links. Something went wrong when sending form.';
			return;
		}
		
		$db->query('DELETE FROM `' . TABLE_PREFIX . 'menu` WHERE `template` = ' . $db->quote($template));
		foreach($post_menu as $id => $menus) {
			foreach($menus as $i => $menu) {
				if(empty($menu)) // don't save empty menu item
					continue;
				
				try {
					$db->insert(TABLE_PREFIX . 'menu', array('template' => $template, 'name' => $menu, 'link' => $post_menu_link[$id][$i], 'category' => $id, 'ordering' => $i));
				}
				catch(PDOException $error) {
					warning('Error while adding menu item (' . $name . '): ' . $error->getMessage());
				}
			}
		}
		
		success('Saved at ' . date('H:i'));
	}
	
	$file = TEMPLATES . $template . '/config.php';
	if(file_exists($file)) {
		require_once($file);
	}
	else {
		echo 'Cannot find template config.php file.';
		return;
	}
	
	if(!isset($config['menu_categories'])) {
		echo "No menu categories set in template config.php.<br/>This template doesn't support dynamic menus.";
		return;
	}
	
	echo 'Hint: You can drag menu items.<br/>Editing: ' . $template . ' template.';
	$menus = array();
	$menus_db = $db->query('SELECT `name`, `link`, `category`, `ordering` FROM `' . TABLE_PREFIX . 'menu` WHERE `enabled` = 1 AND `template` = ' . $db->quote($template) . ' ORDER BY `ordering` ASC;')->fetchAll();
	foreach($menus_db as $menu) {
		$menus[$menu['category']][] = array('name' => $menu['name'], 'link' => $menu['link'], 'ordering' => $menu['ordering']);
	}
	
	$last_id = array();
	echo '<form method="post" action="?p=menus">';
	echo '<input type="hidden" name="template" value="' . $template . '"/>';
	foreach($config['menu_categories'] as $id => $cat) {
		echo '<h2>' . $cat['name'] . '<img class="add-button" id="add-button-' . $id . '" src="' . BASE_URL . 'images/plus.png" width="16" height="16"/></h2>';
		echo '<ul class="sortable" id="sortable-' . $id . '">';
		if(isset($menus[$id])) {
			$i = 0;
			foreach($menus[$id] as $menu) {
				echo '<li class="ui-state-default" id="list-' . $id . '-' . $i . '"><input type="text" name="menu[' . $id . '][]" value="' . $menu['name'] . '"/><input type="text" name="menu_link[' . $id . '][]" value="' . $menu['link'] . '"/><a class="remove-button" id="remove-button-' . $id . '-' . $i . '"><img src="' . BASE_URL . 'images/del.png"/></a></li>';
				
				$i++;
				$last_id[$id] = $i;
			}
		}
			
		echo '</ul>';
	}
	
	echo '<input type="submit" class="button" value="Update">';
	echo '<input type="button" class="button" value="Cancel" onclick="window.location = \'' . ADMIN_URL . '?p=menus&template=' . $template . '\';">';
	echo '</form>';
	
	echo $twig->render('admin.menus.js.html.twig', array(
		'menus' => $menus,
		'last_id' => $last_id
	));
}
else {
	$templates = $db->query('SELECT `template` FROM `' . TABLE_PREFIX . 'menu` GROUP BY `template`;')->fetchAll();
	
	echo $twig->render('admin.menus.form.html.twig', array(
		'templates' => $templates
	));
}
?>