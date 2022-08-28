<?php
$menus = get_template_menus();

function get_template_pages($category) {
	global $menus;

	$ret = array();
	foreach($menus[$category] as $menu) {
		$ret[] = $menu['link'];
	}

	return $ret;
}
?>
var category = '<?php
if(strpos(URI, 'subtopic=') !== false) {
	$tmp = array($_REQUEST['subtopic']);
}
else {
	$tmp = URI;
	if(empty($tmp)) {
		$tmp = array('news');
	}
	else {
		$tmp = explode('/', URI);
	}
}

if(in_array($tmp[0], get_template_pages(MENU_CATEGORY_NEWS)))
	echo 'news';
elseif(in_array($tmp[0], get_template_pages(MENU_CATEGORY_LIBRARY)))
	echo 'library';
elseif(in_array($tmp[0], get_template_pages(MENU_CATEGORY_COMMUNITY)))
	echo 'community';
elseif(in_array($tmp[0], array_merge(get_template_pages(MENU_CATEGORY_ACCOUNT), array('account'))))
	echo 'account';
elseif(in_array($tmp[0], get_template_pages(MENU_CATEGORY_SHOP)))
	echo 'shops';
else {
	echo 'news';
}
?>';
