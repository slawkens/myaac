<?php
$nick = stripslashes($_REQUEST['nick']);

$player = new OTS_Player();
$account = new OTS_Account();
$player->find($nick);
if($player->isLoaded())
	$account = $player->getAccount();
if($account->isLoaded())
{
	$account_key = $account->getCustomField('key');
	if(!empty($account_key))
	{
		echo 'If you enter right recovery key you will see form to set new e-mail and password to account. To this e-mail will be send your new password and account name.<BR>
					<FORM ACTION="' . getLink('account/lost') . '?action=step2" METHOD=post>
					<TABLE CELLSPACING=1 CELLPADDING=4 BORDER=0 WIDTH=100%>
					<TR><TD BGCOLOR="'.$config['vdarkborder'].'" class="white"><B>Please enter your recovery key</B></TD></TR>
					<TR><TD BGCOLOR="'.$config['darkborder'].'">
					Character name:&nbsp;<INPUT TYPE=text NAME="nick" VALUE="'.$nick.'" SIZE="40" readonly="readonly"><BR />
					Recovery key:&nbsp;&nbsp;&nbsp;&nbsp;<INPUT TYPE=text NAME="key" VALUE="" SIZE="40"><BR>
					</TD></TR>
					</TABLE>
					<BR>
					<TABLE CELLSPACING=0 CELLPADDING=0 BORDER=0 WIDTH=100%><TR><TD><div style="text-align:center">
					' . $twig->render('buttons.submit.html.twig') . '</div>
					</TD></TR></FORM></TABLE></TABLE>';
	}
	else
		echo 'Account of this character has no recovery key!';
}
else {
	echo 'Player or account of player <b>' . htmlspecialchars($nick) . '</b> doesn\'t exist.';
}

$twig->display('account.back_button.html.twig', [
	'new_line' => true,
	'center' => true,
	'action' => getLink('account/lost'),
]);
