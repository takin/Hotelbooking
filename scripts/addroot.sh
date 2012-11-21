#!/bin/bash
#if [ "$1" = "VALEUR" ]
#then
#  echo $1
#else
#  echo "NULLa"

query1="CREATE USER 'root'@'$1' IDENTIFIED BY 'temp123';"
#echo $query1
query2="GRANT ALL PRIVILEGES ON *.* TO 'root'@'$1' IDENTIFIED BY 'temp123' WITH GRANT OPTION MAX_QUERIES_PER_HOUR 0 MAX_CONNECTIONS_PER_HOUR 0 MAX_UPDATES_PER_HOUR 0 MAX_USER_CONNECTIONS 0;"
#echo $query2
#DROP USER 'root'@'216.99._._';
mysql -u root -p  -e "$query1 $query2"
