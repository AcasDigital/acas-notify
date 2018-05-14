#!/bin/bash
if (( "$#" != 1 )) 
then
	echo "Please provide a commit message"
exit 1
fi
eval msg="$1"
cd /var/www/html
file=".git/index.lock"
if [ -f $file ] ; then
    rm -f $file
fi
cmd_output=$(/usr/bin/git add . 2>&1)
cmd_output=$(/usr/bin/git commit -m $msg . 2>&1)
echo $cmd_output