## [2.0-beta - 23.10.2026]

Compatible with PHP 8.2 - 8.5.

### Added
* Add show views and visitors counter in admin dashboard (https://github.com/slawkens/myaac/commit/58eae138b831a23e2549004600a4bb109cea639a)
* Open Graph Tags (https://github.com/slawkens/myaac/commit/e5f5023f136b3ea363158934bd356243c23ba126)
* Settings: Configurable favicon (https://github.com/slawkens/myaac/commit/f8b9e413a6940adcdb84d48b29b50f57db7a62b8, https://github.com/slawkens/myaac/commit/eea950730ec4c2b165b0b6101e9bd6aa86c430df)
* Admin Visitors: Add "Hide Bots" checkbox (https://github.com/slawkens/myaac/commit/5ce7a9104852935ff4f0cc8f7d27d71042c16e7b, https://github.com/slawkens/myaac/commit/c4b7d867853b53c6c98f70c2ba4b018f4de75ca3)
* Admin Panel: New hook for dashboard: statistics, allows to customize the icons (https://github.com/slawkens/myaac/commit/1f4c439d047afe2b2826adcaae936ec908dcdd48)
* Commands: Add settings:migrate command (to migrate 0.8 config.php into settings) (https://github.com/slawkens/myaac/commit/8372c63a74945f942d00bcc75a31e2b916019c4a)

### Fixed
* Accounts Editor: Prevent duplicate emails (https://github.com/slawkens/myaac/commit/9cc44c1e1ff9f9e8475bd1a4c11a3a594ee9db71)
* Admin Menus: Fix submenus ordering (https://github.com/slawkens/myaac/commit/51d20bce9405dbb122f26d56edbf77dac2a0b561)
* Account Manage: Fix wrong recovery key generate price showed (https://github.com/slawkens/myaac/commit/e3158ce8833ab1d7e9c5f45296b527660f3b2952)
* Accounts Editor: Make player rename check case-insensitive (https://github.com/slawkens/myaac/commit/50861897b4a7ce18a67775bb09fd9c8aa3cb8017)
* General: Throw exception if session cannot be started (https://github.com/slawkens/myaac/commit/0e05213cb42708925dfcc49de89a6cddf1b3306b)
* Plugins: Fix upload when the plugin is disabled (https://github.com/slawkens/myaac/commit/c1885f594e5718ad97fa66aff1f1728559d3e78e)
* Settings: Improve "database manual" detection (https://github.com/slawkens/myaac/commit/7ea01d4955dc45b9c07a3a3bf1ba9f25ba801011)
* Admin - Mailer: Fix sending images by paste (CTRL + V), the images were not correctly displayed (https://github.com/slawkens/myaac/commit/d4e8e26e4bc5e5f2cdd300a70fb781c5e303d019)
* Server Status: Add missing return in the ServerStatus->getPlayers (https://github.com/slawkens/myaac/commit/7c46a9191dd1adf712d9b40f457c986af16d1848)
* Install: Fix PHP warning in install (config.lua not exist) (https://github.com/slawkens/myaac/commit/2fe4c7be55cee72c31da74cd5eb3d6b933284fb8)
* Admin - Pages: Display error message if page cannot be found (edit) (https://github.com/slawkens/myaac/commit/0a2b01c1b07a41adbcb8591352b8329e5625961e)
* Status: Fixed #394 status_port detection problems (https://github.com/slawkens/myaac/commit/8dd31ffa08dfdd64ae19ec5845080590ae97fb56)
* DebugBar: Prevent debugbar jquery conflict #392 (https://github.com/slawkens/myaac/commit/1d6a746383cf64abdd6bec2456c24159e65226b3)
* Validator: Fixed duplicate call of Validator::newCharacterName (https://github.com/slawkens/myaac/commit/4d737927b2e23701371bc13a8466f9a2c103ec35)
* Images: New changelog type icons (https://github.com/slawkens/myaac/commit/31e66d09bea2324a8b5ad9f2f8def7bd9cbc1414)
* PHP: Fixed deprecation warnings (https://github.com/slawkens/myaac/commit/64a3857109ee18be3d24246e0055ab5ff7010c0a, https://github.com/slawkens/myaac/commit/0d9b3f6894b722b964ba649563e3838a20dc3c5a)

### Changed
* Make "Powered by MyAAC" configurable (https://github.com/slawkens/myaac/commit/4f5e466480f1a9165b6600040a69153377fbe4f5)
* Make counters (visitors & views) always enabled, just configurable in footer (https://github.com/slawkens/myaac/commit/92a47f3b321ad81b5b3851d0348220cadcf36ee9)
* Admin Dashboard: Make the statistics numbers clickable (https://github.com/slawkens/myaac/commit/dd856aeb3ff95ff7cfd785896665f18c9cd46159)
* Visitors Counter: Use persistent cache (https://github.com/slawkens/myaac/commit/97857119a240ee7ac9ad53aa79a3a07894b44e4d)
* Settings: Auto detect mime type for favicon and og:image (https://github.com/slawkens/myaac/commit/6f10c6a2ec50f91ec19012ce93c152e037627fc2)
* Validator: Better email verification method (https://github.com/slawkens/myaac/commit/f51d63b65c6b49a1274c3bbdd666a99fe00d98f0)

### Updates
* Updated composer dependencies (https://github.com/slawkens/myaac/commit/e586fa8f92b5ff2438a4e9790ba2d8cb59b763b8, https://github.com/slawkens/myaac/commit/a4245c4ebc869d790baafa4fd817d05da2200d13)
* (dev) Upgrade phpstan to v2 (https://github.com/slawkens/myaac/commit/c2be9640b619e75a6e5f96967249904ea93f9c69)

## [2.0-alpha - 11.08.2026]

### Added
* Menus: Add an "access" option to Menus (#340)
  * Possibility to hide menus for unauthorized users
* Settings: Add Reset button (https://github.com/slawkens/myaac/commit/7104c2258fd724a55239821b46a616dab845b22a, https://github.com/slawkens/myaac/commit/e274b8350451a20c24e652ea05ed1964ebb86b54)
* New Setting: block create account spam by ip (https://github.com/slawkens/myaac/commit/54265f42e987522803288477952d6e5c4daeeb24)
* feat: debugbar backtrace (by @gpedro) (#360)
* Functions: Add the possibility to fetch skills, balance and frags in the getTopPlayers function (#347)
* Plugins: autoload init-priority option (https://github.com/slawkens/myaac/commit/f1aa12840875960849fa0c99a2bbe0ad2949bbec)

### Changed
* Better handling of vocations: (#345)
  * Load from vocations.xml (No need to manually set)
  * Support for Monk vocation
* Better gallery, loads images from images/gallery folder
* Reworked account action logs to use a single IP column as varchar(45) for both ipv4 and ipv6 (#289)
* Make myaac_config table columns bigger (https://github.com/slawkens/myaac/commit/2c62a97160a3ffe9976ee5bd1d770a0abc576742)
* Admin Panel: save menu collapse state (https://github.com/slawkens/myaac/commit/55da00520df7463a1d1ca41931df1598e9f2ffeb)
* Do not save sessions in myaac system folder (https://github.com/slawkens/myaac/commit/6f2bfd21eb7657e98a71ab09c20e2b39fd1cfbdd)

### Internal
* Refactor account/lost pages (#326)
* Refactor OTS_Player to support more distros (#348)
* Refactor PHP cache to store expiration and improve typing (https://github.com/slawkens/myaac/commit/96b8e00f4999f8b4c4c97b54b97d91c6fd7df298)
* Move forum show_board code to Twig (https://github.com/slawkens/myaac/commit/e0e0e467012a5fb9979cc4387af6bad1d4540279)
* Save db cache only if it has changed (https://github.com/slawkens/myaac/commit/11cb1cf97e74f3bccf59360e1efb800a426b3d43)
