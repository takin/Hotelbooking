#!/bin/sh
# filename
#echo $(date +%m-%d-%Y)

df=`df -Pl  | grep "^/dev" | awk '{print $5, $6}' | sed "s/%//"`

echo "$df" | while read percent fs
do

if [ $percent -ge 90 ] ; then

SUBJECT="Disk Space is very Lowi on MCWEB2"
# Email T
EMAIL="technical@mcwebmanagement.com"
EMAILMESSAGE="/opt/scripts/emailmessagelow.txt"
echo "Disk Space is very low only 10% left">> $EMAILMESSAGE

#mail -s "$SUBJECT" "$EMAIL" < ${rep}${f}
mutt -s "$SUBJECT" -- "$EMAIL" < $EMAILMESSAGE

fi
done
rm /opt/scripts/emailmessagelow.txt
