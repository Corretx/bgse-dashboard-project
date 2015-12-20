#!/bin/bash

# installion script

cmd=$1

user=`grep dbuser service.conf | cut -f2 -d' '`
pswd=`grep dbpswd service.conf | cut -f2 -d' '`

target_dir='/var/www/html'

case $cmd in

install)
	echo "Installing"
	
	echo 'Installing R libraries'
	mkdir /home/ubuntu/projects/Rlibs
	chmod 777 /home/ubuntu/projects/Rlibs
	echo R_LIBS=/home/ubuntu/projects/Rlibs > ~/.Renviron 
	echo R_LIBS_USER=/home/ubuntu/projects/Rlibs > ~/.Renviron
	#sudo Rscript --vanilla setup.R

	echo "Creating tables"
	mysql -u $user -p$pswd < db/create.sql

	echo "Inserting values"
	mysql -u $user -p$pswd < db/inserts.sql

	echo "Running procedures"
	mysql -u $user -p$pswd < analysis/cohorts.sql
	
	echo "Running R script"
	R CMD BATCH analysis/AttributeImportance.R
	rm AttributeImportance.Rout
	

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
	;;

*)
	echo "Unknown Command!"

esac
