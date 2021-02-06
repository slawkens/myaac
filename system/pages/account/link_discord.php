<?php
defined('MYAAC') or die('Direct access not allowed!');

if ( !isset($config['discord_application_id']) || !isset($config['discord_application_secret']) ) {
    $twig->display('error_box.html.twig', array('errors' => ['The server-side config is missing required options to link Discord accounts.']));
    return;
}


parse_str($_REQUEST['query'], $parsed_query);
$code = $parsed_query['code'];

if ( !isset($code) ) {
    $twig->display('error_box.html.twig', array('errors' => ['Missing a code querystring parameter. (Maybe try again?)']));
    return;
}

$query = http_build_query(
    array(
        'client_id' => $config['discord_application_id'],
        'client_secret' => $config['discord_application_secret'],
        'grant_type' => 'authorization_code',
        'code' => $code,
        'redirect_uri' => getLink('account/discord/link/'),
        'scope' => 'identify'
    )
);

$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, 'https://discord.com/api/v8/oauth2/token');
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, $query);

$response = json_decode(curl_exec($curl));
$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

if ($status != 200) {
    curl_close($curl);
    log_append('discord.log', 'Token exchange failed with code ' . $status . ' query: ' . $query);
    $twig->display('error_box.html.twig', array('errors' => ['An error occured while exchanging the token with Discord. (Maybe try again?)']));
    return;
}

curl_reset($curl);
curl_setopt($curl, CURLOPT_URL, 'https://discord.com/api/v8/users/@me');
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_HTTPHEADER, array(
    'Authorization: ' . $response->token_type . ' ' . $response->access_token
));

$response = json_decode(curl_exec($curl));
$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
curl_close($curl);

if ($status != 200) {
    log_append('discord.log', 'Fetching failed with code ' . $status . ' response' . $response);
    $twig->display('error_box.html.twig', array('errors' => ['An error occured while fetching details about your account from Discord. (Maybe try again?)']));
    return;
}

$account_logged->setDiscordID($response->id);
$account_logged->setDiscordTag($response->username . '#' . $response->discriminator);
$account_logged->logAction('Linked Discord account <b>' . $account_logged->getDiscordTag() . ' (' . $account_logged->getDiscordID() . ')</b>.');
$account_logged->save();

$twig->display('success.html.twig', array(
    'title' => 'Link Discord Account',
    'description' => 'Your Discord account has been linked with your Tibia account.'
));
?>
