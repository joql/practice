#!/bin/sh
PREFIX=/www/wwwroot/practice/demo/php-resque
INTERVAL=1
QUEUE=* nohup php ${PREFIX}/resque.php >>${PREFIX}/resque.log 2>&1 & echo $! > ${PREFIX}/resque.pid
while [ 1 ]; do
    if [ ! -d /proc/`cat ${PREFIX}/resque.pid` ]; then
        QUEUE=* nohup php ${PREFIX}/resque.php >>${PREFIX}/resque.log 2>&1 & echo $! > ${PREFIX}/resque.pid
        echo 'NEW_PID:'`cat ${PREFIX}/resque.pid && date '+%Y-%m-%d %H:%M:%S'`
    fi
    sleep ${INTERVAL}
done
