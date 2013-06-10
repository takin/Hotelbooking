#!/bin/sh

function log {
    echo `date` $* | tee -a /opt/logs/clearoscachecron.log
}

function clearLocal {
  sudo /opt/scripts/clearoscache.sh
  log $1 OS Cache Clear
}

function clear {
  ssh -t admin@$1 'sudo /opt/scripts/clearoscache.sh'
  log $1 OS Cache Clear
}

clearLocal mcdev01
clear mclb01
clear mcweb01
clear mcweb02
