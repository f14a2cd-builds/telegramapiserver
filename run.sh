#!/bin/bash
/usr/bin/php /tbin/export.php
if [[ $? -ne 1 ]]; then
	exit
else
	php /telegram/server.php
fi
