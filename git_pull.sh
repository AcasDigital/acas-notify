#!/bin/bash
cd /var/www/html
FILE="/var/log/git_pull.log"
cmd_output=$(sudo -u ubuntu /usr/bin/git pull origin master 2>&1)
echo $cmd_output >> $FILE
cmd_output=$(sudo -u ubuntu /usr/local/bin/composer update 2>&1)
echo $cmd_output >> $FILE