#!/bin/sh
if [[ `echo "$1" | sed -r "s/[0-9]+\.[0-9]+\.[0-9]+\.[0-9]+/match/g"` == "match" ]]
then
	sed -i -r "s/[0-9]+\.[0-9]+\.[0-9]+\.[0-9]+/$1/g" updater.php
	sed -i -r "s/[0-9]+\.[0-9]+\.[0-9]+\.[0-9]+/$1/g" download.html
	sed -i -r "s/[0-9]+\.[0-9]+\.[0-9]+\.[0-9]+/$1/g" official-server-org.html
	sed -i -r "s/[0-9]+\.[0-9]+\.[0-9]+\.[0-9]+/$1/g" shop/index.html
	sed -i -r "s/[0-9]+\.[0-9]+\.[0-9]+\.[0-9]+/$1/g" official-server.php
	echo 'update done'
else
	echo "no version compatible passed: $1"
fi
