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
    mv /tmp/datapack-new.tar${RANDOMNUMBERTOKEN} /tmp/datapack.tar
    cat /tmp/datapack.tar | xz -9 --check=crc32 > /tmp/datapack.tar.xz${RANDOMNUMBERTOKEN}
    mv /tmp/datapack.tar.xz${RANDOMNUMBERTOKEN} /tmp/datapack.tar.xz
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
            mv /tmp/datapack-main-${main}-new.tar${RANDOMNUMBERTOKEN} /tmp/datapack-main-${main}.tar
            cat /tmp/datapack-main-${main}.tar | xz -9 --check=crc32 > /tmp/datapack-main-${main}.tar.xz${RANDOMNUMBERTOKEN}
            mv /tmp/datapack-main-${main}.tar.xz${RANDOMNUMBERTOKEN} /tmp/datapack-main-${main}.tar.xz
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
                    mv /tmp/datapack-sub-${main}-${sub}-new.tar${RANDOMNUMBERTOKEN} /tmp/datapack-sub-${main}-${sub}.tar
                    cat /tmp/datapack-sub-${main}-${sub}.tar | xz -9 --check=crc32 > /tmp/datapack-sub-${main}-${sub}.tar.xz${RANDOMNUMBERTOKEN}
                    mv /tmp/datapack-sub-${main}-${sub}.tar.xz${RANDOMNUMBERTOKEN} /tmp/datapack-sub-${main}-${sub}.tar.xz
                else
                    echo 'already ok datapack sub' ${main} ${sub}
                    rm /tmp/datapack-sub-${main}-${sub}-new.tar${RANDOMNUMBERTOKEN}
                fi
        done
done
