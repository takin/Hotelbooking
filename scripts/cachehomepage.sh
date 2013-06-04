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

parseParameters() {
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

checkParameters() {
    if [ -z "$host" ]; then
        exitWithError "Host is invalid"
    fi
    if [ -z "$currency" ]; then
        exitWithError "Currency is invalid"
    fi
}

exittWithError() {
    echo "------------------- Failed ----------------------"
    echo $@
    echo "-------------------------------------------------"
    exit -1
}

function log {
    echo `date` $* | tee -a /opt/logs/cachehomepage.log
}

makeCall() {
    log $host $currency
    if [ -z "$user" ] || [ -z "$password" ];  then
        wget --no-cache --read-timeout=0 "http://$host/?currency=$currency&cacherun=run"
    else
        wget --no-cache --read-timeout=0 --user=$user --password=$password "http://$host/?currency=$currency&cacherun=run"
    fi
}

# execution

parseParameters
checkParameters
makeCall

