<?php
function news_parse($title, $content, $date, $icon = 0, $author = '', $comments = '')
{
	global $template_path;

	if($content[0] != '<')
	{
		$tmp = $template_path.'/images/letters/'.$content[0].'.gif';
		if(file_exists($tmp)) {
			$firstLetter = '<img src="' . $tmp . '" alt="'.$content[0].'" BORDER=0 ALIGN=bottom>';
			$content[0] = '';
		}
	}

	return '
	<div class="NewsHeadline">
		<div class="NewsHeadlineBackground" style="background-image:url(' . $template_path . '/images/news/newsheadline_background.gif)">
			<img src="' . $template_path . '/images/news/icon_' . $icon . '.gif" class=\'NewsHeadlineIcon\' />
			<div class="NewsHeadlineDate">' . date("j.n.Y", $date) . ' - </div>
			<div class="NewsHeadlineText">' . stripslashes($title) . '</div>
			' . (isset($author[0]) ? '
			<div class="NewsHeadlineAuthor"><b>Author: </b><i>' . $author . '</i></div>' : '') . '
		</div>
	</div>
	<table style="clear:both" border=0 cellpadding=0 cellspacing=0 width="100%" >
		<tr>
			<td style="padding-left:10px;padding-right:10px;" >' . (isset($firstLetter) ? $firstLetter : '').$content . '</td>
		</tr>'
		. (isset($comments[0]) ? '
		<tr>
			<td>
				<div style="text-align: right; margin-right: 10px;"><a href="' . $comments . '">» Comment on this news</a></div>
			</td>
		</tr>' : '') .
	'</table><br/>';
}
?>
