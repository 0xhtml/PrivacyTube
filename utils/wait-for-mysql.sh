#!/bin/bash
# Credit: https://github.com/DreamItGetIT/wait-for-mysql

echo "Waiting for mysql"
until mysql -h"$mysql_host" -P"3306" -u"$mysql_user" -p"$mysql_pass" &> /dev/null
do
  sleep 1
done

echo -e "mysql ready"
