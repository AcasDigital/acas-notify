#!/bin/bash
cd /var/www/html
file="DEV"
if [ ! -f $file ] ; then
    echo -e "This script can only be run on DEV!"
	exit 1
fi
cmd_output=$(/usr/bin/ssh -i /home/ubuntu/Acas-tell.pem ubuntu@52.49.14.223 'cd /var/www/html; git pull origin master' 2>&1)
if [[ $cmd_output = *"index.lock': File exists"* ]]; then
	cmd_output=$(/usr/bin/ssh -i /home/ubuntu/Acas-tell.pem ubuntu@52.49.14.223 'rm -f /var/www/html/.git/index.lock' 2>&1)
	cmd_output=$(/usr/bin/ssh -i /home/ubuntu/Acas-tell.pem ubuntu@52.49.14.223 'cd /var/www/html; git pull origin master' 2>&1)
fi
if [[ $cmd_output = *"Your local changes to the following files would be overwritten by merge"* ]]; then
	echo -e "\n\e[0;31m*** WARNING Git Pull on PROD failed ***\e[00m\n"
	echo $cmd_output
	exit 1
fi
echo $cmd_output
echo -e "\nRunning 'composer update', 'drush updb' and 'drush cr' on PROD. Please wait...\n"
cmd_output=$(/usr/bin/ssh -i /home/ubuntu/Acas-tell.pem ubuntu@52.49.14.223 'cd /var/www/html; composer update; drush updb; drush cr' 2>&1)
echo $cmd_output
echo -e "\n\e[1;32mFinished release to PROD :)\e[00m\n"
