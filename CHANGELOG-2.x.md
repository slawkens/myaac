## [2.0-dev - x.x.2025]

### Added
* Add an "access" option to Menus (#340)
	* Possibility to hide menus for unauthorized users
* Add the possibility to fetch skills in the getTopPlayers function (#347)

### Changed
* Better handling of vocations: (#345)
  * Load from vocations.xml (No need to manually set)
  * Support for Monk vocation
* Reworked account action logs to use a single IP column as varchar(45) for both ipv4 and ipv6 (#289)
* Admin Panel: save menu collapse state (https://github.com/slawkens/myaac/commit/55da00520df7463a1d1ca41931df1598e9f2ffeb)

### Internal
* Refactor account/lost pages (#326)
* Refactor OTS_Player to support more distros (#348)
* Refactor PHP cache to store expiration and improve typing (https://github.com/slawkens/myaac/commit/96b8e00f4999f8b4c4c97b54b97d91c6fd7df298)
* Move forum show_board code to Twig (https://github.com/slawkens/myaac/commit/e0e0e467012a5fb9979cc4387af6bad1d4540279)
* Save db cache only if it has changed (https://github.com/slawkens/myaac/commit/11cb1cf97e74f3bccf59360e1efb800a426b3d43)
