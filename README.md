# [MyAAC](https://my-aac.org)

MyAAC is a free and open-source Automatic Account Creator (AAC) for Open Tibia Servers written in PHP. It is a fork of the [Gesior](https://github.com/gesior/Gesior2012) project. It supports only MySQL databases.

Official website: https://my-aac.org

[![GitHub Workflow Status (with event)](https://img.shields.io/github/actions/workflow/status/slawkens/myaac/cypress.yml)](https://github.com/slawkens/myaac/actions)
[![License: GPL-3.0](https://img.shields.io/github/license/slawkens/myaac)](https://opensource.org/licenses/gpl-license)
[![Downloads Count](https://img.shields.io/github/downloads/slawkens/myaac/total)](https://github.com/slawkens/myaac/releases)
[![OpenTibia Discord](https://img.shields.io/discord/288399552581468162)](https://discord.gg/2J39Wus)
[![Closed Issues](https://img.shields.io/github/issues-closed-raw/slawkens/myaac)](https://github.com/slawkens/myaac/issues?q=is%3Aissue+is%3Aclosed)

| Version | Status                 | Branch  | Requirements   |
|:--------|:-----------------------|:--------|:---------------|
| 2.x     | Experimental features  | develop | PHP >= 8.1     |
| **1.x** | **Active development** | main    | **PHP >= 8.1** |
| 0.9.x   | Not developed anymore  | 0.9     | PHP >= 7.2.5   |
| 0.8.x   | Active support         | 0.8     | PHP >= 7.2.5   |
| 0.7.x   | End Of Life            | 0.7     | PHP >= 5.3.3   |

The recommended version to install is 1.x, which can be found at releases page - [https://github.com/slawkens/myaac/releases](https://github.com/slawkens/myaac/releases).

### Documentation
* [docs.my-aac.org](https://docs.my-aac.org)
* [my-aac.org - FAQ](https://my-aac.org/faqs/)

### Requirements

	- MySQL database
	- PHP Extensions: pdo, xml, json
	- (optional) apache2 mod_rewrite (to use friendly_urls)
	- (optional) zip PHP Extension (to install plugins)
	- (optional) gd PHP Extension (for generating signature images)

### Installation

	Just decompress and untar the source (which you should have done by now,
	if you're reading this), into your webserver's document root.

	MyAAC needs proper permissions to handle files correctly.
	If you're using apache2, then your directory needs to have owner set to: www-data, you can do it by using following command:
		chown -R www-data.www-data /var/www/*
			(or any other path your MyAAC installation is located at..)

	  Note: Linux only
		If you're under linux use these commands to set proper permissions:
			chmod 660 config.local.php
			chmod 660 images/guilds
			chmod 660 images/houses
			chmod 660 images/gallery
			chmod -R 760 system/cache

	Visit http://your_domain/install (http://localhost/install) and follow instructions in the browser.

### Configuration

Check *config.php* to get more information. (Notice: MyAAC 1.0+ doesn't use config.php anymore, it has been moved to Admin Panel - Settings page).

Use *config.local.php* for your local configuration changes.

### Branches

This repository follows the Git Flow Workflow.
Cheatsheet: [Git-Flow-Cheatsheet](https://danielkummer.github.io/git-flow-cheatsheet)

That means, we use:
* main branch, for current stable release
* develop branch, for development version (next release)
* feature branches, for features etc.

### Known Problems

- Some compatibility issues with some exotic distributions.

### Contributing

Contributions are more than welcome. 

Pull requests should be made to the *develop* branch as that is the working branch, master is for release code.  

Bug fixes to current release should be done to master branch.

Look: [Contributing](https://docs.my-aac.org/misc/contributing) in our wiki.

### Other Notes

If you have a great idea or want to contribute to the project - visit our website at https://www.my-aac.org

## Project supported by JetBrains

Many thanks to Jetbrains for kindly providing a license for me to work on this and other open-source projects.

[![JetBrains](https://resources.jetbrains.com/storage/products/company/brand/logos/jb_beam.svg)](https://www.jetbrains.com/?from=https://github.com/slawkens)

### License

This program and all associated files are released under the GNU Public License.  
See [LICENSE](https://github.com/slawkens/myaac/blob/main/LICENSE) for details.
