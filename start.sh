#!/bin/bash
 ID=`ps -ef | grep "php" | grep -v "grep" | awk '{print $2}'`
echo $ID
echo "---------------"
for id in $ID
do
kill -9 $id
echo "killed $id"
done
echo "---------------"
nohup php index.php &
