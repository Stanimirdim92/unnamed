# CHANGELOG

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
 - Improved|Refactored Admin\*\* - termtranslation - WIP
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

# TODO
 - Database translations in combination with I18n
 - After/In Beta version start creating the first PHPUnit and Selenium tests
 - *->fetchJoin should access 'n' number of tables and join them (array with tables info passed via foreach maybe?)
 - WAI-ARIA Landmarks
 - Better SEO
 - Social networks controller or module
 - Options controller, which will handle all the options across the CMS
 - Destroyers for all cached variables and queries, when new data is set
 - Finish the AJAX gallery and implement it as LearnZF2 module
 - Improve main Javascript AJAX function
 - Better http://schema.org/
 - Twitter cards
 - Win 8 default png tile cms image
 - Voice Search
