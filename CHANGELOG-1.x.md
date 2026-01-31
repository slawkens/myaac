# Changelog

## [1.8.8 - 31.01.2026]
### Added
* Change Comment: Add missing hooks - patched from 0.8 (https://github.com/slawkens/myaac/commit/a60a23b84f61d41d1503073b52e01e3120f6d92a)

### Changed
* Account Manage: Change the last login to the correct login time – Instead of just "now" (https://github.com/slawkens/myaac/commit/5b841682cdc473b38ef1a5edfcfe1a020802e286)
* Twig: Extract renderInline(content, context) as a method to $twig (https://github.com/slawkens/myaac/commit/5e4806f891f8c88c37d45b89bbede23afc2fa37b)
* Mail: Remove HTML tags from the email function (https://github.com/slawkens/myaac/commit/6661c78dac69c6aa498b9c79fe7da4fe0150e5c8)

### Fixed
* Forum: Fix XSS in board name (https://github.com/slawkens/myaac/commit/e52d9e486f5bf1dea867f59287f70aef3d538189, https://github.com/slawkens/myaac/commit/6db738a87c44b8d96919191ba5e661c32ab47457)
* Forum: Fix edit_post, despite being an author, edit didn't work (https://github.com/slawkens/myaac/commit/e8b47429e8c607c2662a78b65415dfa772aa0e48)
* Forum: Fix a player link in the forum thread being not clickable (When outfits are enabled) (https://github.com/slawkens/myaac/commit/f640ca636f34cd2dfc1fa8de6fdbed0674908b30)
* Settings: Fix variable overlapping if the same var name as in core (https://github.com/slawkens/myaac/commit/c2415e9df3a5ffaf768f6f9668bdd38b5efd0771)
* Settings: fix show_if for the selects (https://github.com/slawkens/myaac/commit/8dcbb66753914322706216cfd01436eb1478a5ce)

## [1.8.7 - 04.01.2026]

### Fixed
* Fixed [player/guild/house] bb code in forum (https://github.com/slawkens/myaac/commit/8ec9bf10682c73f1fe40967a106ccda2a5073ed0)

### Changed
* Settings: better responsiveness on mobile (https://github.com/slawkens/myaac/commit/c65d4e4b62ef26fb4e24ecb1d2bcc4556d746adf)
* Signatures: Return 404 when the signature player is not found (https://github.com/slawkens/myaac/commit/7e6480b380799add7a2b1b7ce1d3c1f2b6819ff1)

### Removed
* Remove setting: outfit_images_wrong_looktypes - is obsolete, the bug doesn't exist in the latest outfit images (https://github.com/slawkens/myaac/commit/cc220bedc1f01535eaac23f6961135e2e7a6e310)

## [1.8.6 - 14.12.2025]

### Added
* Added hook for adding custom rules to validate new character name (https://github.com/slawkens/myaac/commit/8e6749c59984631288e8e9803819b2f0ff389761)

### Fixed
* Highscores: Fix ordering by different skills (Adjust order by desc: skill_tries, manaspent, experience) - More exact results (https://github.com/slawkens/myaac/commit/c86257e6dacbad773aa09c0958eeaa106a967f2d)
* Fix exception shown on first install, when there is no vendor - Before it displayed 500 white page, now it display the exception (https://github.com/slawkens/myaac/commit/18a1178e4b93607a350259679e0366cb83fb4126)
* Fix typo $up -> $down, in migration nr 7, was failing due that (https://github.com/slawkens/myaac/commit/fd74f01291d0e9cdb92ee1b95021c9d7b591ad7c)

### Changed
* Ini set html_errors = 0, to show html code in exceptions (https://github.com/slawkens/myaac/commit/9ed06782e67772826d927ad847a077b99df5060d)

## [1.8.5 - 21.11.2025]

### Added
* New Setting: Account Countries Most Popular (https://github.com/slawkens/myaac/commit/946364f59d7cd01472877108ab27ec78fb28307a)

### Changed
* Status: Write to status-error.log if there is connection error (https://github.com/slawkens/myaac/commit/780d4ccef741c1dd45a00bfc121fba9f1a175313)
* Settings: escapeHtml in values (support for html code) (https://github.com/slawkens/myaac/commit/5861efdbe900ccd35309913af0c0a5f3d4cdc1a8)
* News Page: Don't display hidden news for admin - it's confusing (https://github.com/slawkens/myaac/commit/175e97828b9a08ec3080cc8d3fb4eb3f1c08649f)
* Plugins System: Add plugin:remove + plugin:delete as alias for plugin:uninstall + plugin:activate/deactivate (https://github.com/slawkens/myaac/commit/6367054487368c92741bfd1dc7c70c52aea9ee87, https://github.com/slawkens/myaac/commit/baec6c9ebf5c342b3b2f7123427c6ba21dbb93bc)

### Fixed
* Status: Fix $status['uptimeReadable'], was totally wrong (https://github.com/slawkens/myaac/commit/0a6d44bf21417562491aabc93543a2bc3a44b2df)
* Guilds: Detect "deletion" column in guilds show/delete (https://github.com/slawkens/myaac/commit/6775a061bebc9ff449522f0173556d4a7a44fa5e, https://github.com/slawkens/myaac/commit/603d860b56bc7418db09e206f40aa06d0682c00e)
* General: Ensure some cache folders & index.html exists (https://github.com/slawkens/myaac/commit/730a0f29124811f525207c24c06eb0d088fa3434)

## [1.8.4 - 27.10.2025]

### Changed
* Reimport myaac_ tables on every install, this fixes errors when one table is missing or is duplicated (https://github.com/slawkens/myaac/commit/2580edadf84779f09fd395c21f92019b2c762f83)
* Use custom env init on migrate, migrate:run and migrate:to (https://github.com/slawkens/myaac/commit/13ea68cc0c9349380c8e4051d702a6c2c8256f44, https://github.com/slawkens/myaac/commit/07fd034fe4cb0ffdb88667b1e400f414d0c6d06f)

### Fixed
* Show if there is mysql error on import schema (https://github.com/slawkens/myaac/commit/44110a9496b4385e42c31b75de301037e711b6c3)
* Fix the premium checks, introduced in v1.8.3 (https://github.com/slawkens/myaac/commit/9d92a11fb7cb6d7a1619d79c12faaa0b1c01f980)

## [1.8.3 - 21.10.2025]

### Added
* Feature: resend email verify (https://github.com/slawkens/myaac/commit/fe821c58085483e70491dcf76376ad5b96de3fdd)
* New config: hooks_debug (To view where hooks are located in .twig files) (https://github.com/slawkens/myaac/commit/8c3cb0e06f9709c1de3398b48221241e7cbdd310)
* Functions: Add db->getColumnInfo(table, column) (https://github.com/slawkens/myaac/commit/c898fe25efff6793a01d11c26fc153cb23fcb858)
* Plugins: Add option to use ?subtopic=x for plugins pages (https://github.com/slawkens/myaac/commit/97f9d3d6f6c28aef6d824973058d7133f56e09c4)
* getTopPlayers() Function - Add lookmount & promotion (https://github.com/slawkens/myaac/commit/2da0024c68f1cedc38a16ebbc6f52ffa55e65f7a, https://github.com/slawkens/myaac/commit/901df48d134079d648a18f9d82b60182e818ac02)
* New hooks for account/change-password (https://github.com/slawkens/myaac/commit/470555f2687809a0c12491bbb27597e64b8929c1)

### Changed
* Feature: show vip days in account management (https://github.com/slawkens/myaac/commit/c88b08eb1ec1f560cbfdaaa16b24e3a0f26da7b3, by @andreoam)
* Allow links in error_box.html.twig (https://github.com/slawkens/myaac/commit/9acad15451071639acf7a7d4e81619b0a9742b12)
* Canary - Comment code to update lastday in login.php (https://github.com/slawkens/myaac/commit/38902c30d114fdbce259467f5820f97037b393e9)
* Cache::remember $ttl = -1 = infinite (https://github.com/slawkens/myaac/commit/64acf70d3854182d88aaf0b67f77cea2a254f179)

### Fixed
* Online - Allow for html code (example - img) in online_datacenter (https://github.com/slawkens/myaac/commit/3bb272ebbbd2eb7769d174b7082061d14a17bd44)
* Guilds - Fix guild create with freePremium enabled (https://github.com/slawkens/myaac/commit/c91bb5d4097647dca2196d3dea87bc90c89181d2)
* Canary - Fix premDays count (https://github.com/slawkens/myaac/commit/3e61692780d4add93b7b0e9f12f7a283bd8f4b7a)
* Template Change: Ignore set last visit for AJAX pages - Fixes template change redirect (https://github.com/slawkens/myaac/commit/89fae38caa7e4f645957fcf1a9330a36358ac04f)
* Admin Panel - Accounts: Fix lastip v6 (TFS master) (https://github.com/slawkens/myaac/commit/f54b1bdd2af4c16c64ddff0e87a6c96bc4cf9eeb)
* Functions - Prevent injection in $db->hasColumn (https://github.com/slawkens/myaac/commit/56bd7ec5ed904666074492f2e4f13e4fce226bee)
* Compat Config: Add missing config: email_lai_sec_interval (https://github.com/slawkens/myaac/commit/2eae44e0755e624a91be68b4d1ec26d01eb4d9a1)

## [1.8.2 - 26.09.2025]

### Added
* Routes: Possibility to override routes with plugins pages, like characters.php - No need to define routes in plugin.json anymore (https://github.com/slawkens/myaac/commit/3f24f961b1cdeff5c60387e837ae454448bc5e1b)

### Changed
* Style: Better look for myaac-table (https://github.com/slawkens/myaac/commit/a6032093b21e5bb3f0e75d2704da87d6dea6469d, https://github.com/slawkens/myaac/commit/5aa9bbf1c8e580d973ec82ac012489f8e7bc437e)

### Fixed
* Install: Fix when config.local.php cannot be saved (https://github.com/slawkens/myaac/commit/4eab805d26d8c5562b29ed699769919d77dabced)
* Create Account: Fix an exception when email cannot be sent (https://github.com/slawkens/myaac/commit/d0112d1a67e8b854b65ad131f0375b79305df8d3)
* Login Page: Add missing csrf() - fix create account button (https://github.com/slawkens/myaac/commit/3c0cb53e17dd0b85394cfa0fdc9cf9ad8d4551df)
* tibiacom template: Fix account lost menu (https://github.com/slawkens/myaac/commit/ed9beaf2b6ca069e304e569c52e5b9188b58f05c)
* tibiacom template: Fix Menu div wrong tag/closing (#329) (https://github.com/slawkens/myaac/commit/85e7005fd3f0be51466151a3c122b96085fdfe68)
* tibiacom template: Replace firstChild with firstElementChild (Thanks to @un000000) (https://github.com/slawkens/myaac/commit/df7b6e29fb8875da97f431468c81ee99116271d9)

## [1.8.1 - 05.09.2025]

### Added
* New Commands: plugin:enable/disable/uninstall {plugin-name} (https://github.com/slawkens/myaac/commit/7a08f91d3fc0897c1ff76089ef3c649a2c6d2003, https://github.com/slawkens/myaac/commit/fec773ba4b740f35c0a3ef92ca8444a4c7d02082)
* Gifts: Added Transferable Coins to the store dropdown menu in the admin area (by @andreoam, #321) (https://github.com/slawkens/myaac/commit/42671c5c199dd9e91c774d8c9d30da9e12f1b695)

### Changed
* Commands: Allow settings to be changed/reset by plugin name (https://github.com/slawkens/myaac/commit/f8c4332e03e838d285ea0afb4b72b7c23e324d45, https://github.com/slawkens/myaac/commit/4b948e9510f7ba69d00f84d7fdaea8b3bf05b630)
* Templates: Menus should be saved for each template separately (https://github.com/slawkens/myaac/commit/482f4067b2a2e7513d9ba214274a361ffaf123d8)

### Fixed
* Online: Fix skulls display (#320) (https://github.com/slawkens/myaac/commit/98073a110ae13f9592ec9d2c4d1d1aace87587a9)
* Online: Fix if there is no world_id in the server_record table (https://github.com/slawkens/myaac/commit/b6e1620f14c20eecfc9001a7d86dfb67942985c6) (Reported by @gesior in #318)
* tibiacom: some fixes to menus (https://github.com/slawkens/myaac/commit/20f99903ae80c74ad66c1cf5a5ea8d0b0fc2fd70, https://github.com/slawkens/myaac/commit/11dae90fa94fbbf47447017db5e5847c33d6aadf)
* Guilds: Fix for some servers that don't have guild_invites table (https://github.com/slawkens/myaac/commit/9725a3c2bdb7003f5cb48febb77604c31a9b805b)

## [1.8 - 02.08.2025]

### Added
* Templates - Kathrine: Possibility to add custom menu categories (https://github.com/slawkens/myaac/commit/ec11c1402417c25980582467546d1c1e9bb8267f)
* Admin Panel - Accounts Editor: Add Coins Transferable (https://github.com/slawkens/myaac/commit/45d6047031c9c3a0e7e512dc5d15c75629aec5a2, https://github.com/slawkens/myaac/commit/bb097b69ce106500a49686d6f4fe604348eaa310)
* Highscores:
  * Revamped: (https://github.com/slawkens/myaac/commit/d8132d4d76e03d5aa0c042be426320655a601392)
    * Show real rank, if 2 or more players have the same skill, show them with same rank
    * New setting: highscores_online_status
    * Additional fields passed to twig: updatedAt, totalResults, page, baseLink
  * Add new Setting: Display Skills Box (https://github.com/slawkens/myaac/commit/36ca755243ef1c83f6ac87465b426d4d8d3b0bb9)
* Functions: Add getExperienceForLevel (level) (https://github.com/slawkens/myaac/commit/1566deb84a082176b8c683fda205d828bc38fbcc)
* Commands - cache:clear : Add warning about APCu clear in CLI (https://github.com/slawkens/myaac/commit/83f84172e02e8ea2ccb6dca29bc033e44c35aebc)
* Models - PlayerOnline: Add missing $fillable into model (https://github.com/slawkens/myaac/commit/43415cf35db1c1307f2684c1728693d65065ffff)
* Twig: add cache variable (https://github.com/slawkens/myaac/commit/0efe47ce71c4b364a9e96bc5a55b1655326ae6da)

### Changed
* pages/online: add cache, resulting in 20x performance boost
  * (for an example server with 2k players) (https://github.com/slawkens/myaac/commit/c8363086015cbb6e8786c398c7b9ac3959a26ec4)
* Admin Bar: Move admin bar code into body_start place_holder (https://github.com/slawkens/myaac/commit/f17269e44ce9dd38447bd2e2a8e1bdb065d4161f)
* Cache::remember: $ttl = 0 means no cache (https://github.com/slawkens/myaac/commit/3b47e9df2f4051807c5ff87892f7fa3d348f9c55)
* Templates: Load config.ini with $process_sections set to true (https://github.com/slawkens/myaac/commit/a89f9a84847630eb75b4890fdcc8b7a7bfa6b8ac)
* Twig: Allow for timestamp as integer in the timeago twig function
  (https://github.com/slawkens/myaac/commit/34fead906ea13b9f09d7a3c41ed88109d34d386c)

### Fixed
* Settings: Fixed two exceptions (https://github.com/slawkens/myaac/commit/6e5a4ff8c78ff5373aba091baa66cae029557643, https://github.com/slawkens/myaac/commit/20d69a641c0a933d14889a89da6d32f6a4bc6c7d)
* Models\Account + OTS_Account -> isPremium -> ignore config.freePremium (https://github.com/slawkens/myaac/commit/5271633bdbfbbfed0b1d59c403093ce6fc2b7d20)
* Admin Panel - Mailer:
  * Fix send to email link redirecting from accounts page (https://github.com/slawkens/myaac/commit/080cc2781f034c844af658229e495e9a47fd2298)
  * Option to send only to verified accounts - only if setting('core.account_mail_verify') enabled (https://github.com/slawkens/myaac/commit/cf7fd20452e863980045bb5d6012ec86c6e8e01f)

### Internal
* Rewrite to use constants (account transferable coins) (https://github.com/slawkens/myaac/commit/bccf8e056df985bbe1bab5f7ab5492f714d6b62b)
* Refactor to use HAS_ACCOUNT_COINS (https://github.com/slawkens/myaac/commit/caf326a6584a234775ebc6c8000ea02b3fecd160)

## [1.7.1 - 27.06.2025]

### Changed
* Rename plugin:install:install to plugin:setup, also add alias to previous command (https://github.com/slawkens/myaac/commit/13d33822b59df349199e885a78a3d6beb0863d0b)

### Fixed
* Fix commands: setup + cache:clear (https://github.com/slawkens/myaac/commit/0da524fefe93b3028392e9014550eea3324d3a22, https://github.com/slawkens/myaac/commit/fe8281594e989f00280ba1adc734a9198c6b5cc1)
* Fix polls link in tibiacom template (https://github.com/slawkens/myaac/commit/d90fa323d7c77d81768df60feeb1c374b1650a0c)

## [1.7 - 22.06.2025]

### Added
* Feature: plugins versions check (#310)
* New hooks: HOOK_ACCOUNT_MANAGE_AFTER_CHARACTERS, HOOK_GUILDS_AFTER_MANAGE_BUTTON (https://github.com/slawkens/myaac/commit/c074a48f245df55646b6705737f667b6a84149b2, https://github.com/slawkens/myaac/commit/e6100a1b72de8695bba1dae9ba4e28bfdce47b10)
* Add OTS_Toolbox::getVocationName(id, promotion) + OTS_Player->isNameLocked() (https://github.com/slawkens/myaac/commit/e222957893c4a1de0dc8dbba55bce1a43418d275, https://github.com/slawkens/myaac/commit/522f6c11d835afd36fd07a07074d96d7e219b488)
* Add missing csrf in more places, causing white page with error about Request (https://github.com/slawkens/myaac/commit/dca904e61d21d856bf809070e7652803a2df0f58, https://github.com/slawkens/myaac/commit/c720ccc451ff90ef40b2a1595468d061ffd7e1e4)

### Changed
* Revamped online page (https://github.com/slawkens/myaac/commit/9a90e4aae280e607430511c6727d9a714b11f4c5, https://github.com/slawkens/myaac/commit/4767120043b09141870383e249f3729638d53dc2)
* Better $title inventing (https://github.com/slawkens/myaac/commit/0c95bcfd06b68b21512e477646ef7bd3a0d4912b)

### Fixed
* Use apcu cache clear (https://github.com/slawkens/myaac/commit/b329da52aae9d0e21120a6444d3caf442420ce50, https://github.com/slawkens/myaac/commit/566c2a9151ab6392286f74e26853faa19a1b4f24)
* fix: boostedcreatures for 13.40 (by @GooseWithAKnife) (#307)

## [1.6.1 - 11.06.2025]

### Fixed
* Fixed "Request has been cancelled due to security reasons", cause of missing csrf() in twig files (https://github.com/slawkens/myaac/commit/10cd71a6630ffec91b43a26a6d685b66c5836a6a)
* Fix: Ignore duplicated route exception (https://github.com/slawkens/myaac/commit/9d8e9d27bd87167d8d4005942a6af62bfe4c0892)

### Changed
* Move counter & visitors code before router (In case someone wants to include that info on page) (https://github.com/slawkens/myaac/commit/f78285030708ad3c74ab048711f73bbf3ee5281e)
* Set TinyMCE license key to gpl (Avoid warning message in browser console) (https://github.com/slawkens/myaac/commit/8d29fdb98b92dbc3d2853ef88a185c67036b4a77)

### Removed
* Remove deprecated TinyMCE plugin - template (https://github.com/slawkens/myaac/commit/309c1fb715b882e67cb673b1544a03befbf64a22)

## [1.6 - 03.06.2025]

### Added
* Add new setting/configurable: site_url, prevents domain spoofing (https://github.com/slawkens/myaac/commit/d8a6090be382c35c19117cfef964b594ed02b8d4)
* Add new account coins setting (https://github.com/slawkens/myaac/commit/28886551e86fe562172c4c7f2afb89a2e7672c2e)
* autoload: settings/install/init.php (https://github.com/slawkens/myaac/commit/e5749437074c3b3556628a2aeb5bad2edf97bde0, https://github.com/slawkens/myaac/commit/7d213f479a7e40c6254069b5fc4e578dc32bf8d9, https://github.com/slawkens/myaac/commit/207d6bc69120aba1af2b51808f17e0059b571fed)
* Protect against csrf in more places (accounts & guilds & forums pages) (https://github.com/slawkens/myaac/commit/6eda38603c8ed7e99b92a78a4600b1245377f74d, https://github.com/slawkens/myaac/commit/e776bd52beb3064a9e694efd1b9021ec972ee2f6, https://github.com/slawkens/myaac/commit/84d502bf105f2a789481fba1acc820d236b4de66)
* Added two new hooks for pages loaded from database (custom pages): HOOK_BEFORE_PAGE_CUSTOM, HOOK_AFTER_PAGE_CUSTOM (https://github.com/slawkens/myaac/commit/c961a1ebf837f2ab1734a825ff2c57b4937610c9)
* Add global variables into $hooks->executeFilter (https://github.com/slawkens/myaac/commit/8fdea943768b20193eede99d60313ee84511a0be)
* Add getNPCsCount() to OTS_InfoRespond (https://github.com/slawkens/myaac/commit/7d435ff6433ef1fb2295ee79ed043ee10dc725e9)

### Fixed
* Allow [] in character name (https://github.com/slawkens/myaac/commit/de6603a51347b9e656c58637ed9971fffdd7cedd)
* Do not allow access to tools/ folder after install (https://github.com/slawkens/myaac/commit/6e0f5913831f8dba69fd2d1505be3e2a303c6324)
* Fix CHANGELOG-1.x.md loading in admin panel (https://github.com/slawkens/myaac/commit/4a30fb495dbfbe1d434e8d52419eaf44fe517aee)
* Fix links not working in admin dashboard modules (https://github.com/slawkens/myaac/commit/be7b27c31aa3bbd6c0289c34d1e61139a3fe015c)
* Fix twig variables: logged + account_logged being not set directly after login (https://github.com/slawkens/myaac/commit/1e9b10d6489c488cadf7f6ed17b42f1ea6c767a8)

### Changed
* OTS_ServerInfo -> move setTimeout out of class - Possibility to use the class without MyAAC (https://github.com/slawkens/myaac/commit/40d65a6613149fda51bdceb82c807e5301a3388b)

## [1.5 - 14.05.2025]

### Added
* Feature/twig hooks filters (#258)
* Add latest client versions (14.00 - 15.01) (https://github.com/slawkens/myaac/commit/5367df23812c6182863353c9a39fd7fb0b743f4b)
* db variable to twig (https://github.com/slawkens/myaac/commit/5ed1aec28e146b871a75597411d12e42a067f4e6)
* New filter: HOOK_FILTER_ROUTES (https://github.com/slawkens/myaac/commit/9b75011224f385db8b27e109bfeb28e75b9d779c)
* Allow optionally separate folder for views (thanks @Scrollog for idea) (https://github.com/slawkens/myaac/commit/03e275213901a89edb0ebb8974b776a992ab391f)
* Add float & double types to the Settings (https://github.com/slawkens/myaac/commit/67ab425bb9796d9d123296e3fda542fa8f7f05ee)
* Add optional param _page_only for single-page apps etc. (https://github.com/slawkens/myaac/commit/113473f2560aab6d364c301cc14a8b5ba8f309f4)

### Changed
* Change OTS_Account->getPremDays to not return -1 in case of freePremium (https://github.com/slawkens/myaac/commit/3befde2a1e4d24a011311e785f15185db57e19b8)
* Add note about highscores being updated x minutes + allow ttl 0 to disable cache (https://github.com/slawkens/myaac/commit/a161cff00329da6f970f3a70967fe8346fe92bbc)
* Better monster images (no image not found anymore) + use cache (https://github.com/slawkens/myaac/commit/73a5829974ceca3f02d7925d5cfbd5fa50b1bbd2)
* Rename server-info -> ots-info, changelog -> change-log (Due to conflict with apache2 server-info mod) (https://github.com/slawkens/myaac/commit/3949d84e5d7631f332111b6d00278bddbd0ad10a)
* Move rules page to admin panel (https://github.com/slawkens/myaac/commit/3949d84e5d7631f332111b6d00278bddbd0ad10a)

### Fixed
* php 8.4 warnings
* Visitors counter not working properly on dev mode (https://github.com/slawkens/myaac/commit/da151051186c913dd0dd091aabe893649c2b9ee7)
* Fix login.php boosted creature & boss (not sure exact version, but should be 14.12 or around) (https://github.com/slawkens/myaac/commit/c48b8006319f6c3b5f082befd16785420bb98110)
* Fix installMenus when theme/template was removed from disc (https://github.com/slawkens/myaac/commit/c24c580796bccd54bf9e95b864763f4642684d55)
* Fix if user removes the menu category (https://github.com/slawkens/myaac/commit/dbea69f31478391dacfbbc02c8353c39b4245daf)

### Updated:
* Update cypress from version ^13.17.0 to ^14.3.3 (https://github.com/slawkens/myaac/commit/629fd18ea166860d5898a822f44f9277da6ce43d)

## [1.4 - 22.04.2025]

### Added
* feat: admin-pages (can add admin pages through plugins) (https://github.com/slawkens/myaac/commit/ceaa0639e66d31e8177ff90791463470367aa45d)
	* just place the page in admin-pages folder in the plugin
	* Also, possibility to overwrite default myaac admin pages
* Add db->hasTableAndColumns(table, columns), credits to @opentibiabr Team (https://github.com/slawkens/myaac/commit/82a533d88c8a342076891d132b4b409ed9a1fe72)
* Add noSubmit option to buttons.base (https://github.com/slawkens/myaac/commit/64f6d3abcada3bf9fd7599f50d2fac0a1367f383)

### Fixed
* Fix: display 404 error instead of 500 when page has been removed from filesystem (https://github.com/slawkens/myaac/commit/c2bf94fb2370d2009a2eb907f818955132cf8611)
* Fix headline.php: change image format to .png cause of black background (https://github.com/slawkens/myaac/commit/b618084d50918539d9a70abd97e764137b966067)
* Clear cache on plugin enable/disable, fixes some issues with plugin pages being cached (https://github.com/slawkens/myaac/commit/1d0c173e7d000aecbd432800941fc3e38a0e50f2)
* Do not autoload sub-folders if autoload pages is disabled (https://github.com/slawkens/myaac/commit/d47195a7878095336f9c9edc6f96244257f67eec)

### Changed
* SQL Syntax Standardization (by @JoaozinhoBrasil, #298)
* Pages in theme/template folder will now have precedence over normal pages (https://github.com/slawkens/myaac/commit/6d8f4718a1d349fba8f0ebc39cfd3a1a84d104b0)
* Small changes in account.login.html.twig (https://github.com/slawkens/myaac/commit/f40b986b59d4c8fa89ab4745731bf366f8619976)
* Plugin name is required, version is optional (https://github.com/slawkens/myaac/commit/e6f05a2731c61d931be49e121c068e49c0ad5e01)

## [1.3.3 - 04.04.2025]

### Fixed
* Fix uninstall plugin when plugin is disabled (https://github.com/slawkens/myaac/commit/6c568fd36a271270684fc412ccd556b230273a6d)

### Changed
* Display more useful info when error parsing config.lua (https://github.com/slawkens/myaac/commit/fa6b6aa153ffc131e0d1631a4dcd9012a5850c2e)

### Other
* Small adjustments (https://github.com/slawkens/myaac/commit/35e2483de86e295bdf089cceffa25842eeb2e34c, https://github.com/slawkens/myaac/commit/ae639d65b0bfa491e747e907e2ebc77f83f47981)

## [1.3.2 - 01.04.2025]

### Fixed
* Fix debugBar/admin panel menu when using custom base_dir (https://github.com/slawkens/myaac/commit/65696f63e3aac02ff952ea81279e7cb2fa7570fb)

### Changed
* Settings: Show/hide IP Ban Protection options depending on the value (enabled/disabled) (https://github.com/slawkens/myaac/commit/dbf73d0b61b45601ae95e51b23c051c2704169c5)
* Do not require init.php in cache:clear command (https://github.com/slawkens/myaac/commit/d25c71857f767834239bbffacd00fdc671adb157)

## [1.3.1 - 19.03.2025]

### Fixed
* Fixed migrate:run command (https://github.com/slawkens/myaac/commit/1a5771ad51e595fe13368a0721b059c4ecefb17d)

### Changed
* Small adjustments (https://github.com/slawkens/myaac/commit/6fac883659f581baac1361826d046410156f1e58, https://github.com/slawkens/myaac/commit/4a6896b4469968b9904292734cf6c14ba5eeef14)

## [1.3 - 10.03.2025]

### Changed
* Use latest outfit-images host from @gesior (https://github.com/slawkens/myaac/commit/529bdcf016dd0f9dffbc34d81f99a046a9ddb70d)
* Change monster link to $_GET ?name= (https://github.com/slawkens/myaac/commit/4c5cc8b573b2b3e7ec00a22b7ede30a68083a924)

### Fixed
* Fixed house links (https://github.com/slawkens/myaac/commit/887b5068ad11c4cdab614afd34525caba785ce13)
* Fixed long title on headline.php (https://github.com/slawkens/myaac/commit/3e3f4bb5a514158ec8777684ca6c7f1c2a37bed5)
* Fixed menu colors once again, plus add !important tag (https://github.com/slawkens/myaac/commit/aa52df6e2ec92cafc25b655ae907bf2e1746d9cc)
* Fix: add possibility to remove all menu items in admin panel (https://github.com/slawkens/myaac/commit/00fe1adc15ea7646596d755f6e6e1f7854ffc1d5, https://github.com/slawkens/myaac/commit/9239a4f4198c3ad260802ac3b47e9c41b80b754e)

## [1.2 - 09.02.2025]

### Added
* Twig session(key) function + reworked session functions to accept multi-array like in Laravel (https://github.com/slawkens/myaac/commit/b46ddb43d03ef7e5fc34e555e92e856bdc905691)
* add template_name to twig variables (https://github.com/slawkens/myaac/commit/ae1161d77050bda181802b4496c9de920a7bb1bc)
* add HOOK_INIT, executed just after $hooks are loaded (https://github.com/slawkens/myaac/commit/19686725dc810f63a07f049f82c66cf336d90ca6)

### Changed
* settings: password input hide/show, enable Save button only if changes has been made, save settings in transaction (https://github.com/slawkens/myaac/commit/4fda4f643b60a151179e5dd4f04912fb2618d98f, https://github.com/slawkens/myaac/commit/28fef952f857b79d64bc7495ffa5e1999e68e192, https://github.com/slawkens/myaac/commit/4b6024dc451accadb6c469fa282a9a764c1c0a81)
* rework menus: Different categories can have different colors + Option to reset menus (https://github.com/slawkens/myaac/commit/73de93a561f6b13111e019075724357d8a617249, https://github.com/slawkens/myaac/commit/3da3e62c5b12390d75de9b3320729bcca6e0b458)

### Fixed
* highscores: Fix online status + vocation for TFS 0.x (https://github.com/slawkens/myaac/commit/ea51ad27c38be88d86514cb979bb394fcfbef1f0)
* clear cache button in admin bar needed to be clicked twice until it worked (https://github.com/slawkens/myaac/commit/ea51ad27c38be88d86514cb979bb394fcfbef1f0)
* HOOK_STARTUP location (https://github.com/slawkens/myaac/commit/a73fb1003ee3f812cf182d1834d65f08e6f60d1f)
* if vocation name has more words (https://github.com/slawkens/myaac/commit/9d7fc98e1e0a96b59ecc1a7c39800a64445db364)

### Updated
* Bump twig/twig from 3.18.0 to 3.19.0 (#284)

## [1.1 - 27.01.2025]

### Changed
* adjust mailer settings descriptions to latest gmail (https://github.com/slawkens/myaac/commit/c5d5bb80671db135e6b503f53684771c7272e05d)
* optimize $player->isOnline() function, thanks @gesior (https://github.com/slawkens/myaac/commit/10dd818b139d5e1bb1ca9ec81edfb083ba9316b4)
* make players.comment and guilds.description VARCHAR (https://github.com/slawkens/myaac/commit/a45ceab83a74bee2b89cdb72baceda75e577e3cf)
* add lua/ folder to .gitignore (https://github.com/slawkens/myaac/commit/07012f786b1114cb6ab2f064f82c645b136a375a)

### Fixed
* general fixes in the tibiacom template menus, better support for custom menus
* make functions_custom.php optional (https://github.com/slawkens/myaac/commit/dc2b5afd9980984e2b259c9fc99f2ade46f70a5a)
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
