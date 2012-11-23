#!/bin/sh
# filename
rep=/srv/d_mcweb2/sshscript/logs/
f=mirrorlog.log
rm -r ${rep}*
echo $(date +%m-%d-%Y)
touch ${rep}${f}
date >>  ${rep}${f}
makemime -c -N "${f}" -o ${rep}${f}.msg ${rep}${f}
#cat ${rep}${f}.msg | mail -s "Logs $(date +%m%d%Y)" gbourdages@graphem.ca

SUBJECT="SET-EMAIL-SUBJECT"
# Email To ?
EMAIL="gbourdages@graphem.ca"
# Email text/message
# send an email using /bin/mail
/bin/mail -s "$SUBJECT" "$EMAIL" < ${rep}${f}


