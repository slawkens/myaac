## [2.0-dev - x.x.2025]

### Added
* Add an "access" option to Menus (#340)
	* Possibility to hide menus for unauthorized users
* Add the possibility to fetch skills in the getTopPlayers function

### Changed
* Better handling of vocations: (#345)
  * Load from vocations.xml (No need to manually set)
  * Support for Monk vocation
* Reworked account action logs to use a single IP column as varchar(45) for both ipv4 and ipv6 (#289)
