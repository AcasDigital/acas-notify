#!/bin/bash
cd /var/www/html
FILE="/var/log/update.log"
cmd_output=$(/usr/local/bin/composer update 2>&1)
echo $cmd_output >> $FILE
if [[ $cmd_output = *"Nothing to install or update"* ]]; then
  exit
fi
cmd_output=$(/usr/local/bin/drush -y --root=/var/www/html updb 2>&1)
echo $cmd_output >> $FILE
cmd_output=$(/usr/local/bin/drush --root=/var/www/html cr 2>&1)
echo $cmd_output >> $FILE