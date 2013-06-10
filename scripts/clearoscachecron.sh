#!/bin/sh

function log {
    /bin/echo `date` $* | /usr/bin/tee -a /opt/logs/clearoscachecron.log
}

function clearLocal {
  /usr/bin/sudo /opt/scripts/clearoscache.sh
  log $1 OS Cache Clear
}

function clear {
  /usr/bin/ssh -t admin@$1 'sudo /opt/scripts/clearoscache.sh'
  log $1 OS Cache Clear
}

clearLocal mcdev01
clear mclb01
clear mcweb01
clear mcweb02
