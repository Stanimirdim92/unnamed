<?php











namespace Composer\Util;

use Composer\IO\IOInterface;
use Composer\Config;
use Composer\Downloader\TransportException;
use Composer\Json\JsonFile;




class GitHub
{
protected $io;
protected $config;
protected $process;
protected $remoteFilesystem;









public function __construct(IOInterface $io, Config $config, ProcessExecutor $process = null, RemoteFilesystem $remoteFilesystem = null)
{
$this->io = $io;
$this->config = $config;
$this->process = $process ?: new ProcessExecutor;
$this->remoteFilesystem = $remoteFilesystem ?: new RemoteFilesystem($io, $config);
}







public function authorizeOAuth($originUrl)
{
if (!in_array($originUrl, $this->config->get('github-domains'))) {
return false;
}


 if (0 === $this->process->execute('git config github.accesstoken', $output)) {
$this->io->setAuthentication($originUrl, trim($output), 'x-oauth-basic');

return true;
}

return false;
}










public function authorizeOAuthInteractively($originUrl, $message = null)
{
$attemptCounter = 0;

$apiUrl = ('github.com' === $originUrl) ? 'api.github.com' : $originUrl . '/api/v3';

if ($message) {
$this->io->write($message);
}
$this->io->write('The credentials will be swapped for an OAuth token stored in '.$this->config->getAuthConfigSource()->getName().', your password will not be stored');
$this->io->write('To revoke access to this token you can visit https://github.com/settings/applications');
while ($attemptCounter++ < 5) {
try {
if (empty($otp) || !$this->io->hasAuthentication($originUrl)) {
$username = $this->io->ask('Username: ');
$password = $this->io->askAndHideAnswer('Password: ');
$otp = null;

$this->io->setAuthentication($originUrl, $username, $password);
}


 $appName = 'Composer';
if ($this->config->get('github-expose-hostname') === true && 0 === $this->process->execute('hostname', $output)) {
$appName .= ' on ' . trim($output);
} else {
$appName .= ' [' . date('YmdHis') . ']';
}

$headers = array();
if ($otp) {
$headers = array('X-GitHub-OTP: ' . $otp);
}


 $contents = null;
$auths = JsonFile::parseJson($this->remoteFilesystem->getContents($originUrl, 'https://'. $apiUrl . '/authorizations', false, array(
'retry-auth-failure' => false,
'http' => array(
'header' => $headers
)
)));
foreach ($auths as $auth) {
if (
isset($auth['app']['name'])
&& 0 === strpos($auth['app']['name'], $appName)
&& $auth['app']['url'] === 'https://getcomposer.org/'
) {
$this->io->write('An existing OAuth token for Composer is present and will be reused');

$contents['token'] = $auth['token'];
break;
}
}


 if (empty($contents['token'])) {
$headers[] = 'Content-Type: application/json';

$contents = JsonFile::parseJson($this->remoteFilesystem->getContents($originUrl, 'https://'. $apiUrl . '/authorizations', false, array(
'retry-auth-failure' => false,
'http' => array(
'method' => 'POST',
'follow_location' => false,
'header' => $headers,
'content' => json_encode(array(
'scopes' => array('repo'),
'note' => $appName,
'note_url' => 'https://getcomposer.org/',
)),
)
)));
$this->io->write('Token successfully created');
}
} catch (TransportException $e) {
if (in_array($e->getCode(), array(403, 401))) {

 if ($this->io->hasAuthentication($originUrl)) {
$headerNames = array_map(function ($header) {
return strtolower(strstr($header, ':', true));
}, $e->getHeaders());

if ($key = array_search('x-github-otp', $headerNames)) {
$headers = $e->getHeaders();
list($required, $method) = array_map('trim', explode(';', substr(strstr($headers[$key], ':'), 1)));

if ('required' === $required) {
$this->io->write('Two-factor Authentication');

if ('app' === $method) {
$this->io->write('Open the two-factor authentication app on your device to view your authentication code and verify your identity.');
}

if ('sms' === $method) {
$this->io->write('You have been sent an SMS message with an authentication code to verify your identity.');
}

$otp = $this->io->ask('Authentication Code: ');

continue;
}
}
}

$this->io->write('Invalid credentials.');
continue;
}

throw $e;
}

$this->io->setAuthentication($originUrl, $contents['token'], 'x-oauth-basic');


 $this->config->getConfigSource()->removeConfigSetting('github-oauth.'.$originUrl);
$this->config->getAuthConfigSource()->addConfigSetting('github-oauth.'.$originUrl, $contents['token']);

return true;
}

throw new \RuntimeException("Invalid GitHub credentials 5 times in a row, aborting.");
}
}
