# [MyAAC](https://my-aac.org)

[![Build Status Master](https://img.shields.io/travis/slawkens/myaac/master)](https://travis-ci.org/github/slawkens/myaac)
[![License: GPL-3.0](https://img.shields.io/github/license/slawkens/myaac)](https://opensource.org/licenses/gpl-license)
[![Downloads Count](https://img.shields.io/github/downloads/slawkens/myaac/total)](https://github.com/slawkens/myaac/releases)
[![PHP Versions](https://img.shields.io/travis/php-v/slawkens/myaac/master)](https://github.com/slawkens/myaac/blob/d8b3b4135827ee17e3c6d41f08a925e718c587ed/.travis.yml#L3)
[![OpenTibia Discord](https://img.shields.io/discord/288399552581468162)](https://discord.gg/2J39Wus)
[![Closed Issues](https://img.shields.io/github/issues-closed-raw/slawkens/myaac)](https://github.com/slawkens/myaac/issues?q=is%3Aissue+is%3Aclosed)

MyAAC is a free and open-source Automatic Account Creator (AAC) written in PHP. It is a fork of the [Gesior](https://github.com/gesior/Gesior2012) project. It supports only MySQL databases.

### Requirements

	- PHP 5.6 or later
	- MySQL database
	- PDO PHP Extension
	- XML PHP Extension
	- ZIP PHP Extension
	- (optional) mod_rewrite to use friendly_urls

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
			chmod -R 770 system/cache

	Visit http://your_domain/install (http://localhost/install) and follow instructions in the browser.

### Configuration

Check *config.php* to get more informations.
Use *config.local.php* for your local configuration changes.

### Branches

This repository follows the Git Flow Workflow.
Cheatsheet: [Git-Flow-Cheetsheet](https://danielkummer.github.io/git-flow-cheatsheet)

That means, we use:
* master branch, for current stable release
* develop branch, for development version (next release)
* feature branches, for features etc.

### Known Problems

- Some compatibility issues with some exotical distibutions.


### Contributing

Contributions are more than welcome. 

Pull requests should be made to the Dev branch as that is the working branch, master is for release code.  

Look: [Contributing](https://github.com/otsoft/myaac/wiki/Contributing) in our wiki.

### Other Notes

If you have a great idea or want contribute to the project - visit our website at https://www.my-aac.org

### License

This program and all associated files are released under the GNU Public License.  
See [LICENSE](https://github.com/slawkens/myaac/blob/master/LICENSE) for details.
