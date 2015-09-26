# README #

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/dbe0fb70-00d3-49ca-99b8-90dfcf688c2b/mini.png)](https://insight.sensiolabs.com/projects/dbe0fb70-00d3-49ca-99b8-90dfcf688c2b)
[![Build Status](https://semaphoreci.com/api/v1/projects/0613b317-95c1-4af3-8b73-4e1c99d7c8db/552367/shields_badge.svg)](https://semaphoreci.com/stanimir/unnamed)

This README would normally document whatever steps are necessary to get your application up and running.

### Unnamed CMS ###

* Building a new CMS on top of Zend Framework 2. Why? Because I can, it's fun and because the internet needs something better.
* Version 0.0.14
* Huge thanks to [Abdul Malik Ikhsan](https://samsonasik.wordpress.com/) a.k.a [samsonasik](https://twitter.com/samsonasik?lang=en) for being saw helpful and patient

### Requirements ###

* PHP 5.5.0+
* Extensions:
 - mcrypt - for encryption
 - mbstring - for proper string encoding
 - intl for translations
 - GD2 - for image manipulation
 - fileinfo for image mimetype detection
 - pdo_X for database driver
* Apache 2.4.4+ or nginx (nginx setup will be proveded later)
* Zend Framework 2.5+ will be installed from script

### Requirements for development ###

* APPLICATION_ENV must be set to development via .htaccess
* ZendDeveloperTools (integrated into the system)
* BjyProfiler (integrated into the system)
* SanSessionToolbar (integrated into the system)
* The PHP Coding Standards Fixer (integrated into the system)
* Gulp (requires NodeJS 0.12.7). node_modules folder is not included. You will have to run **npm install** from the app root directory

### LICENSE ###

The files in this project are released under the MIT license. You can find a copy of this license in LICENSE.md.

### Contacts ###

* For questions, send an email to stanimirdim92 at gmail dot com
