# Changelog

## [0.9.0-alpha - x.x.2023]

Minimum PHP version for this release is 7.2.5.

### Added
* reworked Admin Panel (@Leesneaks, @gpedro, @slawkens)
  * updated to Bootstrap v4
  * new Menu
  * new Dashboard: statistics, server status
  * new Admin Bar showed on top when admin logged in
  * new page: Server Data, to reload server data
  * new pages: mass account & teleport tools
  * editable changelogs
  * revised Accounts & Players editors
  * option to add/modify menus with plugins
  * option to enable/disable plugins
  * better, updated TinyMCE editor (v6.x)
    * with option to upload images
  * list of open source libraries used in project
* brand new charming installation page (by @fernandomatos)
  * using Bootstrap
* new pages router: nikic/fast-route, allowing for better customisation
* Guild Wars support (available as plugin)
* support for login and create account only by email (configurable)
  * with no need for account name
* Google ReCAPTCHA v3 support (available as plugin)
* automatically load towns names from .OTBM file
* support for Account Number
  * suggest account number option
* many new functions, hooks and configurables
* better Exception Handler (Whoops - https://github.com/filp/whoops)
* add Cypress testing

### Changed
* Composer is now used for external libraries like: Twig, PHPMailer, fast-route etc.
* mail support is disabled on fresh install, can be manually enabled by user
* don't show PHP errors on prod
* disable add php pages in admin panel for security. Option to disable plugins upload
* visitors counter shows now user browser, and also if its bot
* changes in required and optional PHP extensions
* reworked Pages:
	* Bans
		* works now for TFS 1.x
	* Highscores
		* frags works for TFS 1.x
		* cached
* moved pages to Twig:
  * experience stages
* update player_deaths entries on name change
* change_password email to be more informal

### Fixed
* hundrets of bug fixes, mostly patched from 0.8, so it makes no sense writing them again here
