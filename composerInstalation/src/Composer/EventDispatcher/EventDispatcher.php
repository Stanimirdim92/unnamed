<?php











namespace Composer\EventDispatcher;

use Composer\DependencyResolver\PolicyInterface;
use Composer\DependencyResolver\Pool;
use Composer\DependencyResolver\Request;
use Composer\Installer\InstallerEvent;
use Composer\IO\IOInterface;
use Composer\Composer;
use Composer\DependencyResolver\Operation\OperationInterface;
use Composer\Repository\CompositeRepository;
use Composer\Script;
use Composer\Script\CommandEvent;
use Composer\Script\PackageEvent;
use Composer\Util\ProcessExecutor;














class EventDispatcher
{
protected $composer;
protected $io;
protected $loader;
protected $process;
protected $listeners;








public function __construct(Composer $composer, IOInterface $io, ProcessExecutor $process = null)
{
$this->composer = $composer;
$this->io = $io;
$this->process = $process ?: new ProcessExecutor($io);
}









public function dispatch($eventName, Event $event = null)
{
if (null == $event) {
$event = new Event($eventName);
}

return $this->doDispatch($event);
}











public function dispatchScript($eventName, $devMode = false, $additionalArgs = array(), $flags = array())
{
return $this->doDispatch(new Script\Event($eventName, $this->composer, $this->io, $devMode, $additionalArgs, $flags));
}










public function dispatchPackageEvent($eventName, $devMode, OperationInterface $operation)
{
return $this->doDispatch(new PackageEvent($eventName, $this->composer, $this->io, $devMode, $operation));
}











public function dispatchCommandEvent($eventName, $devMode, $additionalArgs = array(), $flags = array())
{
return $this->doDispatch(new CommandEvent($eventName, $this->composer, $this->io, $devMode, $additionalArgs, $flags));
}














public function dispatchInstallerEvent($eventName, PolicyInterface $policy, Pool $pool, CompositeRepository $installedRepo, Request $request, array $operations = array())
{
return $this->doDispatch(new InstallerEvent($eventName, $this->composer, $this->io, $policy, $pool, $installedRepo, $request, $operations));
}











protected function doDispatch(Event $event)
{
$listeners = $this->getListeners($event);

$return = 0;
foreach ($listeners as $callable) {
if (!is_string($callable) && is_callable($callable)) {
$event = $this->checkListenerExpectedEvent($callable, $event);
$return = false === call_user_func($callable, $event) ? 1 : 0;
} elseif ($this->isPhpScript($callable)) {
$className = substr($callable, 0, strpos($callable, '::'));
$methodName = substr($callable, strpos($callable, '::') + 2);

if (!class_exists($className)) {
$this->io->write('<warning>Class '.$className.' is not autoloadable, can not call '.$event->getName().' script</warning>');
continue;
}
if (!is_callable($callable)) {
$this->io->write('<warning>Method '.$callable.' is not callable, can not call '.$event->getName().' script</warning>');
continue;
}

try {
$return = false === $this->executeEventPhpScript($className, $methodName, $event) ? 1 : 0;
} catch (\Exception $e) {
$message = "Script %s handling the %s event terminated with an exception";
$this->io->write('<error>'.sprintf($message, $callable, $event->getName()).'</error>');
throw $e;
}
} else {
$args = implode(' ', array_map(array('Composer\Util\ProcessExecutor','escape'), $event->getArguments()));
if (0 !== ($exitCode = $this->process->execute($callable . ($args === '' ? '' : ' '.$args)))) {
$this->io->write(sprintf('<error>Script %s handling the %s event returned with an error</error>', $callable, $event->getName()));

throw new \RuntimeException('Error Output: '.$this->process->getErrorOutput(), $exitCode);
}
}

if ($event->isPropagationStopped()) {
break;
}
}

return $return;
}






protected function executeEventPhpScript($className, $methodName, Event $event)
{
$event = $this->checkListenerExpectedEvent(array($className, $methodName), $event);

return $className::$methodName($event);
}






protected function checkListenerExpectedEvent($target, Event $event)
{
if (!$event instanceof Script\Event) {
return $event;
}

try {
$reflected = new \ReflectionParameter($target, 0);
} catch (\Exception $e) {
return $event;
}

$typehint = $reflected->getClass();

if (!$typehint instanceof \ReflectionClass) {
return $event;
}

$expected = $typehint->getName();

if (!$event instanceof $expected && $expected === 'Composer\Script\CommandEvent') {
$event = new CommandEvent($event->getName(), $event->getComposer(), $event->getIO(), $event->isDevMode(), $event->getArguments());
}

return $event;
}








protected function addListener($eventName, $listener, $priority = 0)
{
$this->listeners[$eventName][$priority][] = $listener;
}








public function addSubscriber(EventSubscriberInterface $subscriber)
{
foreach ($subscriber->getSubscribedEvents() as $eventName => $params) {
if (is_string($params)) {
$this->addListener($eventName, array($subscriber, $params));
} elseif (is_string($params[0])) {
$this->addListener($eventName, array($subscriber, $params[0]), isset($params[1]) ? $params[1] : 0);
} else {
foreach ($params as $listener) {
$this->addListener($eventName, array($subscriber, $listener[0]), isset($listener[1]) ? $listener[1] : 0);
}
}
}
}







protected function getListeners(Event $event)
{
$scriptListeners = $this->getScriptListeners($event);

if (!isset($this->listeners[$event->getName()][0])) {
$this->listeners[$event->getName()][0] = array();
}
krsort($this->listeners[$event->getName()]);

$listeners = $this->listeners;
$listeners[$event->getName()][0] = array_merge($listeners[$event->getName()][0], $scriptListeners);

return call_user_func_array('array_merge', $listeners[$event->getName()]);
}







public function hasEventListeners(Event $event)
{
$listeners = $this->getListeners($event);

return count($listeners) > 0;
}







protected function getScriptListeners(Event $event)
{
$package = $this->composer->getPackage();
$scripts = $package->getScripts();

if (empty($scripts[$event->getName()])) {
return array();
}

if ($this->loader) {
$this->loader->unregister();
}

$generator = $this->composer->getAutoloadGenerator();
$packages = $this->composer->getRepositoryManager()->getLocalRepository()->getCanonicalPackages();
$packageMap = $generator->buildPackageMap($this->composer->getInstallationManager(), $package, $packages);
$map = $generator->parseAutoloads($packageMap, $package);
$this->loader = $generator->createLoader($map);
$this->loader->register();

return $scripts[$event->getName()];
}







protected function isPhpScript($callable)
{
return false === strpos($callable, ' ') && false !== strpos($callable, '::');
}
}
