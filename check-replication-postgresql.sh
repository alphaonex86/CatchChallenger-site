#!/bin/bash
echo Lag:
CONTENT=`ssh root@catchchallenger-db-common-1-a-slave 'psql -c "SELECT CASE WHEN pg_last_xlog_receive_location() = pg_last_xlog_replay_location() THEN 0  ELSE EXTRACT (EPOCH FROM now() - pg_last_xact_replay_timestamp())::INTEGER END AS replication_lag;" postgres' | grep -E 'row'`
#execpted output: (1 row)
if [ "${CONTENT}" != "" ]
then
    echo -n catchchallenger-db-common-1-a-slave:
    echo ${CONTENT}
fi

echo Process on slave:
CONTENT=`ssh root@catchchallenger-db-common-1-a-slave 'ps aux | grep -F "wal receiver" | grep -F -v grep | grep -F postgres | grep -F streaming'`
# content execpted:
# postgres 22123  0.0  0.4 154236  5032 ?        Ss   14:16   0:00 postgres: wal receiver process   streaming 0/4B00F800
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
