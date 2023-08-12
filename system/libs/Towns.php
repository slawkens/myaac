<?php
/**
 * Project: MyAAC
 *     Automatic Account Creator for Open Tibia Servers
 *
 * This is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This software is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @package   MyAAC
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2020 MyAAC
 * @link      https://my-aac.org
 */

use MyAAC\Models\Town;

/**
 * Class Towns
 */
class Towns
{
	/**
	 * @var string
	 */
	private static $filename = CACHE . 'towns.php';

	/**
	 * Determine towns
	 *
	 * @return array
	 */
	public static function determine()
	{
		global $db;

		if($db->hasTable('towns')) {
			return self::getFromDatabase();
		}

		return self::getFromOTBM();
	}

	/**
	 * Load cached towns file
	 */
	public static function load()
	{
		$towns = config('towns');
		if (file_exists(self::$filename)) {
			$towns = require self::$filename;
		}

		config(['towns', $towns]);
	}

	/**
	 * Save into cache file
	 *
	 * @return bool
	 */
	public static function save()
	{
		$towns = self::determine();
		if (count($towns) > 0) {
			file_put_contents(self::$filename, '<?php return ' . var_export($towns, true) . ';', LOCK_EX);
			return true;
		}

		return false;
	}

	/**
	 * Load from OTBM map file
	 *
	 * @return array
	 */
	public static function getFromOTBM()
	{
		$mapName = configLua('mapName');
		if (!isset($mapName)) {
			$mapName = configLua('map');
			$mapFile = config('server_path') . $mapName;
		}

		if (strpos($mapName, '.otbm') === false) {
			$mapName .= '.otbm';
		}

		if (!isset($mapFile)) {
			$mapFile = config('data_path') . 'world/' . $mapName;
		}

		if (strpos($mapFile, '.gz') !== false) {
			$mapFile = str_replace('.gz', '', $mapFile);
		}

		$towns = [];
		if (file_exists($mapFile)) {
			ini_set('memory_limit', '-1');

			require LIBS . 'TownsReader.php';
			$townsReader = new TownsReader($mapFile);
			$townsReader->load();

			$towns = $townsReader->get();
		}

		return $towns;
	}

	/**
	 * Load from database
	 *
	 * @return array
	 */
	public static function getFromDatabase()
	{
		return Town::pluck('name', 'id')->toArray();
	}
}
