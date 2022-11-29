<?php
/**
 * Teleport Admin Tool
 *
 * @package   MyAAC
 * @author    Slawkens <slawkens@gmail.com>
 * @author    Lee
 * @copyright 2020 MyAAC
 * @link      https://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');

$title = 'Mass Teleport Actions';

function admin_teleport_position($x, $y, $z) {
    global $db;
    $statement = $db->prepare('UPDATE `players` SET `posx` = :x, `posy` = :y, `posz` = :z');
    if (!$statement) {
        error('Failed to prepare query statement.');
        return;
    }

    if (!$statement->execute([
        'x' => $x, 'y' => $y, 'z' => $z
    ])) {
        error('Failed to execute query.');
        return;
    }

    success('Player\'s position updated.');
}

function admin_teleport_town($town_id) {
    global $db;
    $statement = $db->prepare('UPDATE `players` SET `town_id` = :town_id');
    if (!$statement) {
        error('Failed to prepare query statement.');
        return;
    }

    if (!$statement->execute([
        'town_id' => $town_id
    ])) {
        error('Failed to execute query.');
        return;
    }

    success('Player\'s town updated.');
}

if (isset($_POST['action']) && $_POST['action'])    {

    $action = $_POST['action'];

    if (preg_match("/[^A-z0-9_\-]/", $action)) {
        error('Invalid action.');
    } else {

        $playersOnline = 0;
        if($db->hasTable('players_online')) {// tfs 1.0
            $playersOnline = $db->query('SELECT count(*) FROM `players_online`');
        } else {
            $playersOnline = $db->query('SELECT count(*) FROM `players` WHERE `players`.`online` > 0');
        }

        if ($playersOnline > 0) {
            error('Please, close the server before execute this action otherwise players will not be affected.');
            return;
        }

        $town_id = isset($_POST['town_id']) ? intval($_POST['town_id']) : 0;
        $posx = isset($_POST['posx']) ? intval($_POST['posx']) : 0;
        $posy = isset($_POST['posy']) ? intval($_POST['posy']) : 0;
        $posz = isset($_POST['posz']) ? intval($_POST['posz']) : 0;

        switch ($action) {
            case 'set-town':
                if (!isset($config['towns'][$town_id])) {
                    error('Please fill all inputs');
                    return;
                }

                admin_teleport_town($value);
                break;
            case 'set-position':
                if (!$posx || !$posy || !$posz) {
                    error('Please fill all inputs');
                    return;
                }

                admin_teleport_position($posx, $posy, $posz);
                break;
            default:
                error('Action ' . $action . 'not found.');
        }
    }

}

$twig->display('admin.tools.teleport.html.twig', array());
