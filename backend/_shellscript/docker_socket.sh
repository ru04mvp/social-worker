#!/bin/sh
# 啟動 Socket Server
echo '目前的執行項目:'
ps -ef | grep '/socket/server'
echo '砍殺程序...'
ps -ef | grep '/socket/server' | awk '{print $2}' |xargs kill -9
echo '啟動程序...'
nohup php /var/www/public/api/index.php /socket/server >> /var/www/_shellscript/logs/socket.log &
echo '新的執行項目:'
ps -ef | grep '/socket/server'