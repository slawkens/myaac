<?php
$code = isset($_REQUEST['code']) ? trim($_REQUEST['code']) : '';
$character = isset($_REQUEST['character']) ? stripslashes(trim($_REQUEST['character'])) : '';

if(empty($code) || empty($character))
	$twig->display('account.lost.check-code.html.twig', [
		'code' => $code,
		'characters' => $character,
	]);
else
{
	$player = new OTS_Player();
	$account = new OTS_Account();
	$player->find($character);
	if($player->isLoaded()) {
		$account = $player->getAccount();
	}

	if($account->isLoaded()) {
		if($account->getCustomField('email_code') == $code) {
			echo '
				Please enter new password to your account and repeat to make sure you remember password.<BR>
				<FORM ACTION="' . getLink('account/lost') . '?action=setnewpassword" METHOD=post>
				<INPUT TYPE=hidden NAME="character" VALUE="'.$character.'">
				<INPUT TYPE=hidden NAME="code" VALUE="'.$code.'">
				<TABLE CELLSPACING=1 CELLPADDING=4 BORDER=0 WIDTH=100%>
				<TR><TD BGCOLOR="'.$config['vdarkborder'].'" class="white"><B>Passwords</B></TD></TR>
				<TR><TD BGCOLOR="'.$config['darkborder'].'">
				New password:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<INPUT TYPE=password ID="passor" NAME="passor" VALUE="" SIZE="40"><BR />
				Repeat new password:&nbsp;<INPUT TYPE=password ID="passor2" NAME="passor2" VALUE="" SIZE="40"><BR />
				</TD></TR>
				</TABLE>
				<BR>
				<TABLE CELLSPACING=0 CELLPADDING=0 BORDER=0 WIDTH=100%><TR><TD><div style="text-align:center">
				' . $twig->render('buttons.submit.html.twig') . '</div>
				</TD></TR></FORM></TABLE></TABLE>';
		}
		else {
			$error = 'Wrong code to change password.';
		}
	}
	else {
		$error = "Account of this character or this character doesn't exist.";
	}
}

if(!empty($error)) {
	$twig->display('error_box.html.twig', [
		'errors' => [$error],
	]);

	echo '<br/>';

	$twig->display('account.lost.check-code.html.twig', [

	]);
}
