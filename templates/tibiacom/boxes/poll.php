<?php

if(PAGE != 'news') {
	return;
}

$poll = $db->query('SELECT `id`, `question` FROM `z_polls` WHERE end > ' . time() . ' ORDER BY `end` LIMIT 1');
if($poll->rowCount() > 0) {
	$poll = $poll->fetch();
	?>
	<div id="CurrentPollBox" class="Themebox"
	     style="background-image:url(<?php echo $template_path; ?>/images/themeboxes/current-poll/currentpollbox.gif);">
		<div id="CurrentPollText"><?php echo $poll['question']; ?></div>
		<a class="ThemeboxButton" href="<?php echo getLink('polls') . '/' . $poll['id']; ?>" onMouseOver="MouseOverBigButton(this);" onMouseOut="MouseOutBigButton(this);" style="background-image:url(<?php echo $template_path; ?>/images/global/buttons/sbutton.gif);"><div class="BigButtonOver" style="background-image:url(<?php echo $template_path; ?>/images/global/buttons/sbutton_over.gif);"></div><div class="ButtonText" style="background-image:url(<?php echo $template_path; ?>/images/global/buttons/_sbutton_votenow.gif);"></div>
		</a>
		<div class="Bottom"
		     style="background-image:url(<?php echo $template_path; ?>/images/general/box-bottom.gif);"></div>
	</div>
	<?php
}
?>
