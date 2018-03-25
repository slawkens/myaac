<?php
defined('MYAAC') or die('Direct access not allowed!');
$GuildList = new OTS_Guilds_List();
$GuildListArray = array();

foreach($GuildList as $Guild)
{
$GuildLogo = $Guild->getCustomField('logo_name');
  if(empty($GuildLogo) || !file_exists('images/guilds/' . $GuildLogo))
    $GuildLogo = "default.gif";

  $GuildListArray[] = array(
    'name' => $Guild->getName(),
    'link' => getGuildLink($Guild->getName(), false),
    'logo' => $GuildLogo,
    'description' => $Guild->getCustomField('description')
  );
}

echo $twig->render('guilds.list.html.twig', array(
	'guilds' => $GuildListArray,
));
?>
