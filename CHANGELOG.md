# Changelog

## [1.1 - 27.01.2025]

### Changed
* adjust mailer settings descriptions to latest gmail (https://github.com/slawkens/myaac/commit/c5d5bb80671db135e6b503f53684771c7272e05d)
* optimize $player->isOnline() function, thanks @gesior (https://github.com/slawkens/myaac/commit/10dd818b139d5e1bb1ca9ec81edfb083ba9316b4)
* make players.comment and guilds.description VARCHAR (https://github.com/slawkens/myaac/commit/a45ceab83a74bee2b89cdb72baceda75e577e3cf)
* add lua/ folder to .gitignore (https://github.com/slawkens/myaac/commit/07012f786b1114cb6ab2f064f82c645b136a375a)

### Fixed
* general fixes in the tibiacom template menus, better support for custom menus
* make functions_custom.php optional
* error in CLI, where BASE_URL is not defined (https://github.com/slawkens/myaac/commit/4d749b881582f64b5a46196dbbb5ee8097127f03)
* hook ACCOUNT_LOGIN_BEFORE_ACCOUNT location (https://github.com/slawkens/myaac/commit/669c447fca8643ce56d9ef8c1374ec647c780998)

## [1.0.1 - 14.01.2025]

### Fixed
* tibiacom account & news menu links not auto expanding

### Updated (Thanks dependabot)
* twig from ^2.0 to ^3.11
* tinymce from ^6.8.3 to ^7.2.0
* cypress from ^12.12.0 to ^13.17.0
* nesbot/carbon from 2.72.5 to 2.72.6

## [1.0 - 12.01.2025]

First stable release in the v1.0 series.

Minimum PHP 8.1 is required.

Changes since RC.2:

### Added
* feature: migrations up/down. Allows to downgrade/upgrade database to specified version (https://github.com/slawkens/myaac/commit/3f6ff3a3326b0475d28d11ffd7fff51f362d799f)
* new hooks for news management (https://github.com/slawkens/myaac/commit/011a85d8ae34283ded6999882833f9d4797028ec, https://github.com/slawkens/myaac/commit/36bd3eb846e829b45313e10f7568dc4e95841143)
* None Vocation to highscores (can be changed to RookStayer in Admin Panel) (https://github.com/slawkens/myaac/commit/a4a248099521bb5b8b2aa5bd592138debd2f19d5)
* support for button_color (green, red, blue) (https://github.com/slawkens/myaac/commit/d8b6b749ee62e88b6af4a05d3d7557f90b94d94e)
* add $whoopsHandler as variable, can be used by plugins (https://github.com/slawkens/myaac/commit/b0c8cf2ecda23045d725aaf43cfb3852ed766a4b)
* PlayerModel->outfit_url attribute (https://github.com/slawkens/myaac/commit/3b5be1a8db5dceecaa388e2925a5536d13b38881)
* support for selecting plugin themes in Admin menus.php (https://github.com/slawkens/myaac/commit/77a2c1cec343ffe4be5c2c2503ee81bc32a14ca1)

### Changed
* schema: Change character set to utf8mb4 (support for Emojis in Menus/Pages/News/Forum etc.) (https://github.com/slawkens/myaac/commit/27c44f1bdfb6234cf0c9d5b4b491123bb205b08f)
* prefer get_browser_real_ip() over REMOTE_ADDR (https://github.com/slawkens/myaac/commit/941846605c00cee83168d2f916410b8ba8d4b7b9)
* automatically set selected current one on highscores filters (https://github.com/slawkens/myaac/commit/e96227fbe41ae281783b2d49edb169a603601813)
* rewrite towns loading code, removed OTBM loader (was too slow) (https://github.com/slawkens/myaac/commit/c980a0914632e7b27f718464f669a200707d217e)
* allow OTS_Player to be passed as object to getPlayerLink (https://github.com/slawkens/myaac/commit/84d37c5a8f2c4535a41c8aa8264752969d3f3a3d)
* do not clear menus by default on install (https://github.com/slawkens/myaac/commit/12d8faa3eda5e798f97b71e941c035187daad96e)
* display warning in admin panel - plugins - if zip extension is not installed (https://github.com/slawkens/myaac/commit/e3ffe5d9e11d78ab064a370d8541bac351c9bcd9)
* set default_socket_timeout for ipinfo.io checkup to 5 seconds (https://github.com/slawkens/myaac/commit/783d96fc6568a607d3198b832fed3a0dd06c4ebb)
* refactor getTopPlayers function (support for balance) (https://github.com/slawkens/myaac/commit/c769962e39fe8dfb72ecd5be1864e145696be794)

### Fixed
* XSS in forum (https://github.com/slawkens/myaac/commit/c2b7286d20d4b579171540f7a774e8a0995d5e8f, https://github.com/slawkens/myaac/commit/8fb643596f9586005976e7bdb484a541a9d8715e)
* price deducted when changing sex (https://github.com/slawkens/myaac/commit/16671ea40b72dcf74037c359ad572f9eb825edf9)
* move_thread by unauthorized user (https://github.com/slawkens/myaac/commit/d6c40c836a53cb1710f911f77f45f28b54ea1b54, thanks @anyeor)
* TFS 1.4.2 where conditions is NULL (https://github.com/slawkens/myaac/commit/b8396d4c8482e951da538b13f2296123732c4545)
* do not show forum new thread show button if not logged in (https://github.com/slawkens/myaac/commit/507402171ba3b6e7ee184bd7fa73e0d55e0cad7a, @anyeor)
* login if limiter is disabled (https://github.com/slawkens/myaac/commit/a0f1971583f0f790013e2145fb5ac573c59fbdef)
* fixes to installMenus function (https://github.com/slawkens/myaac/commit/a2fadc5945fe0a5e39f740827f6ffbda1bb501e2)
* many PHP exceptions in different places
* fixes to tibiacom menus ActiveSubmenuItem

### Removed
* bugtracker SQL table code as the page has been removed/moved to plugins (https://github.com/slawkens/myaac/commit/5782772b901b05fb814bc718d062f6e2cd71df8c)

## [1.0-RC.2 - 25.10.2024]

Still waiting for your reports about bugs found in this release. We are very close to stable release.

### Added
* feat: rate limit settings for blocking accounts login attempts (@gpedro, #266)
* search by email in accounts editor (https://github.com/slawkens/myaac/commit/c2ec46824621468f2a1cb4046805c485ed13fea5)
* New hooks in account manage + create (https://github.com/slawkens/myaac/commit/93641fc68ac9a5f1479329e2bd41380c19534d5d)

### Changed
* chore: drop raw queries + accounts - search by email + accounts - required min size for search by account number (@gpedro, #266)
* Use https for outfit & item images (https://github.com/slawkens/myaac/commit/71c00aa5e01fbdfd88802912e200dd1025976231)
* Do not require players & guilds tables on install (https://github.com/slawkens/myaac/commit/779aa152fa940261c9b161533946f44e288597a2)
* Do not create player if there is no players table in db (https://github.com/slawkens/myaac/commit/201f95caa8b70e88fa651eac8c3c3aa7cd765bd0)

### Fixed
* Highscore frags fixed for TFS 0.3 (@Scrollog, #263)
* Missing groups variable #262. thanks, @Scrollog for reporting (https://github.com/slawkens/myaac/commit/8d8bdb6dac6df21672ac77288fff2f2f8d6eb665)
* Verified email for login.php (@gpedro, #265)
* Warning if core.account_country is disabled (https://github.com/slawkens/myaac/commit/ab73d60c61e14a1cacdb6cfbf7f89f4bf3be0833)


## [1.0-RC.1 - 23.07.2024]

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
* hundreds of bug fixes, mostly patched from 0.8, so it makes no sense writing them again here
