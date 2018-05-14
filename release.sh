#!/bin/bash
if (( "$#" != 1 )) 
then
	echo "Please provide a commit message"
	exit 1
fi
eval msg="$*"
cd /var/www/html
file=".git/index.lock"
if [ -f $file ] ; then
    rm -f $file
fi
cmd_output=$(/usr/bin/git add . 2>&1)
cmd_output=$(/usr/bin/git commit -m $msg 2>&1)
if [[ $cmd_output = *"nothing to commit"* ]]; then
	echo "Nothing to commit, working directory clean"
	exit 1
fi
echo $cmd_output
cmd_output=$(/usr/bin/git push origin master 2>&1)
echo $cmd_output
cmd_output=$(/usr/bin/ssh -i /home/ubuntu/Acas-dev.pem ubuntu@34.243.107.7 'cd /var/www/html; git pull origin master; composer update' 2>&1)
echo $cmd_output
echo "Finished release to UAT"