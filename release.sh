#!/bin/bash
if (( "$#" != 1 )) 
then
	echo -e "Please provide a commit message"
	exit 1
fi
msg="$*"
cd /var/www/html
file=".git/index.lock"
if [ -f $file ] ; then
    rm -f $file
fi
cmd_output=$(/usr/bin/git add . 2>&1)
cmd_output=$(/usr/bin/git commit -m "$msg" 2>&1)
if [[ $cmd_output = *"nothing to commit"* ]]; then
	echo -e "Nothing to commit, working directory clean\n"
	exit 1
fi
echo -e $cmd_output\n
cmd_output=$(/usr/bin/git push origin master 2>&1)
echo -e $cmd_output\n
cmd_output=$(/usr/bin/ssh -i /home/ubuntu/Acas-dev.pem ubuntu@34.243.107.7 'cd /var/www/html; git pull origin master; composer update' 2>&1)
echo -e $cmd_output\n
echo -e "Finished release to UAT\n"