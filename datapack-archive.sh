#!/bin/bash
#--posix:information leak, -H ustar no and leak and better compression
find datapack/ -type d -exec chmod 700 "{}" \;
find datapack/ -type f -exec chmod 600 "{}" \;
find datapack/ -type f -print | grep -F -v 'datapack/pack/' | grep -F -v 'map/main/' | sort > /tmp/temporary_file_list
tar --owner=0 --group=0 --mtime='2010-01-01' -H ustar -c -f - -T /tmp/temporary_file_list > /tmp/datapack-new.tar
mkdir -p /tmp/datapack/pack/
if [ ! -e /tmp/datapack-new.tar ] || [ "`sha256sum /tmp/datapack-new.tar | awk '{print $1}'`" != "`sha256sum /tmp/datapack.tar | awk '{print $1}'`" ]
then
    echo 'regen datapack'
    cp /tmp/datapack-new.tar /tmp/datapack.tar
    cat /tmp/datapack.tar | xz -9 --check=crc32 > /tmp/datapack.tar.xz
fi
PATHB=`pwd`
for main in $(ls ${PATHB}/datapack/map/main); do
        cd ${PATHB}/datapack/map/main/
        find ${main}/ -type f -print | grep -F -v 'sub/' | sort > /tmp/temporary_file_list
        tar --owner=0 --group=0 --mtime='2010-01-01' -H ustar -c -f - -T /tmp/temporary_file_list > /tmp/datapack-main-${main}-new.tar
        if [ ! -e /tmp/datapack-main-${main}-new.tar ] || [ "`sha256sum /tmp/datapack-main-${main}-new.tar | awk '{print $1}'`" != "`sha256sum /tmp/datapack-main-${main}.tar | awk '{print $1}'`" ]
        then
            echo 'regen datapack main' ${main}
            cp /tmp/datapack-main-${main}-new.tar /tmp/datapack-main-${main}.tar
            cat /tmp/datapack-main-${main}.tar | xz -9 --check=crc32 > /tmp/datapack-main-${main}.tar.xz
        fi
        for sub in $(ls ${PATHB}/datapack/map/main/${main}/sub/); do
                cd ${PATHB}/datapack/map/main/${main}/sub/
                find ${sub}/ -type f -print | sort > /tmp/temporary_file_list
                tar --owner=0 --group=0 --mtime='2010-01-01' -H ustar -c -f - -T /tmp/temporary_file_list > /tmp/datapack-sub-${main}-${sub}-new.tar
                if [ ! -e /tmp/datapack-sub-${main}-${sub}-new.tar ] || [ "`sha256sum /tmp/datapack-sub-${main}-${sub}-new.tar | awk '{print $1}'`" != "`sha256sum /tmp/datapack-sub-${main}-${sub}.tar | awk '{print $1}'`" ]
                then
                    echo 'regen datapack sub' ${main} ${sub}
                    cp /tmp/datapack-sub-${main}-${sub}-new.tar /tmp/datapack-sub-${main}-${sub}.tar
                    cat /tmp/datapack-sub-${main}-${sub}.tar | xz -9 --check=crc32 > /tmp/datapack-sub-${main}-${sub}.tar.xz
                fi
        done
done