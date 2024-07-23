# Changelog

## [1.0-RC -23.07.2024]

Changes since 1.0-beta:

### Added
* Feat: Hooks priority (https://github.com/slawkens/myaac/commit/dc17b701da053e04bfa64e21be9247a4f07505e1)
* Make autoload of pages, commands and themes configurable (https://github.com/slawkens/myaac/commit/c1d4b4f80cd6bb85507ee9471e47013955a26a91)
* Fraggers in characters page for TFS 1.x and canary (https://github.com/slawkens/myaac/commit/42f99c3edc8de39cccc5632cb42e88b24579c5a6)
* New hooks: HOOK_INSTALL_FINISH, HOOK_ACCOUNT_CREATE_CHARACTER_* (https://github.com/slawkens/myaac/commit/08ac8ebade106521a5c7396faa5ce7006e629f7c, https://github.com/slawkens/myaac/commit/45dda5e834ff2059faea6ef9be2efa76f1723cbd)

### Changed
* Allow account_create_character_create even if account_mail_verify is activated (https://github.com/slawkens/myaac/commit/203e411b626fe62401a4b74a48420769e512aa39)
* Create guild_rank entries, in case MySQL trigger not loaded (https://github.com/slawkens/myaac/commit/d9c1b2507c81f306970642b35e4bf5f7cc04a6f2, https://github.com/slawkens/myaac/commit/47a19e85dd84e9f3b39a1b29cfc2c04b004832b9)
* Set Admin Account verified by default (https://github.com/slawkens/myaac/commit/cd49dfc79942f3301ce9c0b8d899b9f39bda9a41)
* Refactor account routes into sub folders (https://github.com/slawkens/myaac/commit/bdc0c43d3fd3a51030c3e916bdb9f008468f5ecd)
* Order towns by id (https://github.com/slawkens/myaac/commit/9ea2a5067fc4b75de395f381577b18914132ad84)
* Do not create news about myaac, if any news already exist (on installation (https://github.com/slawkens/myaac/commit/504242fb846b73b56b87bc1e39d070687ad7f5b4)

### Fixed
* Not working google recaptcha plugin (https://github.com/slawkens/myaac/commit/a1bcb217ecf4e21fd58da4ba491da1852029898a)
* Not working account create if account_country is disabled (https://github.com/slawkens/myaac/commit/933b681a9fcdbb6283e0469b3806d2ded492d232)
* Account verify - do not allow login without verified email (Thanks @anyeor, https://github.com/slawkens/myaac/commit/fcb13f3c0fb8ceafda0bd614a229a26a269432bd)
* Detect tools/ext exists on install to prevent broken installs (https://github.com/slawkens/myaac/commit/10a739773c4f2911876bc802a0ee0537c3e00a92)
* Cache reloading each time page refreshes (https://github.com/slawkens/myaac/commit/ec96985872057340112f65073efc0c4bf86dddb0)
* Highscores frags for TFS 1.x and canary (https://github.com/slawkens/myaac/commit/a04d186c22912915f0a7873dfe677ef3b5a23c79)
* Monsters page: monster not found exception (https://github.com/slawkens/myaac/commit/ef79b99b8acc179f14b8475547347d9daca27512)
* Fixed bug if \<flags\> are not present in monster.xml (https://github.com/slawkens/myaac/commit/57b47ab7983f625c7c0ef4f5303a4d07ef172786)
* fastRoute duplicate errors (https://github.com/slawkens/myaac/commit/4c0739d3e93812dff0c33849ea3f38e4e49113ac)
* useGuildNick displaying (https://github.com/slawkens/myaac/commit/0db0ec1aa47e044c26bc403ff5078a2115d086f8)

## [1.0-beta - 18.05.2024]

Minimum PHP version for this release is 8.1.

### Added
* reworked Admin Panel (@Leesneaks, @gpedro, @slawkens)
  * updated to Bootstrap v4
  * new Menu
  * new Dashboard: statistics, server status
  * new Admin Bar showed on top when admin logged in
  * new page: Server Data, to reload server data
    * Towns, NPCs & Items are stored in permanent cache
  * new pages: mass account & teleport tools
  * changelogs editor
  * revised Accounts & Players editors
  * option to add/modify admin menus with plugins
  * option to enable/disable plugins
  * better, updated TinyMCE editor (v6.x)
    * with option to upload images
  * list of open source libraries used in project page
* auto-loading of themes, commands & pages from plugins/ folder. You need just to place them in correct folder and they will be loaded automatically - this allows better customization, without interfering with core AAC folders. This will allow in the future automatic updates for plugins as well the AAC as whole.
* config.php moved to Admin Panel -> Settings page
* new console script: aac - using symfony/console
  * usage: `php aac` (will list all commands by default)
  * example: `php aac cache:clear`
  * example: `php aac plugin:install theme-example.zip`
* replace POT Query Builder to Eloquent ORM. Not 100% yet - in some places there is still old $db approach used (@gpedro) (https://github.com/slawkens/myaac/pull/230)
* brand new charming installation page (by @fernandomatos)
  * using Bootstrap
* new pages router: nikic/fast-route, allowing for better customisation
* Plugin cronjobs: central control of the cronjobs
* Guild Wars support (available as plugin)
* support for login and create account only by email (configurable)
  * with no need for account name
* Google ReCAPTCHA v3 support (available as plugin)
* automatically load towns names from .OTBM file
* support for Account Number
  * suggest account number option
* many new functions, hooks and configurables
* better Exception Handler (Whoops - https://github.com/filp/whoops)
* automated website tests (using Cypress)
* csrf protection (https://github.com/slawkens/myaac/pull/235)
* option to restrict Page view to specified group of users (Not-Logged in, logged-in players, tutors, gamemasters etc.)
* phpdebug bar (http://phpdebugbar.com/). Activated if env == 'dev', can be also activated in production by enabling "enable_debugbar" in local config

### Changed
* Composer and NPM is now used for external libraries like: Twig, PHPMailer, fast-route, jQuery, Bootstrap etc.
* mail support is disabled on fresh install, can be manually enabled by user
* disable add php pages in admin panel for security. Option to disable plugins upload
* visitors counter shows now user browser, and also if its bot
* changes in required and optional PHP extensions
* reworked Pages:
	* Bans
		* works now for TFS 1.x
	* Highscores
		* frags works for TFS 1.x
		* cached
	* Monsters
* moved pages to Twig:
  * experience stages
* update player_deaths entries on name change
* change_password email to be more informal

### Fixed
* hundrets of bug fixes, mostly patched from 0.8, so it makes no sense writing them again here
