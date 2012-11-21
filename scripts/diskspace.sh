#!/bin/sh
# filename
#echo $(date +%m-%d-%Y)

df=`df -Pl  | grep "^/dev" | awk '{print $5, $6}' | sed "s/%//"`

echo "$df" | while read percent fs
do

if [ $percent -ge 90 ] ; then

SUBJECT="Disk Space is very Lowi on MCWEB2"
# Email T
EMAIL="alerts@graphem.ca"
EMAILMESSAGE="/srv/d_mcweb2/sshscript/logs/emailmessagelow.txt"
echo "Disk Space is very low only 10% left">> $EMAILMESSAGE

#mail -s "$SUBJECT" "$EMAIL" < ${rep}${f}
mutt -s "$SUBJECT" -- "$EMAIL" < $EMAILMESSAGE

fi
done
rm /srv/d_mcweb2/sshscript/logs/emailmessagelow.txt
