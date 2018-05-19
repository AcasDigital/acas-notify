#!/bin/bash
cd /var/www/html
FILE="/var/log/git_pull.log"
cmd_output=$(sudo -u ubuntu /usr/bin/git pull origin master 2>&1)
if [[ $cmd_output = *"composer.lock"* ]]; then
  echo $cmd_output >> $FILE
  cmd_output=$(sudo -u ubuntu rm -f /var/www/html/composer.lock 2>&1)
  cmd_output=$(sudo -u ubuntu /usr/bin/git pull origin master 2>&1)
fi
echo $cmd_output >> $FILE
if [[ $cmd_output = *"Already up-to-date"* ]]; then
  exit
fi
cmd_output=$(sudo -u ubuntu /usr/local/bin/composer update 2>&1)
echo $cmd_output >> $FILE