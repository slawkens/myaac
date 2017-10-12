<?php

class Creatures {
	public static function loadFromXML($show = false) {
		global $config, $db;
		
		try { $db->query("DELETE FROM `myaac_monsters`;"); } catch(PDOException $error) {}
		
		if($show) {
			echo '<h2>Reload monsters.</h2>';
			echo "<h2>All records deleted from table 'myaac_monsters' in database.</h2>";
		}
		
		$allmonsters = new OTS_MonstersList($config['data_path'].'monster/');
		//$names_added must be an array
		$names_added[] = '';
		//add monsters
		foreach($allmonsters as $lol) {
			$monster = $allmonsters->current();
			if(!$monster->loaded()) {
				if($show) {
					warning('Error while adding monster: ' . $allmonsters->currentFile());
				}
				continue;
			}
			//load monster mana needed to summon/convince
			$mana = $monster->getManaCost();
			//load monster experience
			$exp = $monster->getExperience();
			//load monster name
			$name = $monster->getName();
			//load monster health
			$health = $monster->getHealth();
			//load monster speed and calculate "speed level"
			$speed_ini = $monster->getSpeed();
			if($speed_ini <= 220) {
				$speed_lvl = 1;
			} else {
				$speed_lvl = ($speed_ini - 220) / 2;
			}
			//check "is monster use haste spell"
			$defenses = $monster->getDefenses();
			$use_haste = 0;
			foreach($defenses as $defense) {
				if($defense == 'speed') {
					$use_haste = 1;
				}
			}
			//load monster flags
			$flags = $monster->getFlags();
			//create string with immunities
			$immunities = $monster->getImmunities();
			$imu_nr = 0;
			$imu_count = count($immunities);
			$immunities_string = '';
			foreach($immunities as $immunitie) {
				$immunities_string .= $immunitie;
				$imu_nr++;
				if($imu_count != $imu_nr) {
					$immunities_string .= ", ";
				}
			}
			
			//create string with voices
			$voices = $monster->getVoices();
			$voice_nr = 0;
			$voice_count = count($voices);
			$voices_string = '';
			foreach($voices as $voice) {
				$voices_string .= '"'.$voice.'"';
				$voice_nr++;
				if($voice_count != $voice_nr) {
					$voices_string .= ", ";
				}
			}
			//load race
			$race = $monster->getRace();
			//create monster gfx name
			//$gfx_name =  str_replace(" ", "", trim(mb_strtolower($name))).".gif";
			$gfx_name =  trim(mb_strtolower($name)).".gif";
			//don't add 2 monsters with same name, like Butterfly
			
			if(!isset($flags['summonable']))
				$flags['summonable'] = '0';
			if(!isset($flags['convinceable']))
				$flags['convinceable'] = '0';
			
			if(!in_array($name, $names_added)) {
				try {
					$db->query("INSERT INTO `myaac_monsters` (`hide_creature`, `name`, `mana`, `exp`, `health`, `speed_lvl`, `use_haste`, `voices`, `immunities`, `summonable`, `convinceable`, `race`, `gfx_name`, `file_path`) VALUES (0, " . $db->quote($name) . ", " . $db->quote(empty($mana) ? 0 : $mana) . ", " . $db->quote($exp) . ", " . $db->quote($health) . ", " . $db->quote($speed_lvl) . ", " . $db->quote($use_haste) . ", " . $db->quote($voices_string) . ", " . $db->quote($immunities_string) . ", " . $db->quote($flags['summonable'] > 0 ? 1 : 0) . ", " . $db->quote($flags['convinceable'] > 0 ? 1 : 0) . ", ".$db->quote($race).", ".$db->quote($gfx_name).", " . $db->quote($allmonsters->currentFile()) . ")");
					
					if($show) {
						success("Added: ".$name."<br/>");
					}
				}
				catch(PDOException $error) {
					if($show) {
						warning('Error while adding monster (' . $name . '): ' . $error->getMessage());
					}
				}
				
				$names_added[] = $name;
			}
		}
		
		return true;
	}
}