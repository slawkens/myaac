<?php
/**
 * Bug tracker
 *
 * @package   MyAAC
 * @author    Gesior <jerzyskalski@wp.pl>
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2019 MyAAC
 * @link      https://my-aac.org
 */

use MyAAC\Models\BugTracker;

defined('MYAAC') or die('Direct access not allowed!');
$title = 'Bug tracker';

if(!$logged)
{
	echo  'You are not logged in. <a href="?subtopic=accountmanagement&redirect=' . BASE_URL . urlencode('?subtopic=bugtracker') . '">Log in</a> to post on the bug tracker.<br /><br />';
	return;
}

$showed = $post = $reply = false;
    // type (1 = question; 2 = answer)
    // status (1 = open; 2 = new message; 3 = closed;)

    $dark = $config['darkborder'];
    $light = $config['lightborder'];

    $tags = array(1 => "[MAP]", "[WEBSITE]", "[CLIENT]", "[MONSTER]", "[NPC]", "[OTHER]");

    if(admin() and isset($_REQUEST['control']) && $_REQUEST['control'] == "true")
    {
        if(empty($_REQUEST['id']) and empty($_REQUEST['acc']) or !is_numeric($_REQUEST['acc']) or !is_numeric($_REQUEST['id']) )
            $bug[1] = BugTracker::where('type', 1)->orderByDesc('uid')->get()->toArray();

        if(!empty($_REQUEST['id']) and is_numeric($_REQUEST['id']) and !empty($_REQUEST['acc']) and is_numeric($_REQUEST['acc']))
			$bug[2] = BugTracker::where('type', 1)->where('account', $_REQUEST['acc'])->where('id', $_REQUEST['id'])->get()->toArray();

        if(!empty($_REQUEST['id']) and is_numeric($_REQUEST['id']) and !empty($_REQUEST['acc']) and is_numeric($_REQUEST['acc']))
        {
            if(!empty($_REQUEST['reply']))
                $reply=true;

            $account = new OTS_Account();
            $account->load($_REQUEST['acc']);
            $account->isLoaded();
            $players = $account->getPlayersList();

            if(!$reply)
            {
                if($bug[2]['status'] == 2)
                    $value = '<span style="color: green">[OPEN]</span>';
                elseif($bug[2]['status'] == 3)
                    $value = '<span style="color: red">[CLOSED]</span>';
                elseif($bug[2]['status'] == 1)
                    $value = '<span style="color: blue">[NEW ANSWER]</span>';

                echo '<TABLE BORDER=0 CELLSPACING=1 CELLPADDING=4 WIDTH=100%><TR BGCOLOR='.$config['vdarkborder'].'><TD COLSPAN=2 CLASS=white><B>Bug Tracker</B></TD></TR>';
                echo '<TR BGCOLOR="'.$dark.'"><td width=40%><i><b>Subject</b></i></td><td>'.$tags[$bug[2]['tag']].' '.$bug[2]['subject'].' '.$value.'</td></tr>';
                echo '<TR BGCOLOR="'.$light.'"><td><i><b>Posted by</b></i></td><td>';

                foreach($players as $player)
                {
                    echo ''.$player->getName().'<br>';
                }

                echo '</td></tr>';
                echo '<TR BGCOLOR="'.$dark.'"><td colspan=2><i><b>Description</b></i></td></tr>';
                echo '<TR BGCOLOR="'.$light.'"><td colspan=2>'.nl2br($bug[2]['text']).'</td></tr>';
                echo '</TABLE>';

                $answers = BugTracker::where('account', $_REQUEST['acc'])->where('id', $_REQUEST['id'])->where('type', 2)->orderBy('reply')->get()->toArray();
                foreach($answers as $answer)
                {
                    if($answer['who'] == 1)
                        $who = '<span style="color: red">[ADMIN]</span>';
                    else
                        $who = '<span style="color: green">[PLAYER]</span>';

                    echo '<br><TABLE BORDER=0 CELLSPACING=1 CELLPADDING=4 WIDTH=100%><TR BGCOLOR='.$config['vdarkborder'].'><TD COLSPAN=2 CLASS=white><B>Answer #'.$answer['reply'].'</B></TD></TR>';
                    echo '<TR BGCOLOR="'.$dark.'"><td width=70%><i><b>Posted by</b></i></td><td>'.$who.'</td></tr>';
                    echo '<TR BGCOLOR="'.$light.'"><td colspan=2><i><b>Description</b></i></td></tr>';
                    echo '<TR BGCOLOR="'.$dark.'"><td colspan=2>'.nl2br($answer['text']).'</td></tr>';
                    echo '</TABLE>';
                }
                if($bug[2]['status'] != 3)
                    echo '<br><a href="?subtopic=bugtracker&control=true&id='.$_REQUEST['id'].'&acc='.$_REQUEST['acc'].'&reply=true"><b>[REPLY]</b></a>';
            }
            else
            {
                if($bug[2]['status'] != 3)
                {
                    $reply = BugTracker::where('account', $_REQUEST['acc'])->where('id', $_REQUEST['id'])->where('type', 2)->max('reply');
                    $reply = $reply + 1;
                    $iswho =  BugTracker::where('account', $_REQUEST['acc'])->where('id', $_REQUEST['id'])->where('type', 2)->orderByDesc('reply')->first()->toArray();

                    if(isset($_POST['finish']))
                    {
                        if(empty($_POST['text']))
                            $error[] = '<span style="color: black"><b>Description cannot be empty.</b></span>';
                        if($iswho['who'] == 1)
                            $error[] = '<span style="color: black"><b>You must wait for User answer.</b></span>';
                        if(empty($_POST['status']))
                            $error[] = '<span style="color: black"><b>Status cannot be empty.</b></span>';

                        if(!empty($error))
                        {
                            foreach($error as $errors)
                                echo ''.$errors.'<br>';
                        }
                        else
                        {
                            $type = 2;
                            $INSERT =  BugTracker::create([
								'account' => $_REQUEST['aac'],
								'id' => $_REQUEST['id'],
								'text' => $_POST['text'],
								'reply' => $reply,
								'type' => $type,
								'who' => 1,
							]);
                            $UPDATE = Bugtracker::where('id', $_REQUEST['id'])->where('account', $_REQUEST['acc'])->update([
								'status' => $_POST['status']
							]);
                            header('Location: ?subtopic=bugtracker&control=true&id='.$_REQUEST['id'].'&acc='.$_REQUEST['acc'].'');
                        }
                    }
                    echo '<br><form method="post" action=""><table><tr><td><i>Description</i></td><td><textarea name="text" rows="15" cols="35"></textarea></td></tr><tr><td>Status[OPEN]</td><td><input type=radio name=status value=2></td></tr><tr><td>Status[CLOSED]</td><td><input type=radio name=status value=3></td></tr></table><br><input type="submit" name="finish" value="Submit" class="input2"/></form>';
                }
                else
                {
                    echo '<br><span style="color: black"><b>You can\'t add answer to closed bug thread.</b></span>';
                }
            }

            $post=true;
        }
        if(!$post)
        {
            echo '<TABLE BORDER=0 CELLSPACING=1 CELLPADDING=4 WIDTH=100%><TR BGCOLOR='.$config['vdarkborder'].'><TD colspan=2 CLASS=white><B>Bug Tracker Admin</B></TD></TR>';
            $i=1;
            foreach($bug[1] as $report)
            {
                if($report['status'] == 2)
                    $value = '<span style="color: green">[OPEN]</span>';
                elseif($report['status'] == 3)
                    $value = '<span style="color: red">[CLOSED]</span>';
                elseif($report['status'] == 1)
                    $value = '<span style="color: blue">[NEW ANSWER]</span>';

                echo '<TR BGCOLOR="' . getStyle($i) . '"><td width=75%><a href="?subtopic=bugtracker&control=true&id='.$report['id'].'&acc='.$report['account'].'">'.$tags[$report['tag']].' '.$report['subject'].'</a></td><td>'.$value.'</td></tr>';

                $showed=true;
                $i++;
            }
            echo '</TABLE>';
        }
    }
    else
    {
        $acc = $account_logged->getId();
        $account_players = $account_logged->getPlayersList();

        foreach($account_players as $player)
        {
            $allow=true;
        }

        if(!empty($_REQUEST['id']))
            $id = addslashes(htmlspecialchars(trim($_REQUEST['id'])));

        if(empty($_REQUEST['id']))
            $bug[1] = BugTracker::where('account', $account_logged->getId())->where('type', 1)->orderBy('id')->get()->toArray();

        if(!empty($_REQUEST['id']) and is_numeric($_REQUEST['id']))
            $bug[2] = BugTracker::where('account', $account_logged->getId())->where('type', 1)->where('id', $id)->get()->toArray();
        else
            $bug[2] = NULL;

        if(!empty($_REQUEST['id']) and $bug[2] != NULL)
        {
            if(!empty($_REQUEST['reply']))
                $reply=true;

            if(!$reply)
            {
                if($bug[2]['status'] == 1)
                    $value = '<span style="color: green">[OPEN]</span>';
                elseif($bug[2]['status'] == 2)
                    $value = '<span style="color: blue">[NEW ANSWER]</span>';
                elseif($bug[2]['status'] == 3)
                    $value = '<span style="color: red">[CLOSED]</span>';

                echo '<TABLE BORDER=0 CELLSPACING=1 CELLPADDING=4 WIDTH=100%><TR BGCOLOR='.$config['vdarkborder'].'><TD COLSPAN=2 CLASS=white><B>Bug Tracker</B></TD></TR>';
                echo '<TR BGCOLOR="'.$dark.'"><td width=40%><i><b>Subject</b></i></td><td>'.$tags[$bug[2]['tag']].' '.$bug[2]['subject'].' '.$value.'</td></tr>';
                echo '<TR BGCOLOR="'.$light.'"><td colspan=2><i><b>Description</b></i></td></tr>';
                echo '<TR BGCOLOR="'.$dark.'"><td colspan=2>'.nl2br($bug[2]['text']).'</td></tr>';
                echo '</TABLE>';

                $answers = Bugtracker::where('account', $account_logged->getId())->where('id', $id)->where('type', 2)->orderBy('reply')->get()->toArray();
                foreach($answers as $answer)
                {
                    if($answer['who'] == 1)
                        $who = '<span style="color: red">[ADMIN]</span>';
                    else
                        $who = '<span style="color: green">[YOU]</span>';

                    echo '<br><TABLE BORDER=0 CELLSPACING=1 CELLPADDING=4 WIDTH=100%><TR BGCOLOR='.$config['vdarkborder'].'><TD COLSPAN=2 CLASS=white><B>Answer #'.$answer['reply'].'</B></TD></TR>';
                    echo '<TR BGCOLOR="'.$dark.'"><td width=70%><i><b>Posted by</b></i></td><td>'.$who.'</td></tr>';
                    echo '<TR BGCOLOR="'.$light.'"><td colspan=2><i><b>Description</b></i></td></tr>';
                    echo '<TR BGCOLOR="'.$dark.'"><td colspan=2>'.nl2br($answer['text']).'</td></tr>';
                    echo '</TABLE>';
                }
                if($bug[2]['status'] != 3)
                    echo '<br><a href="?subtopic=bugtracker&id='.$id.'&reply=true"><b>[REPLY]</b></a>';
            }
            else
            {
                if($bug[2]['status'] != 3)
                {
                    $reply = BugTracker::where('account', $aac)->where('id', $id)->where('type', 2)->max('reply');
                    $reply = $reply + 1;
                    $iswho = BugTracker::where('account', $acc)->where('id', $id)->where('type', 2)->orderByDesc('reply')->first()->toArray();

                    if(isset($_POST['finish']))
                    {
                        if(empty($_POST['text']))
                            $error[] = '<span style="color: black"><b>Description cannot be empty.</b></span>';
                        if($iswho['who'] == 0)
                            $error[] = '<span style="color: black"><b>You must wait for Administrator answer.</b></span>';
                        if(!$allow)
                            $error[] = '<span style="color: black"><b>You haven\'t any characters on account.</b></span>';

                        if(!empty($error))
                        {
                            foreach($error as $errors)
                                echo ''.$errors.'<br>';
                        }
                        else
                        {
                            $type = 2;
                            $INSERT = BugTracker::create([
								'account' => $acc,
								'id' => $id,
								'text' => $_POST['text'],
								'reply' => $reply,
								'type' => $type
							]);
                            $UPDATE = BugTracker::where('id', $id)->where('account', $acc)->update([
								'status' => 1
							]);
                            header('Location: ?subtopic=bugtracker&id='.$id.'');
                        }
                    }
                    echo '<br><form method="post" action=""><table><tr><td><i>Description</i></td><td><textarea name="text" rows="15" cols="35"></textarea></td></tr></table><br><input type="submit" name="finish" value="Submit" class="input2"/></form>';
                }
                else
                {
                    echo '<br><span style="color: black"><b>You can\'t add answer to closed bug thread.</b></span>';
                }
            }

            $post=true;
        }
        elseif(!empty($_REQUEST['id']) and $bug[2] == NULL)
        {
            echo '<TABLE BORDER=0 CELLSPACING=1 CELLPADDING=4 WIDTH=100%><TR BGCOLOR='.$config['vdarkborder'].'><TD CLASS=white><B>Bug Tracker</B></TD></TR>';
            echo '<TR BGCOLOR="'.$dark.'"><td><i>Bug doesn\'t exist.</i></td></tr>';
            echo '</TABLE>';
            $post=true;
        }

        if(!$post)
        {
            if(!isset($_REQUEST['add']) || $_REQUEST['add'] != TRUE)
            {
                echo '<TABLE BORDER=0 CELLSPACING=1 CELLPADDING=4 WIDTH=100%><TR BGCOLOR='.$config['vdarkborder'].'><TD colspan=2 CLASS=white><B>Bug Tracker</B></TD></TR>';
                foreach($bug[1] as $report)
                {
                    if($report['status'] == 1)
                        $value = '<span style="color: green">[OPEN]</span>';
                    elseif($report['status'] == 2)
                        $value = '<span style="color: blue">[NEW ANSWER]</span>';
                    elseif($report['status'] == 3)
                        $value = '<span style="color: red">[CLOSED]</span>';

                    if(is_int($report['id'] / 2))
                    {
                        $bgcolor = $dark;
                    }
                    else
                    {
                        $bgcolor = $light;
                    }

                    echo '<TR BGCOLOR="'.$bgcolor.'"><td width=75%><a href="?subtopic=bugtracker&id='.$report['id'].'">'.$tags[$report['tag']].' '.$report['subject'].'</a></td><td>'.$value.'</td></tr>';

                    $showed=true;
                }

                if(!$showed)
                {
                    echo '<TR BGCOLOR="'.$dark.'"><td><i>You don\'t have reported any bugs.</i></td></tr>';
                }
                echo '</TABLE>';

                echo '<br><a href="?subtopic=bugtracker&add=true"><b>[ADD REPORT]</b></a>';
            }
            elseif(isset($_REQUEST['add']) && $_REQUEST['add'] == TRUE)
            {
                $thread = BugTracker::where('account', $acc)->where('type', 1)->orderByDesc('id')->get()->toArray();
                $id_next = BugTracker::where('account', $acc)->where('type', 1)->max('id');
                $id_next = $id_next + 1;

                if(empty($thread))
                    $thread['status'] = 3;

                if(isset($_POST['submit']))
                {
                    if($thread['status'] != 3)
                        $error[] = '<span style="color: black"><b>Can be only 1 open bug thread.</b></span>';
                    if(empty($_POST['subject']))
                        $error[] = '<span style="color: black"><b>Subject cannot be empty.</b></span>';
                    if(empty($_POST['text']))
                        $error[] = '<span style="color: black"><b>Description cannot be empty.</b></span>';
                    if(!$allow)
                        $error[] = '<span style="color: black"><b>You haven\'t any characters on account.</b></span>';
                    if(empty($_POST['tags']))
                        $error[] = '<span style="color: black"><b>Tag cannot be empty.</b></span>';

                    if(!empty($error))
                    {
                        foreach($error as $errors)
                            echo ''.$errors.'<br>';
                    }
                    else
                    {
                        $type = 1;
                        $status = 1;
                        $INSERT = BugTracker::create([
							'account' => $acc,
							'id' => $id_next,
							'text' => $_POST['text'],
							'type' => $type,
							'subject' => $_POST['subject'],
							'reply' => 0,
							'status' => $status,
							'tag' => $_POST['tags']
						]);
                        header('Location: ?subtopic=bugtracker&id='.$id_next.'');
                    }

                }
                echo '<br><form method="post" action=""><table><tr><td><i>Subject</i></td><td><input type=text name="subject"/></td></tr><tr><td><i>Description</i></td><td><textarea name="text" rows="15" cols="35"></textarea></td></tr><tr><td>TAG</td><td><select name="tags"><option value="">SELECT</option>';

                for($i = 1; $i <= count($tags); $i++)
                {
                    echo '<option value="' . $i . '">' . $tags[$i] . '</option>';
                }

                echo '</select></tr></tr></table><br><input type="submit" name="submit" value="Submit" class="input2"/></form>';
            }
        }
    }

    if(admin() and empty($_REQUEST['control']))
    {
        echo '<br><br><a href="?subtopic=bugtracker&control=true">[ADMIN PANEL]</a>';
    }
