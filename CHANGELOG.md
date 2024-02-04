# Changelog

## [1.0-beta - 02.02.2024]

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
* new console script: aac (comes from MyAAC) - using symfony/console
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
* Composer is now used for external libraries like: Twig, PHPMailer, fast-route etc.
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
