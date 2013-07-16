#!/bin/sh

function log {
    /bin/echo `date` $* | /usr/bin/tee -a /opt/logs/diskspacecron.log
}

function clearLocal {

  if [[ $(/usr/bin/sudo /opt/scripts/diskspace.sh) == *clearoscache* ]]; then
  
  echo "Disk Space is very low on $1 only 10% left. Cache has been cleared" | mutt -s "Disk Space is very Low on $1" technical@mcwebmanagement.com
  log $1 OS Cache Cleared
  
  else
  
  log $1 OS No Cache Cleared
  
  fi
  
}

function clear {
 
  if [[ $(/usr/bin/ssh -t admin@$1 '/usr/bin/sudo /opt/scripts/diskspace.sh') == *clearoscache* ]]; then
  
  echo "Disk Space is very low on $1 only 10% left. Cache has been cleared" | mutt -s "Disk Space is very Low on $1" technical@mcwebmanagement.com
  log $1 OS Cache Cleared
  
  else
  
  log $1 OS No Cache Cleared
  
  fi
  
}

clearLocal mcdev01
clear mclb01
clear mcweb01
clear mcweb02