#!/bin/bash
cd /var/www/html
file="DEV"
if [ ! -f $file ] ; then
    echo -e "This script can only be run on DEV!"
	exit 1
fi
if (( "$#" != 1 )) 
then
	echo -e "Please provide a commit message eg. 'fixed a load of stuff'"
	exit 1
fi
file=".git/index.lock"
if [ -f $file ] ; then
    rm -f $file
fi
cmd_output=$(/usr/bin/git add . 2>&1)
msg="$*"
cmd_output=$(/usr/bin/git commit -m "$msg" 2>&1)
if [[ $cmd_output = *"nothing to commit"* ]]; then
	echo -e "Nothing to commit, working directory clean"
	exit 1
fi
echo $cmd_output
cmd_output=$(/usr/bin/git push origin master 2>&1)
echo $cmd_output
cmd_output=$(/usr/bin/ssh -i /home/ubuntu/Acas-dev.pem ubuntu@34.243.107.7 'cd /var/www/html; git pull origin master' 2>&1)
if [[ $cmd_output = *"overwritten by merge: composer.lock"* ]]; then
	file="composer.lock"
	rm -f $file
	cmd_output=$(/usr/bin/ssh -i /home/ubuntu/Acas-dev.pem ubuntu@34.243.107.7 'cd /var/www/html; git pull origin master' 2>&1)
fi
echo $cmd_output
echo -e "\nRunning 'composer update' and 'drush cr' on UAT. Please wait...\n"
cmd_output=$(/usr/bin/ssh -i /home/ubuntu/Acas-dev.pem ubuntu@34.243.107.7 'cd /var/www/html; composer update; drush cr' 2>&1)
echo $cmd_output
echo -e "\nFinished release to UAT :)\n"

 