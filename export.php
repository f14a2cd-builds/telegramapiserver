<?php
/* env: 0 - exec, 1 - apiid, 2 - apihash, 3 - server_port, 4 - socks_proxy */
$excepted = ["api_id","api_hash"];
if (!in_array($excepted, $_ENV)) {
	echo('
Usage: docker run telegramapidocker [args]
Where args can be:
--api_id=(api_id) - Your API ID from my.telegram.org
--api_hash=(api_hash) - Your API hash from my.telegram.org
--listen=(listen port) - Port which you will listen it. This port should be correctly forwarded from docker
--socks=socks://(server):(port) - SOCKS proxy for connecting to Telegram (if applied)
');
exit(0);
}
foreach($_ENV as $args) {
	foreach($excepted as $req) {
		if (stripos($args,$req)) {
			$export[$req] = str_replace("--{$req}=","",$args);
		}
	}
}

if (!isset($export['listen'])) {echo "listen was not set. Setting it to '9537'".PHP_EOL; $export['listen'] = 9537;}
if (isset($export['socks'])) {
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
file_put_contents("/telegram/.env",$output);
echo "Saved configuration.";
exit(1);
