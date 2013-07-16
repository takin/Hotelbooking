#!/bin/sh

function log {
    /bin/echo `date` $* | /usr/bin/tee -a /opt/logs/diskspacecron.log
}

function clearLocal {

  if [ -n $(/usr/bin/sudo /opt/scripts/diskspace.sh) ]; then
  
  echo "Disk Space is very low on $1 only 10% left. Cache has been cleared" | mutt -s "Disk Space is very Low on $1" korir.mordecai@gmail.com
  
  fi
  
  log $1 OS Cache Clear
  
}

function clear {

 $(/usr/bin/ssh -t admin@$1 '/usr/bin/sudo /opt/scripts/diskspace.sh')
 
  if [ -n $(/usr/bin/ssh -t admin@$1 '/usr/bin/sudo /opt/scripts/diskspace.sh') ]; then
  
  echo "Disk Space is very low on $1 only 10% left. Cache has been cleared" | mutt -s "Disk Space is very Low on $1" korir.mordecai@gmail.com

  fi
  
  log $1 OS Cache Clear
  
}

clearLocal mcdev01
clear mclb01
clear mcweb01
clear mcweb02