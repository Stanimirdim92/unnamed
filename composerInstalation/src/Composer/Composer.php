<?php











namespace Composer;

use Composer\Package\RootPackageInterface;
use Composer\Package\Locker;
use Composer\Repository\RepositoryManager;
use Composer\Installer\InstallationManager;
use Composer\Plugin\PluginManager;
use Composer\Downloader\DownloadManager;
use Composer\EventDispatcher\EventDispatcher;
use Composer\Autoload\AutoloadGenerator;






class Composer
{
const VERSION = '4ca9e602a9a0ef7639ad143b6366b71638c77029';
const BRANCH_ALIAS_VERSION = '1.0-dev';
const RELEASE_DATE = '2015-01-16 20:51:31';




private $package;




private $locker;




private $repositoryManager;




private $downloadManager;




private $installationManager;




private $pluginManager;




private $config;




private $eventDispatcher;




private $autoloadGenerator;





public function setPackage(RootPackageInterface $package)
{
$this->package = $package;
}




public function getPackage()
{
return $this->package;
}




public function setConfig(Config $config)
{
$this->config = $config;
}




public function getConfig()
{
return $this->config;
}




public function setLocker(Locker $locker)
{
$this->locker = $locker;
}




public function getLocker()
{
return $this->locker;
}




public function setRepositoryManager(RepositoryManager $manager)
{
$this->repositoryManager = $manager;
}




public function getRepositoryManager()
{
return $this->repositoryManager;
}




public function setDownloadManager(DownloadManager $manager)
{
$this->downloadManager = $manager;
}




public function getDownloadManager()
{
return $this->downloadManager;
}




public function setInstallationManager(InstallationManager $manager)
{
$this->installationManager = $manager;
}




public function getInstallationManager()
{
return $this->installationManager;
}




public function setPluginManager(PluginManager $manager)
{
$this->pluginManager = $manager;
}




public function getPluginManager()
{
return $this->pluginManager;
}




public function setEventDispatcher(EventDispatcher $eventDispatcher)
{
$this->eventDispatcher = $eventDispatcher;
}




public function getEventDispatcher()
{
return $this->eventDispatcher;
}




public function setAutoloadGenerator(AutoloadGenerator $autoloadGenerator)
{
$this->autoloadGenerator = $autoloadGenerator;
}




public function getAutoloadGenerator()
{
return $this->autoloadGenerator;
}
}
