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

	mysql -u $user -p$pswd < db/create2.sql
	mysql -u $user -p$pswd < db/inserts2.sql
	

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
	R CMD BATCH analysis/analysis.R 
	cat analysis.Rout
	rm analysis.Rout
	;;

*)
	echo "Unknown Command!"

esac
