#! /bin/bash

pidCount=`ps aux | grep -e "app/cli.php sms" -c`
pids=`ps aux | grep -e "app/cli.php sms" | awk '{print $2}'`
datetime=`date "+%F %T"`

echo $pidCount

if [ $pidCount -gt 1 ]
then
        echo "$datetime $pidCount process runing..."
        exit 0
fi

echo "$datetime SMS send task is not runing, fork now..."

for j in {1..1}
do
    /www/php/bin/php /www/linkage/app/cli.php sms send >> /tmp/sms.log 2>&1 &
done
echo "New sms task daemon process done"

for p in $pids
do
        echo "Kiling old vote update task process $p"
        kill -9 $p 2&>/dev/null
done