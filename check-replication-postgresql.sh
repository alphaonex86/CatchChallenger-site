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
if [ "${CONTENT}" != "" ]
then
    echo -n catchchallenger-db-common-1-a-slave:
    echo ${CONTENT}
fi

echo Slave connected on master:
CONTENT=`ssh root@catchchallenger-db-common-1-a-master 'psql -c "select * from pg_stat_replication ;" postgres' | grep -F -v backend_start | grep -v -F +-- | grep -F 172.16.0.85 | grep -F streaming`
if [ "${CONTENT}" != "" ]
then
    echo -n catchchallenger-db-common-1-a-slave:
    echo ${CONTENT}
fi
