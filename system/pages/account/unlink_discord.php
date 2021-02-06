<?php
defined('MYAAC') or die('Direct access not allowed!');

if ( $account_logged->getDiscordID() === null && $account_logged->getDiscordTag() === null ) {
    $twig->display('error_box.html.twig', array('errors' => ['There is no Discord account linked with your Tibia account.']));
    return;
}

$account_logged->logAction('Unlinked Discord account <b>' . $account_logged->getDiscordTag() . ' (' . $account_logged->getDiscordID() . ')</b>.');
$account_logged->setDiscordID(null);
$account_logged->setDiscordTag(null);
$account_logged->save();

$twig->display('success.html.twig', array(
    'title' => 'Unlink Discord Account',
    'description' => 'Your Discord account has been unlinked with your Tibia account.'
));

?>
