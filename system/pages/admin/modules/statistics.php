<?php
defined('MYAAC') or die('Direct access not allowed!');
$count = $db->query('SELECT
  (SELECT COUNT(*) FROM `accounts`) as total_accounts, 
  (SELECT COUNT(*) FROM `players`) as total_players,
  (SELECT COUNT(*) FROM `guilds`) as total_guilds,
  (SELECT COUNT(*) FROM `' . TABLE_PREFIX . 'monsters`) as total_monsters,
  (SELECT COUNT(*) FROM `houses`) as total_houses;')->fetch();

$error_icon = '<i class="fas fa-exclamation-circle text-danger"></i>';
?>
	<div class="col">
		<div class="info-box">
			<span class="info-box-icon bg-info elevation-1"><i class="fas fa-user-plus"></i></span>
			<div class="info-box-content">
				<span class="info-box-text">Accounts:</span>
				<span class="info-box-number"><?php echo $count['total_accounts']?></span>
			</div>
		</div>
	</div>
	<div class="col">
		<div class="info-box">
			<span class="info-box-icon bg-red elevation-1"><i class="fas fa-user-plus"></i></span>
			<div class="info-box-content">
				<span class="info-box-text">Players:</span>
				<span class="info-box-number"><?php echo $count['total_players']?></span>
			</div>
		</div>
	</div>
	<div class="col">
		<div class="info-box">
			<span class="info-box-icon bg-teal elevation-1"><i class="fas fa-pastafarianism"></i></span>
			<div class="info-box-content">
				<span class="info-box-text">Monsters:</span>
				<span class="info-box-number"><?php echo $count['total_monsters']?></span>
			</div>
		</div>
	</div>
	<div class="col">
		<div class="info-box">
			<span class="info-box-icon bg-green elevation-1"><i class="fas fa-chart-pie"></i></span>
			<div class="info-box-content">
				<span class="info-box-text">Guilds:</span>
				<span class="info-box-number"><?php echo $count['total_guilds']?></span>
			</div>
		</div>
	</div>
	<div class="col">
		<div class="info-box">
			<span class="info-box-icon bg-yellow elevation-1"><i class="fas fa-home"></i></span>
			<div class="info-box-content">
				<span class="info-box-text">Houses:</span>
				<span class="info-box-number"><?php echo $count['total_houses']?></span>
			</div>
		</div>
	</div>
