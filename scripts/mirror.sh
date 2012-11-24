#!/bin/bash
# copy to server 4 and 5

rep=/srv/d_mcweb2/sshscript/logs/
f=mirrorlog.txt
#rm -r ${rep}*
echo $(date +%m-%d-%Y)
touch ${rep}${f}
date >>  ${rep}${f}

echo ' ' >> ${rep}${f}
echo '******************' >> ${rep}${f}
echo Copying sitemap to root >> ${rep}${f}
echo '******************' >> ${rep}${f}

cp /srv/d_mcweb2/www/ajroot/htdocs/ci/sitemaps/* /srv/d_mcweb2/www/ajroot/htdocs/ci --preserve=all
echo ' ' >> ${rep}${f}
echo '******************' >> ${rep}${f}
echo Doing htaccess >> ${rep}${f}
echo '******************' >> ${rep}${f}
echo '//Server1://' >> ${rep}${f}
rsync -a -e ssh /srv/d_mcweb2/www/ajroot/htdocs/.htaccess root@92.243.3.53:/srv/d_mcweb1/www/ajroot/htdocs/.htaccess --progress --log-file=${rep}${f}
echo '//Server5://' >> ${rep}${f}
rsync -a -e ssh /srv/d_mcweb2/www/ajroot/htdocs/.htaccess root@95.142.165.222:/srv/d_mcweb5/www/ajroot/htdocs/.htaccess --progress --log-file=${rep}${f}
echo '//Server4://' >> ${rep}${f}
rsync -a -e ssh /srv/d_mcweb2/www/ajroot/htdocs/.htaccess root@95.142.165.140:/srv/d_mcweb4/www/ajroot/htdocs/.htaccess --progress --log-file=${rep}${f}
echo '//Server6://' >> ${rep}${f}
rsync -a -e ssh /srv/d_mcweb2/www/ajroot/htdocs/.htaccess root@95.142.168.80:/srv/d_mcweb6/www/ajroot/htdocs/.htaccess --progress --log-file=${rep}${f}
echo '//Server7://' >> ${rep}${f}
rsync -a -e ssh /srv/d_mcweb2/www/ajroot/htdocs/.htaccess root@95.142.168.84:/srv/d_mcweb7/www/ajroot/htdocs/.htaccess --progress --log-file=${rep}${f}
echo '//Server8://' >> ${rep}${f}
rsync -a -e ssh /srv/d_mcweb2/www/ajroot/htdocs/.htaccess root@95.142.170.8:/srv/d_mcweb8/www/ajroot/htdocs/.htaccess --progress --log-file=${rep}${f}
echo '//Server9://' >> ${rep}${f}
rsync -a -e ssh /srv/d_mcweb2/www/ajroot/htdocs/.htaccess root@95.142.170.9:/srv/d_mcweb9/www/ajroot/htdocs/.htaccess --progress --log-file=${rep}${f}
echo '//Server10://' >> ${rep}${f}
rsync -a -e ssh /srv/d_mcweb2/www/ajroot/htdocs/.htaccess root@95.142.167.244:/srv/d_mcweb10_2/www/ajroot/htdocs/.htaccess --progress --log-file=${rep}${f}
echo '//Server11://' >> ${rep}${f}
rsync -a -e ssh /srv/d_mcweb2/www/ajroot/htdocs/.htaccess root@95.142.168.120:/srv/d_mcweb11/www/ajroot/htdocs/.htaccess --progress --log-file=${rep}${f}
echo '//Server12://' >> ${rep}${f}
rsync -a -e ssh /srv/d_mcweb2/www/ajroot/htdocs/.htaccess root@95.142.168.116:/srv/d_mcweb12/www/ajroot/htdocs/.htaccess --progress --log-file=${rep}${f}


echo ' ' >> ${rep}${f}
echo '******************' >> ${rep}${f}
echo Doing Maintenance file >> ${rep}${f}
echo '******************' >> ${rep}${f}
echo '//Server1://' >> ${rep}${f}
rsync -a -e ssh /srv/d_mcweb2/www/ajroot/htdocs/maintenance.html root@92.243.3.53:/srv/d_mcweb1/www/ajroot/htdocs/maintenance.html --progress --log-file=${rep}${f}
echo '//Server5://' >> ${rep}${f}
rsync -a -e ssh /srv/d_mcweb2/www/ajroot/htdocs/maintenance.html root@95.142.165.222:/srv/d_mcweb5/www/ajroot/htdocs/maintenance.html --progress --log-file=${rep}${f}
echo '//Server4://' >> ${rep}${f}
rsync -a -e ssh /srv/d_mcweb2/www/ajroot/htdocs/maintenance.html root@95.142.165.140:/srv/d_mcweb4/www/ajroot/htdocs/maintenance.html --progress --log-file=${rep}${f}
echo '//Server6://' >> ${rep}${f}
rsync -a -e ssh /srv/d_mcweb2/www/ajroot/htdocs/maintenance.html root@95.142.168.80:/srv/d_mcweb6/www/ajroot/htdocs/maintenance.html --progress --log-file=${rep}${f}
echo '//Server7://' >> ${rep}${f}
rsync -a -e ssh /srv/d_mcweb2/www/ajroot/htdocs/maintenance.html root@95.142.168.84:/srv/d_mcweb7/www/ajroot/htdocs/maintenance.html --progress --log-file=${rep}${f}
echo '//Server8://' >> ${rep}${f}
rsync -a -e ssh /srv/d_mcweb2/www/ajroot/htdocs/maintenance.html root@95.142.170.8:/srv/d_mcweb8/www/ajroot/htdocs/maintenance.html --progress --log-file=${rep}${f}
echo '//Server9://' >> ${rep}${f}
rsync -a -e ssh /srv/d_mcweb2/www/ajroot/htdocs/maintenance.html root@95.142.170.9:/srv/d_mcweb9/www/ajroot/htdocs/maintenance.html --progress --log-file=${rep}${f}
echo '//Server10://' >> ${rep}${f}
rsync -a -e ssh /srv/d_mcweb2/www/ajroot/htdocs/maintenance.html root@95.142.167.244:/srv/d_mcweb10_2/www/ajroot/htdocs/maintenance.html --progress --log-file=${rep}${f}
echo '//Server11://' >> ${rep}${f}
rsync -a -e ssh /srv/d_mcweb2/www/ajroot/htdocs/maintenance.html root@95.142.168.120:/srv/d_mcweb11/www/ajroot/htdocs/maintenance.html --progress --log-file=${rep}${f}
echo '//Server12://' >> ${rep}${f}
rsync -a -e ssh /srv/d_mcweb2/www/ajroot/htdocs/maintenance.html root@95.142.168.116:/srv/d_mcweb12/www/ajroot/htdocs/maintenance.html --progress --log-file=${rep}${f}


echo ' ' >> ${rep}${f}
echo '******************' >> ${rep}${f}
echo Doing CI >> ${rep}${f}
echo '******************' >> ${rep}${f}
echo '//Server1://' >> ${rep}${f}
rsync -a -e ssh /srv/d_mcweb2/www/ajroot/htdocs/ci/ --exclude-from '/srv/d_mcweb2/sshscript/exclude.txt'  root@92.243.3.53:/srv/d_mcweb1/www/ajroot/htdocs/ci/ --progress --log-file=${rep}${f}
echo '//Server5://' >> ${rep}${f}
rsync -a -e ssh /srv/d_mcweb2/www/ajroot/htdocs/ci/ --exclude-from '/srv/d_mcweb2/sshscript/exclude.txt' root@95.142.165.222:/srv/d_mcweb5/www/ajroot/htdocs/ci/ --progress --log-file=${rep}${f}
echo '//Server4://' >> ${rep}${f}
rsync -a -e ssh /srv/d_mcweb2/www/ajroot/htdocs/ci/ --exclude-from '/srv/d_mcweb2/sshscript/exclude.txt'  root@95.142.165.140:/srv/d_mcweb4/www/ajroot/htdocs/ci/ --progress --log-file=${rep}${f}
echo '//Server6://' >> ${rep}${f}
rsync -a -e ssh /srv/d_mcweb2/www/ajroot/htdocs/ci/ --exclude-from '/srv/d_mcweb2/sshscript/exclude.txt'  root@95.142.168.80:/srv/d_mcweb6/www/ajroot/htdocs/ci/ --progress --log-file=${rep}${f}
echo '//Server7://' >> ${rep}${f}
rsync -a -e ssh /srv/d_mcweb2/www/ajroot/htdocs/ci/ --exclude-from '/srv/d_mcweb2/sshscript/exclude.txt'  root@95.142.168.84:/srv/d_mcweb7/www/ajroot/htdocs/ci/ --progress --log-file=${rep}${f}
echo '//Server8://' >> ${rep}${f}
rsync -a -e ssh /srv/d_mcweb2/www/ajroot/htdocs/ci/ --exclude-from '/srv/d_mcweb2/sshscript/exclude.txt'  root@95.142.170.8:/srv/d_mcweb8/www/ajroot/htdocs/ci/ --progress --log-file=${rep}${f}
echo '//Server9://' >> ${rep}${f}
rsync -a -e ssh /srv/d_mcweb2/www/ajroot/htdocs/ci/ --exclude-from '/srv/d_mcweb2/sshscript/exclude.txt'  root@95.142.170.9:/srv/d_mcweb9/www/ajroot/htdocs/ci/ --progress --log-file=${rep}${f}
echo '//Server10://' >> ${rep}${f}
rsync -a -e ssh /srv/d_mcweb2/www/ajroot/htdocs/ci/ --exclude-from '/srv/d_mcweb2/sshscript/exclude.txt'  root@95.142.167.244:/srv/d_mcweb10_2/www/ajroot/htdocs/ci/ --progress --log-file=${rep}${f}
echo '//Server11://' >> ${rep}${f}
rsync -a -e ssh /srv/d_mcweb2/www/ajroot/htdocs/ci/ --exclude-from '/srv/d_mcweb2/sshscript/exclude.txt'  root@95.142.168.120:/srv/d_mcweb11/www/ajroot/htdocs/ci/ --progress --log-file=${rep}${f}
echo '//Server12://' >> ${rep}${f}
rsync -a -e ssh /srv/d_mcweb2/www/ajroot/htdocs/ci/ --exclude-from '/srv/d_mcweb2/sshscript/exclude.txt'  root@95.142.168.116:/srv/d_mcweb12/www/ajroot/htdocs/ci/ --progress --log-file=${rep}${f}


echo ' ' >> ${rep}${f}
echo '******************' >> ${rep}${f}
echo Doing Wordpress Config ... >> ${rep}${f}
echo '******************' >> ${rep}${f}
echo '//Server1://' >> ${rep}${f}
rsync -a -e ssh /srv/d_mcweb2/www/ajroot/htdocs/wp/wp-config.php root@92.243.3.53:/srv/d_mcweb1/www/ajroot/htdocs/wp/wp-config.php --progress --log-file=${rep}${f}
rsync -a -e ssh /srv/d_mcweb2/www/ajroot/htdocs/wp/.htaccess root@92.243.3.53:/srv/d_mcweb1/www/ajroot/htdocs/wp/.htaccess --progress --log-file=${rep}${f}
echo '//Server5://' >> ${rep}${f}
rsync -a -e ssh /srv/d_mcweb2/www/ajroot/htdocs/wp/wp-config.php root@95.142.165.222:/srv/d_mcweb5/www/ajroot/htdocs/wp/wp-config.php --progress --log-file=${rep}${f}
rsync -a -e ssh /srv/d_mcweb2/www/ajroot/htdocs/wp/.htaccess root@95.142.165.222:/srv/d_mcweb5/www/ajroot/htdocs/wp/.htaccess --progress --log-file=${rep}${f}
echo '//Server4://' >> ${rep}${f}
rsync -a -e ssh /srv/d_mcweb2/www/ajroot/htdocs/wp/wp-config.php root@95.142.165.140:/srv/d_mcweb4/www/ajroot/htdocs/wp/wp-config.php --progress --log-file=${rep}${f}
rsync -a -e ssh /srv/d_mcweb2/www/ajroot/htdocs/wp/.htaccess root@95.142.165.140:/srv/d_mcweb4/www/ajroot/htdocs/wp/.htaccess --progress --log-file=${rep}${f}

echo '//Server6://' >> ${rep}${f}
rsync -a -e ssh /srv/d_mcweb2/www/ajroot/htdocs/wp/wp-config.php root@95.142.168.80:/srv/d_mcweb6/www/ajroot/htdocs/wp/wp-config.php --progress --log-file=${rep}${f}
rsync -a -e ssh /srv/d_mcweb2/www/ajroot/htdocs/wp/.htaccess root@95.142.168.80:/srv/d_mcweb6/www/ajroot/htdocs/wp/.htaccess --progress --log-file=${rep}${f}

echo '//Server7://' >> ${rep}${f}
rsync -a -e ssh /srv/d_mcweb2/www/ajroot/htdocs/wp/wp-config.php root@95.142.168.84:/srv/d_mcweb7/www/ajroot/htdocs/wp/wp-config.php --progress --log-file=${rep}${f}
rsync -a -e ssh /srv/d_mcweb2/www/ajroot/htdocs/wp/.htaccess root@95.142.168.84:/srv/d_mcweb7/www/ajroot/htdocs/wp/.htaccess --progress --log-file=${rep}${f}

echo '//Server8://' >> ${rep}${f}
rsync -a -e ssh /srv/d_mcweb2/www/ajroot/htdocs/wp/wp-config.php root@95.142.170.8:/srv/d_mcweb8/www/ajroot/htdocs/wp/wp-config.php --progress --log-file=${rep}${f}
rsync -a -e ssh /srv/d_mcweb2/www/ajroot/htdocs/wp/.htaccess root@95.142.170.8:/srv/d_mcweb8/www/ajroot/htdocs/wp/.htaccess --progress --log-file=${rep}${f}

echo '//Server9://' >> ${rep}${f}
rsync -a -e ssh /srv/d_mcweb2/www/ajroot/htdocs/wp/wp-config.php root@95.142.170.9:/srv/d_mcweb9/www/ajroot/htdocs/wp/wp-config.php --progress --log-file=${rep}${f}
rsync -a -e ssh /srv/d_mcweb2/www/ajroot/htdocs/wp/.htaccess root@95.142.170.9:/srv/d_mcweb9/www/ajroot/htdocs/wp/.htaccess --progress --log-file=${rep}${f}

echo '//Server10://' >> ${rep}${f}
rsync -a -e ssh /srv/d_mcweb2/www/ajroot/htdocs/wp/wp-config.php root@95.142.167.244:/srv/d_mcweb10_2/www/ajroot/htdocs/wp/wp-config.php --progress --log-file=${rep}${f}
rsync -a -e ssh /srv/d_mcweb2/www/ajroot/htdocs/wp/.htaccess root@95.142.167.244:/srv/d_mcweb10_2/www/ajroot/htdocs/wp/.htaccess --progress --log-file=${rep}${f}

echo '//Server11://' >> ${rep}${f}
rsync -a -e ssh /srv/d_mcweb2/www/ajroot/htdocs/wp/wp-config.php root@95.142.168.120:/srv/d_mcweb11/www/ajroot/htdocs/wp/wp-config.php --progress --log-file=${rep}${f}
rsync -a -e ssh /srv/d_mcweb2/www/ajroot/htdocs/wp/.htaccess root@95.142.168.120:/srv/d_mcweb11/www/ajroot/htdocs/wp/.htaccess --progress --log-file=${rep}${f}

echo '//Server12://' >> ${rep}${f}
rsync -a -e ssh /srv/d_mcweb2/www/ajroot/htdocs/wp/wp-config.php root@95.142.168.116:/srv/d_mcweb12/www/ajroot/htdocs/wp/wp-config.php --progress --log-file=${rep}${f}
rsync -a -e ssh /srv/d_mcweb2/www/ajroot/htdocs/wp/.htaccess root@95.142.168.116:/srv/d_mcweb12/www/ajroot/htdocs/wp/.htaccess --progress --log-file=${rep}${f}

echo ' ' >> ${rep}${f}
echo '******************' >> ${rep}${f}
echo Doing Wordpress Theme ... >> ${rep}${f}
echo '******************' >> ${rep}${f}
echo '//Server1://' >> ${rep}${f}
rsync -a -e ssh /srv/d_mcweb2/www/ajroot/htdocs/wp/wp-content/themes/Auberge/ --exclude '/scripts/cache/'  root@92.243.3.53:/srv/d_mcweb1/www/ajroot/htdocs/wp/wp-content/themes/Auberge/ --progress --log-file=${rep}${f}
echo '//Server5://' >> ${rep}${f}
rsync -a -e ssh /srv/d_mcweb2/www/ajroot/htdocs/wp/wp-content/themes/Auberge/ --exclude '/scripts/cache/' root@95.142.165.222:/srv/d_mcweb5/www/ajroot/htdocs/wp/wp-content/themes/Auberge/ --progress --log-file=${rep}${f}
echo '//Server4://' >> ${rep}${f}
rsync -a -e ssh /srv/d_mcweb2/www/ajroot/htdocs/wp/wp-content/themes/Auberge/ --exclude '/scripts/cache/'  root@95.142.165.140:/srv/d_mcweb4/www/ajroot/htdocs/wp/wp-content/themes/Auberge/ --progress --log-file=${rep}${f}
echo '//Server6://' >> ${rep}${f}
rsync -a -e ssh /srv/d_mcweb2/www/ajroot/htdocs/wp/wp-content/themes/Auberge/ --exclude '/scripts/cache/'  root@95.142.168.80:/srv/d_mcweb6/www/ajroot/htdocs/wp/wp-content/themes/Auberge/ --progress --log-file=${rep}${f}
echo '//Server7://' >> ${rep}${f}
rsync -a -e ssh /srv/d_mcweb2/www/ajroot/htdocs/wp/wp-content/themes/Auberge/ --exclude '/scripts/cache/'  root@95.142.168.84:/srv/d_mcweb7/www/ajroot/htdocs/wp/wp-content/themes/Auberge/ --progress --log-file=${rep}${f}
echo '//Server8://' >> ${rep}${f}
rsync -a -e ssh /srv/d_mcweb2/www/ajroot/htdocs/wp/wp-content/themes/Auberge/ --exclude '/scripts/cache/'  root@95.142.170.8:/srv/d_mcweb8/www/ajroot/htdocs/wp/wp-content/themes/Auberge/ --progress --log-file=${rep}${f}
echo '//Server9://' >> ${rep}${f}
rsync -a -e ssh /srv/d_mcweb2/www/ajroot/htdocs/wp/wp-content/themes/Auberge/ --exclude '/scripts/cache/'  root@95.142.170.9:/srv/d_mcweb9/www/ajroot/htdocs/wp/wp-content/themes/Auberge/ --progress --log-file=${rep}${f}
echo '//Server10://' >> ${rep}${f}
rsync -a -e ssh /srv/d_mcweb2/www/ajroot/htdocs/wp/wp-content/themes/Auberge/ --exclude '/scripts/cache/'  root@95.142.167.244:/srv/d_mcweb10_2/www/ajroot/htdocs/wp/wp-content/themes/Auberge/ --progress --log-file=${rep}${f}
echo '//Server11://' >> ${rep}${f}
rsync -a -e ssh /srv/d_mcweb2/www/ajroot/htdocs/wp/wp-content/themes/Auberge/ --exclude '/scripts/cache/'  root@95.142.168.120:/srv/d_mcweb11/www/ajroot/htdocs/wp/wp-content/themes/Auberge/ --progress --log-file=${rep}${f}
echo '//Server12://' >> ${rep}${f}
rsync -a -e ssh /srv/d_mcweb2/www/ajroot/htdocs/wp/wp-content/themes/Auberge/ --exclude '/scripts/cache/'  root@95.142.168.116:/srv/d_mcweb12/www/ajroot/htdocs/wp/wp-content/themes/Auberge/ --progress --log-file=${rep}${f}


echo ' ' >> ${rep}${f}
echo '******************' >> ${rep}${f}
echo Doing Wordpress Admin auberge plugin ... >> ${rep}${f}
echo '******************' >> ${rep}${f}
echo '//Server1://' >> ${rep}${f}
rsync -a -e ssh /srv/d_mcweb2/www/ajroot/htdocs/wp/wp-content/plugins/auberges_admin/ --exclude '/scripts/cache/'  root@92.243.3.53:/srv/d_mcweb1/www/ajroot/htdocs/wp/wp-content/plugins/auberges_admin/ --progress --log-file=${rep}${f}
echo '//Server5://' >> ${rep}${f}
rsync -a -e ssh /srv/d_mcweb2/www/ajroot/htdocs/wp/wp-content/plugins/auberges_admin/ --exclude '/scripts/cache/'  root@95.142.165.222:/srv/d_mcweb5/www/ajroot/htdocs/wp/wp-content/plugins/auberges_admin/ --progress --log-file=${rep}${f}
echo '//Server4://' >> ${rep}${f}
rsync -a -e ssh /srv/d_mcweb2/www/ajroot/htdocs/wp/wp-content/plugins/auberges_admin/ --exclude '/scripts/cache/'  root@95.142.165.140:/srv/d_mcweb4/www/ajroot/htdocs/wp/wp-content/plugins/auberges_admin/ --progress --log-file=${rep}${f}
echo '//Server6://' >> ${rep}${f}
rsync -a -e ssh /srv/d_mcweb2/www/ajroot/htdocs/wp/wp-content/plugins/auberges_admin/ --exclude '/scripts/cache/'  root@95.142.168.80:/srv/d_mcweb6/www/ajroot/htdocs/wp/wp-content/plugins/auberges_admin/ --progress --log-file=${rep}${f}
echo '//Server7://' >> ${rep}${f}
rsync -a -e ssh /srv/d_mcweb2/www/ajroot/htdocs/wp/wp-content/plugins/auberges_admin/ --exclude '/scripts/cache/'  root@95.142.168.84:/srv/d_mcweb7/www/ajroot/htdocs/wp/wp-content/plugins/auberges_admin/ --progress --log-file=${rep}${f}
echo '//Server8://' >> ${rep}${f}
rsync -a -e ssh /srv/d_mcweb2/www/ajroot/htdocs/wp/wp-content/plugins/auberges_admin/ --exclude '/scripts/cache/'  root@95.142.170.8:/srv/d_mcweb8/www/ajroot/htdocs/wp/wp-content/plugins/auberges_admin/ --progress --log-file=${rep}${f}
echo '//Server9://' >> ${rep}${f}
rsync -a -e ssh /srv/d_mcweb2/www/ajroot/htdocs/wp/wp-content/plugins/auberges_admin/ --exclude '/scripts/cache/'  root@95.142.170.9:/srv/d_mcweb9/www/ajroot/htdocs/wp/wp-content/plugins/auberges_admin/ --progress --log-file=${rep}${f}
echo '//Server10://' >> ${rep}${f}
rsync -a -e ssh /srv/d_mcweb2/www/ajroot/htdocs/wp/wp-content/plugins/auberges_admin/ --exclude '/scripts/cache/'  root@95.142.167.244:/srv/d_mcweb10_2/www/ajroot/htdocs/wp/wp-content/plugins/auberges_admin/ --progress --log-file=${rep}${f}
echo '//Server11://' >> ${rep}${f}
rsync -a -e ssh /srv/d_mcweb2/www/ajroot/htdocs/wp/wp-content/plugins/auberges_admin/ --exclude '/scripts/cache/'  root@95.142.168.120:/srv/d_mcweb11/www/ajroot/htdocs/wp/wp-content/plugins/auberges_admin/ --progress --log-file=${rep}${f}
echo '//Server12://' >> ${rep}${f}
rsync -a -e ssh /srv/d_mcweb2/www/ajroot/htdocs/wp/wp-content/plugins/auberges_admin/ --exclude '/scripts/cache/'  root@95.142.168.116:/srv/d_mcweb12/www/ajroot/htdocs/wp/wp-content/plugins/auberges_admin/ --progress --log-file=${rep}${f}

SUBJECT="Mirror has been ran"
# Email To ?
EMAIL="technical@mcwebmanagement.com"
EMAILMESSAGE="/srv/d_mcweb2/sshscript/logs/emailmessage.txt"
echo "Mirror was ran. Yeah man Replication !!!!">> $EMAILMESSAGE

#mail -s "$SUBJECT" "$EMAIL" < ${rep}${f}
mutt -s "$SUBJECT" -a ${rep}${f} -- "$EMAIL" < $EMAILMESSAGE
rm -r ${rep}*
