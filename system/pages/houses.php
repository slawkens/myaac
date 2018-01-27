<?php
/**
 * Houses
 *
 * @package   MyAAC
 * @author    Gesior <jerzyskalski@wp.pl>
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2017 MyAAC
 * @link      http://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');
$title = 'Houses';

if(!$db->hasColumn('houses', 'name')) {
	echo 'Houses list is not available on this server.';
	return;
}
$rent = trim(strtolower($config['lua']['houseRentPeriod']));
if($rent != 'yearly' && $rent != 'monthly' && $rent != 'weekly' && $rent != 'daily')
	$rent = 'never';

$state = '';
$order = '';
$type = '';
?>
<table border="0" cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td><img src="<?php echo $template_path; ?>/images/general/blank.gif" width="10" height="1" border="0"></td>
		<td>
<?php
		if(isset($_GET['page']) && $_GET['page'] == 'view' && isset($_REQUEST['house']))
		{
			$beds = array("", "one", "two", "three", "fourth", "fifth");
			$houseName = $_REQUEST['house'];
			$houseId = (Validator::number($_REQUEST['house']) ? $_REQUEST['house'] : -1);
			$house = $db->query('SELECT * FROM ' . $db->tableName('houses') . ' WHERE ' . $db->fieldName('name') . ' LIKE ' . $db->quote($houseName) . ' OR `id` = ' . $db->quote($houseId));

			if($house->rowCount() > 0)
			{
				$house = $house->fetch();
				$houseId = $house['id'];

				$title = $house['name'] . ' - ' . $title;
				echo '
				<table border="0" cellspacing="1" cellpadding="4">
					<tr><td valign="top">';
						$img_path = 'images/houses/' . $houseId . '.gif';
						if(file_exists($img_path))
							echo '<img src="' . $img_path . '">';
						else
							echo '<img src="images/houses/default.jpg">';

						echo '
						</td>
						<td valign="top"><B>' . $house['name'] . '</b><br>This house ';
						$houseBeds = $house['beds'];
						if($houseBeds > 0)
							echo 'has ' . (isset($beds[$houseBeds]) ? $beds[$houseBeds] : $houseBeds) . ' bed' . ($houseBeds > 1 ? 's' : '');
						else
							echo 'dont have any beds';

						echo '.<br/><br/>The house has a size of <b>' . $house['size'] . ' square meters</b>.';

						if($rent != 'never')
							echo ' The ' . $rent . ' rent is <b>' . $house['rent'] . ' gold</b> and will be debited to the bank account on <b>' . $config['lua']['serverName'] . '</b>.';

						$houseOwner = $house['owner'];
						if($houseOwner > 0)
						{
							$guild = NULL;
							echo '<br/><br/>The house has been rented by ';
							if(isset($house['guild']) && $house['guild'] == 1)
							{
								$guild = new OTS_Guild();
								$guild->load($houseOwner);
								echo getGuildLink($guild->getName());
							}
							else
								echo getCreatureName($houseOwner);

							echo '.';

							if($rent != 'never' && $house['paid'] > 0)
							{
								$who = '';
								if($guild)
									$who = $guild->getName();
								else
								{
									$player = new OTS_Player();
									$player->load($houseOwner);
									if($player->isLoaded())
									{
										$sexs = array('She', 'He');
										$who = $sexs[$player->getSex()];
									}
								}
								echo ' ' . $who . ' has paid the rent until <b>' . date("M d Y, H:i:s", $house['paid']) . ' CEST</b>.';
							}
						}

						echo '</TD></TR></TABLE>';
			}
			else
				echo 'House with name ' . $houseName . ' does not exists.';
		}
		else
		{
			echo '
				Here you can see the list of all available houses, flats' . ($db->hasTable('houses', 'guild') ? ' or guildhall' : '') . '.
				Click on any view button to get more information about a house or adjust
				the search criteria and start a new search.<br/><br/>';
				if(isset($config['lua']['houseCleanOld'])) {
					$cleanOld = (int)(eval('return ' . $config['lua']['houseCleanOld'] . ';') / (24 * 60 * 60));
					if($cleanOld > 0 || $rent != 'never')
					{
						echo '<b>Every morning during global server save there is automatic house cleaning. Server delete house owners who have not logged in last ' . $cleanOld . ' days';
						if($rent != 'never')
						{
							echo ' or have not paid ' . $rent . ' house rent. Remember to leave money for a rent in ';
							$bank = getBoolean($config['lua']['bankSystem']);
							if($bank)
								echo 'your house bank account or ';

							echo 'depo in same city where you have house!';
						}
						else
							echo '.';

						echo '</b><br/><br/>';
					}
				}

				echo '<br/>';

				if(isset($_POST['town']) && isset($_POST['state']) && isset($_POST['order'])
					&& (isset($_POST['type']) || !$db->hasColumn('houses', 'guild')))
				{
					$order = $_POST['order'];
					$orderby = '`name`';
					if(!empty($order))
					{
						if($order == 'size')
							$orderby = '`size`';
						else if($order == 'rent')
							$orderby = '`rent`';
					}

					$town = 'town';
					if($db->hasColumn('houses', 'town_id'))
						$town = 'town_id';
					else if($db->hasColumn('houses', 'townid'))
						$town = 'townid';
	
					$whereby = '`' . $town . '` = ' .(int)$_POST['town'];
					$state = $_POST['state'];
					if(!empty($state))
						$whereby .= ' AND `owner` ' . ($state == 'free' ? '' : '!'). '= 0';

					$type = isset($_POST['type']) ? $_POST['type'] : NULL;
					if($type == 'guildhalls' && !$db->hasColumn('houses', 'guild'))
						$type = 'all';

					if(!empty($type) && $type != 'all')
							$whereby .= ' AND `guild` ' . ($type == 'guildhalls' ? '!' : '') . '= 0';

					$houses_info = $db->query('SELECT * FROM `houses` WHERE ' . $whereby. ' ORDER BY ' . $orderby);

				echo '
				<TABLE BORDER=0 CELLSPACING=1 CELLPADDING=4 WIDTH=100%>
					<TR BGCOLOR='.$config['vdarkborder'].'>
						<TD COLSPAN=6 class="white"><B>Available ' . ($type == 'guildhalls' ? 'Guildhalls' : 'Houses and Flats').' in '.$config['towns'][$_POST['town']].' on <b>'.$config['lua']['serverName'].'</b></B></TD>
					</TR>
					<TR BGCOLOR='.$config['darkborder'].'>';
					if($houses_info->rowCount() > 0)
					{
					echo '
						<TD WIDTH=40%><B>Name</B></TD>
						<TD WIDTH=10%><B>Size</B></TD>
						<TD WIDTH=10%><B>Rent</B></TD>

						<TD WIDTH=40%><B>Status</B></TD>
						<TD>&#160;</TD>';
					}
					else
						echo '<TD>No ' . ($type == 'guildhalls' ? 'guildhalls' : 'houses') . ' with specified criterias.</TD>';

					echo '</TR>';

					$players_info = $db->query("SELECT `houses`.`id` AS `houseid` , `players`.`name` AS `ownername` FROM `houses` , `players` , `accounts` WHERE `players`.`id` = `houses`.`owner` AND `accounts`.`id` = `players`.`account_id`");
					$players = array();
					foreach($players_info->fetchAll() as $player)
						$players[$player['houseid']] = array('name' => $player['ownername']);

					$rows = 1;
					foreach($houses_info->fetchAll() as $house)
					{
						$owner = isset($players[$house['id']]) ? $players[$house['id']] : array();
						echo
						'<TR BGCOLOR="'.getStyle($rows).'">
							<TD WIDTH="40%"><NOBR>'.$house['name'].'</TD>
							<TD WIDTH="10%"><NOBR>'.$house['size'].' sqm</TD>
							<TD WIDTH="10%"><NOBR>'.$house['rent'].' gold</TD>
							<TD WIDTH="40%"><NOBR>';
						if($db->hasColumn('houses', 'guild') && $house['guild'] == 1 && $house['owner'] != 0)
						{
							$guild = new OTS_Guild();
							$guild->load($house['owner']);
							echo 'Rented by ' . getGuildLink($guild->getName());
						}
						else
						{
							if(!empty($owner['name']))
								echo 'Rented by ' . getPlayerLink($owner['name']);
							else
								echo
									'Free';
						}

						echo '
							</TD>
							<TD>
								<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=0>
									<FORM ACTION=?subtopic=houses&page=view METHOD=post>
										<TR><TD>
											<INPUT TYPE=hidden NAME=house VALUE="'.$house['name'].'">
											<INPUT TYPE=image NAME="View" ALT="View" SRC="'.$template_path.'/images/global/buttons/sbutton_view.gif" BORDER=0 WIDTH=120 HEIGHT=18>
										</TD></TR>
									</FORM>
								</TABLE>
							</TD>
						</TR>';
						$rows++;
					}
					echo
					'</TABLE>'.
					'<br/><br/>';
				}

				echo '
				<form method="post">
				<TABLE BORDER=0 CELLSPACING=1 CELLPADDING=4 WIDTH=100%>
					<TR BGCOLOR=' . $config['vdarkborder'] . '>
						<TD COLSPAN=4 class="white"><B>House Search</B></TD>
					</TR>
					<TR BGCOLOR=' . $config['darkborder'] . '>
						<TD WIDTH=25%><B>Town</B></TD>
						<TD WIDTH=25%><B>Status</B></TD>
						<TD WIDTH=25%><B>Order</B></TD>
					</TR>
					<TR BGCOLOR=' . $config['darkborder'] . '>
						<TD VALIGN=top ROWSPAN=2>';
						$townId = isset($_POST['town']) ? $_POST['town'] : '';
						$i = 0;
						$checked = false;
						foreach($config['towns'] as $id => $name)
						{
							if($id == 0)
								continue;

							$i++;
							if(((empty($townId) && !empty($name)) || $id == $townId) && !$checked)
							{
								$add = 'CHECKED';
								$checked = true;
							}
							else
								$add = '';

							if(!empty($name))
								echo '<INPUT TYPE=radio NAME="town" id="town_' . $id . '" VALUE="'.$id.'" '.$add.'><label for="town_' . $id . '"> '.$name.'</label><BR>';
						}

						echo '
						</TD>
						<TD VALIGN=top>
							<INPUT TYPE=radio NAME="state" id="state_all" VALUE="" '.(empty($state) ? 'CHECKED' : '').'><label for="state_all"> all states</label><br/>
							<INPUT TYPE=radio NAME="state" id="state_free" VALUE="free" '.($state == 'free' ? 'CHECKED' : '').'><label for="state_free"> free</label><br/>
							<INPUT TYPE=radio NAME="state" id="state_rented" VALUE="rented" '.($state == 'rented' ? 'CHECKED' : '').'><label for="state_rented"> rented</label><br/>
						</TD>
						<TD VALIGN=top ROWSPAN=2>
							<INPUT TYPE=radio NAME="order" id="order_name" VALUE="" '.(empty($order) ? 'CHECKED' : '').'><label for="order_name"> by name</label><br/>
							<INPUT TYPE=radio NAME="order" id="order_size" VALUE="size" '.($order == 'size' ? 'CHECKED' : '').'><label for="order_size"> by size</label><br/>
							<INPUT TYPE=radio NAME="order" id="order_rent" VALUE="rent" '.($order == 'rent' ? 'CHECKED' : '').'><label for="order_rent"> by rent</label><br/>
						</TD>
					</TR>';
					
					if($db->hasColumn('houses', 'guild')) {
						echo '
						<TR BGCOLOR='.$config['darkborder'].'>
							<TD VALIGN=top>
								<INPUT TYPE=radio NAME="type" VALUE="" '.(empty($type) ? 'CHECKED' : '').'> all<BR>
								<INPUT TYPE=radio NAME="type" VALUE="houses" '.($type == 'houses' ? 'CHECKED' : '').'> houses and flats<BR>
								<INPUT TYPE=radio NAME="type" VALUE="guildhalls" '.($type == 'guildhalls' ? 'CHECKED' : '').'> guildhalls<BR>
							</TD>
						</TR>';
					}
				echo '
				</TABLE>
				<BR>
				<CENTER>
					<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=0><TR><TD>
						' . $twig->render('buttons.submit.html.twig') . '
					</TD></TR></FORM></TABLE>
				</CENTER>';
			}
			echo '
			</TD>
			<TD><IMG SRC="'.$template_path.'/images/general/blank.gif" WIDTH=10 HEIGHT=1 BORDER=0></TD>
		</TR>
	</TABLE>
	';
?>
