<?php

if(PAGE != 'news') {
	return;
}

$query = $db->query('SELECT `thumb` FROM `' . TABLE_PREFIX . 'gallery` WHERE `id` = ' . $db->quote($config['gallery_image']));
if($query->rowCount() == 1):
$image = $query->fetch();
?>
<div id="GalleryBox" class="Themebox" style="background-image:url(<?php echo $template_path; ?>/images/themeboxes/gallery/gallerybox.gif);">
	<a href="?subtopic=gallery&image=<?php echo $config['gallery_image']; ?>" >
		<img id="GalleryContent" class="ThemeboxContent" src="<?php echo $image['thumb']; ?>" alt="Screenshot of the Day" />
	</a>
	<div class="Bottom" style="background-image:url(<?php echo $template_path; ?>/images/general/box-bottom.gif);"></div>
</div>
<?php endif; ?>
