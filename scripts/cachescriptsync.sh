#!/bin/bash
# copy to server 4 and 5
#rep=/srv/d_mcweb2/sshscript/logs/
#f=mirrorlog.txt
#rm -r ${rep}*
echo $(date +%m-%d-%Y)
echo '******************'
echo Syncing caching scripts
echo '******************'
echo '//Server1://'
rsync -a -e ssh /home/admin/cacheadmin/clear_property_seach.sh root@92.243.3.53:/home/admin/cacheadmin/clear_property_seach.sh --progress
rsync -a -e ssh /home/admin/cacheadmin/clear_cache_propertie_pages.sh root@92.243.3.53:/home/admin/cacheadmin/clear_cache_propertie_pages.sh --progress
rsync -a -e ssh /home/admin/cacheadmin/clear_all_cache_fullsite.sh root@92.243.3.53:/home/admin/cacheadmin/clear_all_cache_fullsite.sh --progress
echo '//Server5://'
rsync -a -e ssh /home/admin/cacheadmin/clear_property_seach.sh root@95.142.165.222:/home/admin/cacheadmin/clear_property_seach.sh --progress
rsync -a -e ssh /home/admin/cacheadmin/clear_cache_propertie_pages.sh root@95.142.165.222:/home/admin/cacheadmin/clear_cache_propertie_pages.sh --progress
rsync -a -e ssh /home/admin/cacheadmin/clear_all_cache_fullsite.sh root@95.142.165.222:/home/admin/cacheadmin/clear_all_cache_fullsite.sh --progress
echo '//Server4://'
rsync -a -e ssh /home/admin/cacheadmin/clear_property_seach.sh root@95.142.165.140:/home/admin/cacheadmin/clear_property_seach.sh --progress
rsync -a -e ssh /home/admin/cacheadmin/clear_cache_propertie_pages.sh root@95.142.165.140:/home/admin/cacheadmin/clear_cache_propertie_pages.sh --progress
rsync -a -e ssh /home/admin/cacheadmin/clear_all_cache_fullsite.sh root@95.142.165.140:/home/admin/cacheadmin/clear_all_cache_fullsite.sh --progress
echo '//Server6://'
rsync -a -e ssh /home/admin/cacheadmin/clear_property_seach.sh root@95.142.168.80:/home/admin/cacheadmin/clear_property_seach.sh --progress 
rsync -a -e ssh /home/admin/cacheadmin/clear_cache_propertie_pages.sh root@95.142.168.80:/home/admin/cacheadmin/clear_cache_propertie_pages.sh --progress 
rsync -a -e ssh /home/admin/cacheadmin/clear_all_cache_fullsite.sh root@95.142.168.80:/home/admin/cacheadmin/clear_all_cache_fullsite.sh --progress 
echo '//Server7://'
rsync -a -e ssh /home/admin/cacheadmin/clear_property_seach.sh root@95.142.168.84:/home/admin/cacheadmin/clear_property_seach.sh --progress
rsync -a -e ssh /home/admin/cacheadmin/clear_cache_propertie_pages.sh root@95.142.168.84:/home/admin/cacheadmin/clear_cache_propertie_pages.sh --progress
rsync -a -e ssh /home/admin/cacheadmin/clear_all_cache_fullsite.sh root@95.142.168.84:/home/admin/cacheadmin/clear_all_cache_fullsite.sh --progress
echo '//Server8://'
rsync -a -e ssh /home/admin/cacheadmin/clear_property_seach.sh root@95.142.170.8:/home/admin/cacheadmin/clear_property_seach.sh --progress 
rsync -a -e ssh /home/admin/cacheadmin/clear_cache_propertie_pages.sh root@95.142.170.8:/home/admin/cacheadmin/clear_cache_propertie_pages.sh --progress 
rsync -a -e ssh /home/admin/cacheadmin/clear_all_cache_fullsite.sh root@95.142.170.8:/home/admin/cacheadmin/clear_all_cache_fullsite.sh --progress 
echo '//Server9://'
rsync -a -e ssh /home/admin/cacheadmin/clear_property_seach.sh root@95.142.170.9:/home/admin/cacheadmin/clear_property_seach.sh --progress 
rsync -a -e ssh /home/admin/cacheadmin/clear_cache_propertie_pages.sh root@95.142.170.9:/home/admin/cacheadmin/clear_cache_propertie_pages.sh --progress 
rsync -a -e ssh /home/admin/cacheadmin/clear_all_cache_fullsite.sh root@95.142.170.9:/home/admin/cacheadmin/clear_all_cache_fullsite.sh --progress 
echo '//Server10://'
#rsync -a -e ssh /home/admin/cacheadmin/clear_property_seach.sh root@95.142.167.244:/home/admin/cacheadmin/clear_property_seach.sh --progress
#rsync -a -e ssh /home/admin/cacheadmin/clear_cache_propertie_pages.sh root@95.142.167.244:/home/admin/cacheadmin/clear_cache_propertie_pages.sh --progress
echo '//Server11://'
rsync -a -e ssh /home/admin/cacheadmin/clear_property_seach.sh root@95.142.168.120:/home/admin/cacheadmin/clear_property_seach.sh --progress 
rsync -a -e ssh /home/admin/cacheadmin/clear_cache_propertie_pages.sh root@95.142.168.120:/home/admin/cacheadmin/clear_cache_propertie_pages.sh --progress 
rsync -a -e ssh /home/admin/cacheadmin/clear_all_cache_fullsite.sh root@95.142.168.120:/home/admin/cacheadmin/clear_all_cache_fullsite.sh --progress 
echo '//Server12://'
rsync -a -e ssh /home/admin/cacheadmin/clear_property_seach.sh root@95.142.168.116:/home/admin/cacheadmin/clear_property_seach.sh --progress 
rsync -a -e ssh /home/admin/cacheadmin/clear_cache_propertie_pages.sh root@95.142.168.116:/home/admin/cacheadmin/clear_cache_propertie_pages.sh --progress 
rsync -a -e ssh /home/admin/cacheadmin/clear_all_cache_fullsite.sh root@95.142.168.116:/home/admin/cacheadmin/clear_all_cache_fullsite.sh --progress 
