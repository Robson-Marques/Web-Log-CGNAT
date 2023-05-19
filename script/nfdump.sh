#!/bin/bash
DIA=$(date +%d)
TIPO="flow"
if [ $8 != $TIPO ]
then
if [ $4 -eq $DIA ]
then
consulta_ip=$(sudo cat /var/log/cgnat/$8/$7/$1/$2/$3/server-$4.log | grep $5:$6)
echo "$consulta_ip"
else
consulta_ip=$(sudo zcat /var/log/cgnat/$8/$7/$1/$2/$3/server-$4.log | grep $5:$6)
echo "$consulta_ip"
fi
else
if [ $4 -eq $DIA ]
then
consulta_ip=$(sudo cat /var/log/cgnat/$8/$7/$1/$2/$3/nfcapd.$1$2$3$400 | nfdump -r - "src xport $6 and src xip $5")
echo "$consulta_ip"
else
consulta_ip=$(sudo zcat /var/log/cgnat/$8/$7/$1/$2/$3/nfcapd.$1$2$3$400 | nfdump -r - "src xport $6 and src xip $5")
echo "$consulta_ip"
fi
fi