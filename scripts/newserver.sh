rsync -a -e ssh /srv/d_mcweb2/www/ --exclude-from 'exclude-tf.txt' root@95.142.168.116:/srv/d_mcweb12/www/ --progress
