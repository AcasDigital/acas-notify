#!/bin/bash
cd /var/www/html
FILE="/var/log/git_pull.log"
cmd_output=$(/usr/bin/git pull origin master 2>&1)
echo $cmd_output >> $FILE