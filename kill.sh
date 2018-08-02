#!/bin/bash
systemctl stop firewalld.service
ID=`ps -ef | grep "php index.php" | grep -v "grep" | awk '{print $2}'`
echo $ID  
echo "---------------"  
for id in $ID
do
kill -9 $id
echo "killed $id"  
done
echo "---------------"
export PATH=$PATH:/usr/local/php/bin/
php index.php
