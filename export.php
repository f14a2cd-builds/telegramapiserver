<?php
/* env: 0 - exec, 1 - apiid, 2 - apihash, 3 - server_port, 4 - socks_proxy */

if (!isset($_SERVER['api_id'])||!isset($_SERVER['api_hash'])) {
	echo('
Usage: docker run -e api_id=(api_id from my.telegram.org) -e api_hash=(api_hash from my.telegram.org) telegramapidocker
Also, can be used -e socks=socks://(user):(password)@(address):(port) for connect via socks.
Username and password for proxy is not required if it\'s not set.
');
exit(0);
}
if (!file_exists("/telegram/sessions/lockfile")) {
	echo "Did you mounted your 'madeline session' directory?".PHP_EOL;
	echo "BE DANGEROUS! IF YOU DIDN'T MOUNTED DIRECTORY IN DOCKER IMAGE, YOU WILL LOSE YOUR SESSION AFTER TERMINATE OR SHUTDOWN THIS DOCKER IMAGE.".PHP_EOL;
	echo "DON'T BE LOSE! USE '-v' ARGUMENT WHEN RUN YOUR DOCKER IMAGE!".PHP_EOL;
	sleep(5);
	mkdir("/telegram/sessions/");
	file_put_contents("/telegram/sessions/lockfile","");
}
$export['api_id'] = $_SERVER['api_id'];
$export['api_hash'] = $_SERVER['api_hash'];
$export['listen'] = (isset($_SERVER['listen'])&&!empty($_SERVER['listen']))?$_SERVER['listen']:9537;
$export['socks'] = (isset($_SERVER['socks'])&&!empty($_SERVER['socks']))?$_SERVER['socks']:'';

if (!isset($export['listen'])) {echo "listen was not set. Setting it to '9537'".PHP_EOL; $export['listen'] = 9537;}
if (isset($export['socks'])&&!empty($export['socks'])) {
	$socks = parse_url($export['socks']);
	$export['telegram_proxy_address'] = $socks['host'];
	$export['telegram_proxy_port'] = $socks['port'];
	$export['telegram_proxy_username'] = $socks['username'];
	$export['telegram_proxy_password'] = $socks['password'];
	unset($export['socks']);
}

/* exporting */
$output = '';
foreach($export as $ename => $evalue) {
	$output .= strtoupper($ename)."=".$evalue.PHP_EOL;
}
chdir("/telegram");
$script_rev = "907f5c9b7233252f5238adbe99c9640fb2621384"; /* our rev */
if (str_replace(array(PHP_EOL,"\r","\n","\r\n"),"",shell_exec("git rev-parse HEAD"))!==$script_rev) {
	echo "Main git is out-of-date. Updating...".PHP_EOL;
	shell_exec("git pull");
}
else {
	echo "Everything is up-to-date".PHP_EOL;
}
file_put_contents("/telegram/.env",$output);
echo "Saved configuration.".PHP_EOL;
exit(1);
