#!/bin/bash
#--posix:information leak, -H ustar no and leak and better compression
RANDOMNUMBERTOKEN=`/usr/bin/openssl rand -hex 14`
find datapack/ -type d -exec chmod 700 "{}" \;
find datapack/ -type f -exec chmod 600 "{}" \;
find datapack/ -type f -print | grep -F -v 'datapack/pack/' | grep -F -v 'map/main/' | sort > /tmp/temporary_file_list${RANDOMNUMBERTOKEN}
tar --owner=0 --group=0 --mtime='2010-01-01' -H ustar -c -f - -T /tmp/temporary_file_list${RANDOMNUMBERTOKEN} > /tmp/datapack-new.tar${RANDOMNUMBERTOKEN}
rm /tmp/temporary_file_list${RANDOMNUMBERTOKEN}
mkdir -p /tmp/datapack/pack/
if [ ! -e /tmp/datapack-new.tar${RANDOMNUMBERTOKEN} ] || [ "`sha256sum /tmp/datapack-new.tar${RANDOMNUMBERTOKEN} | awk '{print $1}'`" != "`sha256sum /tmp/datapack.tar | awk '{print $1}'`" ]
then
    echo 'regen datapack'
    cat /tmp/datapack-new.tar${RANDOMNUMBERTOKEN} | /usr/bin/zstd -22 --ultra > /tmp/datapack-new.tar${RANDOMNUMBERTOKEN}.zst
    if [ $? -eq 0 ]
    then
        mv  /tmp/datapack-new.tar${RANDOMNUMBERTOKEN} /tmp/datapack.tar
        /usr/bin/zstd --test /tmp/datapack-new.tar${RANDOMNUMBERTOKEN}.zst > /dev/null 2>&1
        if [ $? -eq 0 ]
        then
            mv /tmp/datapack-new.tar${RANDOMNUMBERTOKEN}.zst /tmp/datapack.tar.zst
        else
            rm /tmp/datapack-new.tar${RANDOMNUMBERTOKEN}.zst
        fi
    else
        mv  /tmp/datapack-new.tar${RANDOMNUMBERTOKEN} /tmp/datapack.tar 
    fi
else
    echo 'already ok datapack'
    rm /tmp/datapack-new.tar${RANDOMNUMBERTOKEN}
fi
PATHB=`pwd`
for main in $(ls ${PATHB}/datapack/map/main); do
        cd ${PATHB}/datapack/map/main/
        find ${main}/ -type f -print | grep -F -v 'sub/' | sort > /tmp/temporary_file_list${RANDOMNUMBERTOKEN}
        tar --owner=0 --group=0 --mtime='2010-01-01' -H ustar -c -f - -T /tmp/temporary_file_list${RANDOMNUMBERTOKEN} > /tmp/datapack-main-${main}-new.tar${RANDOMNUMBERTOKEN}
        rm /tmp/temporary_file_list${RANDOMNUMBERTOKEN}
        if [ ! -e /tmp/datapack-main-${main}-new.tar${RANDOMNUMBERTOKEN} ] || [ "`sha256sum /tmp/datapack-main-${main}-new.tar${RANDOMNUMBERTOKEN} | awk '{print $1}'`" != "`sha256sum /tmp/datapack-main-${main}.tar | awk '{print $1}'`" ]
        then
            echo 'regen datapack main' ${main}
            cat /tmp/datapack-main-${main}-new.tar${RANDOMNUMBERTOKEN} | /usr/bin/zstd -22 --ultra > /tmp/datapack-main-${main}-new.tar${RANDOMNUMBERTOKEN}.zst
            if [ $? -eq 0 ]
            then
                mv /tmp/datapack-main-${main}-new.tar${RANDOMNUMBERTOKEN} /tmp/datapack-main-${main}.tar
                /usr/bin/zstd --test /tmp/datapack-main-${main}-new.tar${RANDOMNUMBERTOKEN}.zst > /dev/null 2>&1
                if [ $? -eq 0 ]
                then
                    mv /tmp/datapack-main-${main}-new.tar${RANDOMNUMBERTOKEN}.zst /tmp/datapack-main-${main}.tar.zst
                else
                    rm /tmp/datapack-main-${main}-new.tar${RANDOMNUMBERTOKEN}.zst
                fi
            else
                mv /tmp/datapack-main-${main}-new.tar${RANDOMNUMBERTOKEN} /tmp/datapack-main-${main}.tar
            fi
        else
            echo 'already ok datapack main' ${main}
            rm /tmp/datapack-main-${main}-new.tar${RANDOMNUMBERTOKEN}
        fi
        for sub in $(ls ${PATHB}/datapack/map/main/${main}/sub/); do
                cd ${PATHB}/datapack/map/main/${main}/sub/
                find ${sub}/ -type f -print | sort > /tmp/temporary_file_list${RANDOMNUMBERTOKEN}
                tar --owner=0 --group=0 --mtime='2010-01-01' -H ustar -c -f - -T /tmp/temporary_file_list${RANDOMNUMBERTOKEN} > /tmp/datapack-sub-${main}-${sub}-new.tar${RANDOMNUMBERTOKEN}
                rm /tmp/temporary_file_list${RANDOMNUMBERTOKEN}
                if [ ! -e /tmp/datapack-sub-${main}-${sub}-new.tar${RANDOMNUMBERTOKEN} ] || [ "`sha256sum /tmp/datapack-sub-${main}-${sub}-new.tar${RANDOMNUMBERTOKEN} | awk '{print $1}'`" != "`sha256sum /tmp/datapack-sub-${main}-${sub}.tar | awk '{print $1}'`" ]
                then
                    echo 'regen datapack sub' ${main} ${sub}
                    cat /tmp/datapack-sub-${main}-${sub}-new.tar${RANDOMNUMBERTOKEN} | /usr/bin/zstd --rm -22 --ultra > /tmp/datapack-sub-${main}-${sub}-new.tar${RANDOMNUMBERTOKEN}.zst
                    if [ $? -eq 0 ]
                    then
                        mv /tmp/datapack-sub-${main}-${sub}-new.tar${RANDOMNUMBERTOKEN} /tmp/datapack-sub-${main}-${sub}.tar
                        /usr/bin/zstd --test /tmp/datapack-sub-${main}-${sub}-new.tar${RANDOMNUMBERTOKEN}.zst > /dev/null 2>&1
                        if [ $? -eq 0 ]
                        then
                            mv /tmp/datapack-sub-${main}-${sub}-new.tar${RANDOMNUMBERTOKEN}.zst /tmp/datapack-sub-${main}-${sub}.tar.zst
                        else
                            rm /tmp/datapack-sub-${main}-${sub}-new.tar${RANDOMNUMBERTOKEN}.zst
                        fi
                    else
                        mv /tmp/datapack-sub-${main}-${sub}-new.tar${RANDOMNUMBERTOKEN} /tmp/datapack-sub-${main}-${sub}.tar
                    fi
                else
                    echo 'already ok datapack sub' ${main} ${sub}
                    rm /tmp/datapack-sub-${main}-${sub}-new.tar${RANDOMNUMBERTOKEN}
                fi
        done
done
