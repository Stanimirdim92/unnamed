# README #

|   Info  |                                                   Badges                                                                                                                                                                                                                                                                                                                      |
|:-------:|:------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| Versions | [![PHP minimum version](https://img.shields.io/badge/php-%3E%3D5.5-8892BF.svg)](https://php.net/) [![Unnamed version](https://img.shields.io/badge/Unnamed-v0.0.15-8892BF.svg)](https://bitbucket.org/StanimirDim92/unnamed/overview) [![NPM minimum version](https://img.shields.io/badge/npm-v2.14.3-8892BF.svg)](https://www.npmjs.com/)                                                                                                                           |
| License | [![Unnamed license](https://img.shields.io/badge/license-MIT-blue.svg)](https://bitbucket.org/StanimirDim92/unnamed/raw/master/LICENSE)                                                                                                                                                                                                                                       |
| Testing | [![Build Status](https://semaphoreci.com/api/v1/projects/0613b317-95c1-4af3-8b73-4e1c99d7c8db/552367/shields_badge.svg)](https://semaphoreci.com/stanimir/unnamed)                                                                                                                                                                                                            |
| Quality | [![Dependency Status](https://www.versioneye.com/user/projects/5606e2075a262f00220000a9/badge.svg?style=flat)](https://www.versioneye.com/user/projects/5606e2075a262f00220000a9) [![SensioLabsInsight](https://insight.sensiolabs.com/projects/dbe0fb70-00d3-49ca-99b8-90dfcf688c2b/mini.png)](https://insight.sensiolabs.com/projects/dbe0fb70-00d3-49ca-99b8-90dfcf688c2b) |

### Unnamed CMS ###

* Building a new CMS on top of Zend Framework 2. Why? Because I can, it's fun and because the internet needs something better.
* Version 0.0.15
* Huge thanks to [Abdul Malik Ikhsan](https://samsonasik.wordpress.com/) a.k.a [samsonasik](https://twitter.com/samsonasik?lang=en) for being saw helpful and patient

### Current features ###

* Installation script in 2 steps!
* Login with reset password.
* Registration.
* Menu creations for front end and back end with font icons attachements.
* Content creations - can be attached to menus. Multiple contents can be attached to one menu.
* Add administrators.
* Translations with interface and active cache.
* AJAX gallery with image upload and validation.
* Contact form.
* SEO optimized.
* Accesibility optimized.
* User page with account disable option

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
* Gulp (requires NodeJS 0.12.7 or later). node_modules folder is not included. You will have to run **npm install** from the app root directory

### LICENSE ###

The files in this project are released under the MIT license. You can find a copy of this license in [LICENSE.md](https://bitbucket.org/StanimirDim92/unnamed/raw/master/LICENSE).

### Contacts ###

* For questions, send an email to stanimirdim92 at gmail dot com
