# CHANGELOG

DD.MM.YYYY
## 0.0.14 (25.09.2015) - Stable Alpha
 - Bug fixes from sensiolabs
 - Aded test favicon

DD.MM.YYYY
## 0.0.13 (23.09.2015) - Stable Alpha
 - Encapsulated all exceptions
 - Image library updates
 - *ModelTable bug and doc fixes
 - Removed isset magic property and getProperties() method from *Model classes
 - StyleCI fixes
 - New classmaps
 - Updated all composer packages
 - Renamed local.php to unnamed.local.php and deleted global.php
 - Fixed recursive menu not displaying all menus

DD.MM.YYYY
## 0.0.12 (14.09.2015) - Stable Alpha
 - SensioLabs reports 0 vulnerabilities
 - AJAX image gallery updates
 - Added Gulp
 - Updated layouts
 - New assets structure
 - Devide css styles based on their view ports
 - Add content author
 - Image manipulation library - WIP

DD.MM.YYYY
## 0.0.11 (01.09.2015) - Stable Alpha
 - Instalation script updates and bug fixes
 - Refactored menu logic. Made it multidimensonal. - Needs more testing
 - layout.phtml and style.css fixes
 - $this->view was moved to a function getView()
 - Removed AdminErrorHandler
 - Fixed registration factory class name and namespace
 - global.php now holds all global config options for all modules
 - Removed magic __set() and __get() methods
 - Improved SEO and main menu links
 - Updated font awesome to v 4.4.0
 - Add active/inactive post/menu

DD.MM.YYYY
## 0.0.10 (23.08.2015) - Stable Alpha
 - Upgraded php version to 5.5
 - Upgraded Zend to version 2.5.2
 - Created instalation script
 - Refactored init_autoloader.php
 - Added Vagrant file
 - Bug and security fixes

DD.MM.YYYY
## 0.0.7 (18.08.2015) - Stable Alpha
 - Removed translations view helper
 - Removed Term* files
 - Updated autoload_classmap and template_map
 - Improved routes config, but the config is still slow (10s on 1st request)
 - New cache folders
 - New Translator factory
 - Moved the translations to a php file that returns an array. Enabled the cache. System loads x4 times faster
 - All controllers and plugins except IndexController and ErrorHandler were made final. They are never ment to be extended
 - Updated ZF2 to 2.4.7
 - new translationsaAction in language controller. The action is used to edit/remove the translations files
 - Improved main Javascript AJAX function
 - Fixed addBreadCrumb order
 - Removed Wordpress IIS $_SERVER fixes
 - Added intl extension to requiarments
 - Better folders and file structure. Fixed some namespaces, created form elements factories ho handle dependencies instead of doing it via the controller
 - Created composer file to install zend and it's required files via the browser with one click. Thanks to Abdul Malik for the idea
 - Removed vendor folder
 - Removed CodePlex (for now)

DD.MM.YYYY
## 0.0.6 (08.08.2015) - Stable Alpha
 - Removed vendor/Custom folder. The code was moved to Application\Controller\Plugin and the exception to Exception folder
 - Added factories to controller plugins for better DI
 - Bug fixes and optimizations

DD.MM.YYYY
## 0.0.5 (07.08.2015) - Stable Alpha
 - Moved setLayoutMessages, getTable, setErrorCode, InitMetaTags, clearUserData to controller plugins
 - Completely disabled translations. At the moment the system returns the database term constant.
 - Removed san_Old
 - Removed some images used for testing
 - Bug fixes and optimizations as usual

DD.MM.YYYY
## 0.0.4 (06.08.2015) - Stable Alpha
 - Improved fetchList() and fetchJoin() functions
 - Created new translate function in IndexController and View Helper, which will handle all term translations
 - Improved|Refactored Admin\*\*
 - Improved|Refactored Application\*\*
 - Enabled module config cache
 - Removed /id path from url
 - Fixed module.config.php files
 - Refactored createPlainQueries.
 - Replaced $this->langTranslation with a function language()
 - Created getAdapter() in Functions.php
 - Removed initTranslations() from Functions.php and moved the refactored code to IndexController.
 - Improved and refactored setLayoutMessages function. Now it works with Spl iterators.
 - Removed $this->cache and $this->initCache()
 - Removed SM from controllers
 - initMetaTags() improvements
 - Removed initViewVars()
 - Fixed variable check in checkIdentity()
 - Deleted unused Params.php View Helper and AjaxSearchForm.php
 - Removed ServiceManager from all files
 - Refactored paination.phtml files
 - If user is logging in and is admin redirect to /admin
 - Updated .htaccess. Added/improved security headers. There is still some work to be done
 - Update ZF2, ZDT and SST modules to their latest versions
 - Updated autoload_classmap.php and created template_map.php
 - Replaces setErrorNoParam() with setLayoutMessages()
 - Renamed showForm() to initForm()
 - Removed CurrencyController
 - Created ContactController
 - Removed @category, @package
 - Removed EdpModuleLayouts code in favour for Module::init()
 - Removed salt, userClass, ban, username, country columns from database user table
 - UserController doesn't have add action and never will.
 - New enable|disableAction in UserController. Instead of deleting user accounts, we disable them
 - Fixed user export in excel file
 - Fixed session not being initiated. translation session no longer exists. It was replaced by a global session named zpc
 - Created new controller plugin IndexPlugin
 - Deactivated translations for now.
 - Most of the controller actions were made protected
 - Run composer from the browser - WIP

# TODO
 - ElasticSearch
 - After/In Beta version start creating the first PHPUnit and Selenium tests
 - *->fetchJoin should access 'n' number of tables and join them (array with tables info passed via foreach maybe?)
 - WAI-ARIA Landmarks
 - Better SEO
 - Social networks controller or module
 - Options controller, which will handle all the options across the CMS
 - Destroyers for all cached variables and queries, when new data is set
 - Better http://schema.org/
 - Twitter cards
 - Win 8 default png tile cms image
 - Voice Search
