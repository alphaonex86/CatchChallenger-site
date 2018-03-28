#!/bin/bash
#echo Lag:
#CONTENT=`ssh root@catchchallenger-db-common-1-a-slave 'psql -c "SELECT CASE WHEN pg_last_xlog_receive_location() = pg_last_xlog_replay_location() THEN 0  ELSE EXTRACT (EPOCH FROM now() - pg_last_xact_replay_timestamp())::INTEGER END AS replication_lag;" postgres' | grep -E 'row'`
#execpted output: (1 row)
#if [ "${CONTENT}" != "" ]
#then
#    echo -n catchchallenger-db-common-1-a-slave:
#    echo ${CONTENT}
#fi

echo Process on slave:
CONTENT=`ssh root@catchchallenger-db-common-1-a-slave 'echo "SELECT * FROM pg_stat_subscription;" | psql catchchallenger_common -U postgres' | grep my_sub`
# content execpted:
# subid | subname |  pid  | relid | received_lsn |      last_msg_send_time       |     last_msg_receipt_time     | latest_end_lsn |        latest_end_time        
#-------+---------+-------+-------+--------------+-------------------------------+-------------------------------+----------------+-------------------------------
# 16424 | my_sub  | 21681 |       | 1/DB100038   | 2018-03-28 04:56:28.728831+02 | 2018-03-28 04:56:28.729587+02 | 1/DB100038     | 2018-03-28 04:56:28.728831+02
# (1 row)
if [ "${CONTENT}" != "" ]
then
    echo -n catchchallenger-db-common-1-a-slave:
    echo ${CONTENT}
fi

echo Slave connected on master:
CONTENT=`ssh root@catchchallenger-db-common-1-a-master 'psql -c "select * from pg_stat_replication ;" postgres' | grep -F -v backend_start | grep -v -F +-- | grep -F 172.16.0.85 | grep -F streaming`
# raw content execpted:
# pid | usesysid | usename | application_name | client_addr | client_hostname | client_port |         backend_start         | backend_xmin |   state   | sent_location | write_location | flush_location | replay_location | sync_priority | sync_state 
#-----+----------+---------+------------------+-------------+-----------------+-------------+-------------------------------+--------------+-----------+---------------+----------------+----------------+-----------------+---------------+------------
# 728 |    49152 | rep     | walreceiver      | 172.16.0.85 |                 |       38622 | 2017-02-10 14:17:33.844674+01 |              | streaming | 0/4B00F720    | 0/4B00F720     | 0/4B00F758     | 0/4B00F758      |             0 | async
#(1 row)
if [ "${CONTENT}" != "" ]
then
    echo -n catchchallenger-db-common-1-a-slave:
    echo ${CONTENT}
fi
