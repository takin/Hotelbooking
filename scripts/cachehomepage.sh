#!/bin/sh

# constants

HOST_PARAM_KEY="-host="
CURRENCY_PARAM_KEY="-currency="
USER_PARAM_KEY="-user="
PASSWORD_PARAM_KEY="-password=";

# variables

args=$@
host=""
currency=""
user=""
password=""

# functions

function parseParameters() {
    for arg in $args
    do
        case $arg in
            $HOST_PARAM_KEY*)
                host=${arg:${#HOST_PARAM_KEY}}
            ;;
            $CURRENCY_PARAM_KEY*)
                currency=${arg:${#CURRENCY_PARAM_KEY}}
            ;;
            $USER_PARAM_KEY*)
                user=${arg:${#USER_PARAM_KEY}}
            ;;
            $PASSWORD_PARAM_KEY*)
                password=${arg:${#PASSWORD_PARAM_KEY}}
            ;;
        esac
    done
}

function checkParameters() {
    if [ -z "$host" ]; then
        exitWithError "Host is invalid"
    fi
    if [ -z "$currency" ]; then
        exitWithError "Currency is invalid"
    fi
}

function exitWithError() {
    echo "------------------- Failed ----------------------"
    echo $@
    echo "-------------------------------------------------"
    exit -1
}

function log {
    echo `date` $* | tee -a /opt/logs/cachehomepage.log
}

function makeCall() {
(
    flock 200

    log $host $currency
    if [ -z "$user" ] || [ -z "$password" ];  then
        wget --no-cache --read-timeout=0 "http://$host/?currency=$currency&cacherun=run"
    else
        wget --no-cache --read-timeout=0 --user=$user --password=$password "http://$host/?currency=$currency&cacherun=run"
    fi

) 200>/tmp/.cachehomepage.exclusivelock
}

# execution

parseParameters
checkParameters
makeCall

