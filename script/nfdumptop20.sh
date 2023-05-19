#!/bin/bash
ANO=$(date +%Y)
MES=$(date +%m)
DIA=$(date +"%d" -d "-1 hours")
HORA=$(date +"%H" -d "-1 hours")
MINU="00"
TIPO="flow"
if [ $1 != $TIPO ]
then
consulta_ip=$(sudo tail -n 20 /var/log/cgnat/$1/$2/$ANO/$MES/$DIA/server-$HORA.log)
echo "$consulta_ip"
else
consulta_ip=$(sudo cat /var/log/cgnat/$1/$2/$ANO/$MES/$DIA/nfcapd.$ANO$MES$DIA$HORA$MINU | nfdump -r - -c 20)
echo "$consulta_ip"
fi
