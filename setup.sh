#!/bin/bash

# installion script

cmd=$1

user=`grep dbuser service.conf | cut -f2 -d' '`
pswd=`grep dbpswd service.conf | cut -f2 -d' '`

target_dir='/var/www/html'
#target_dir=$HOME/public_html

case $cmd in

install)
	echo "Installing"

	echo "Creating tables"
	mysql -u $user -p$pswd < db/create.sql

	echo "Inserting values"
	mysql -u $user -p$pswd < db/inserts.sql

	echo "Running procedures"
	mysql -u $user -p$pswd < analysis/cohorts.sql
	

	mkdir -p "$target_dir/MyApp"
	cp -rf web/* "$target_dir/MyApp"

	echo "done!"
	;;

uninstall)
	echo "Uninstalling"
	
	mysql -u $user -p$pswd -e "DROP DATABASE Group9db;" 
	rm -rf "target_dir/MyApp"

	echo "done!"
	;;

run)
	echo "Running"
	R CMD BATCH analysis/AttributeImportance.R 
	cat analysis.Rout
	rm analysis.Rout
	;;

*)
	echo "Unknown Command!"

esac
