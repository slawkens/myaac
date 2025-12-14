<?php
function get_template_pages($category): array
{
	global $menus;

	$ret = array();
	foreach($menus[$category] ?? [] as $menu) {
		$ret[] = $menu['link'];
	}

	return $ret;
}
?>
let category = '<?php
if(str_contains(URI, 'subtopic=')) {
	$tmp = [$_REQUEST['subtopic']];
}
else {
	$tmp = URI;
	if(empty($tmp)) {
		$tmp = ['news'];
	}
	else {
		$tmp = explode('/', URI);
	}
}

foreach (config('menu_categories') as $id => $info) {
	$templatePages = get_template_pages($id);

	if ($id == MENU_CATEGORY_ACCOUNT) {
		$templatePages = array_merge($templatePages, ['account']);
	}

	if (in_array($tmp[0], $templatePages)) {
		echo $info['id'];
		break;
	}
}
?>';
