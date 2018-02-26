#!/bin/bash
CURRENTPATH=`pwd`

testtogameserver () {
    mode=$1
    BINPATHFULL=$2
    echo Mode ${mode}
    
    if [ "${mode}" -eq 0 ]
    then
        echo '-----------------------------------------------------------'
        echo -e '-------------------\e[1mTry empty datapack\e[0m----------------------'
        echo '-----------------------------------------------------------'
        rm -Rf ${BINPATH}/datapack/
        mkdir ${BINPATH}/datapack/
    else
        if [ "${mode}" -eq 1 ]
        then
            echo '-----------------------------------------------------------'
            echo -e '--------------------\e[1mTry full datapack\e[0m----------------------'
            echo '-----------------------------------------------------------'
            rsync -art --delete ${BINPATH}/datapack-full/ ${BINPATH}/datapack/
        else
            if [ "${mode}" -eq 2 ]
            then
                echo '-----------------------------------------------------------'
                echo -e '--------------------\e[1mTry semi datapack\e[0m----------------------'
                echo '-----------------------------------------------------------'
                rsync -art --delete ${BINPATH}/datapack/ ${BINPATH}/datapack-full/
                rm ${BINPATH}/datapack/informations.xml
            else
                echo wrong mode
                exit 123
            fi
        fi
    fi
    
    ${BINPATHFULL}
    RETURNCODE=$?
    if [ ${RETURNCODE} -ne 0 ]
    then
        echo "${BINPATHFULL}"
        echo -e '\e[31m\e[1mFailed\e[39m\e[0m'
        return ${RETURNCODE}
    fi
    echo -e '-------------------------Done:\e[32m\e[1mok\e[39m\e[0m---------------------------'
    
    return 0
}

echo '{' > ${CURRENTPATH}/../other.json

BINPATH=/home/user/Desktop/CatchChallenger/tools/build-bot-test-connect-to-gameserver-Desktop_llvm-Debug/
cd ${BINPATH}




testtogameserver 0 ${BINPATH}/bot-test-connect-to-gameserver
RETURNCODE=$?
if [ ${RETURNCODE} -eq 0 ]
then
    echo '"bot-test-connect-to-gameserver1":{"state":"up"}' >> ${CURRENTPATH}/../other.json
else
    echo '"bot-test-connect-to-gameserver1":{"state":"down","reason":"Failed at empty datapack: '${RETURNCODE}'"}' >> ${CURRENTPATH}/../other.json
fi
echo ',' >> ${CURRENTPATH}/../other.json;sleep 15


testtogameserver 1 ${BINPATH}/bot-test-connect-to-gameserver
RETURNCODE=$?
if [ ${RETURNCODE} -eq 0 ]
then
    echo '"bot-test-connect-to-gameserver2":{"state":"up"}' >> ${CURRENTPATH}/../other.json
else
    echo '"bot-test-connect-to-gameserver2":{"state":"down","reason":"Failed at empty datapack: '${RETURNCODE}'"}' >> ${CURRENTPATH}/../other.json
fi
echo ',' >> ${CURRENTPATH}/../other.json;sleep 15


testtogameserver 2 ${BINPATH}/bot-test-connect-to-gameserver
RETURNCODE=$?
if [ ${RETURNCODE} -eq 0 ]
then
    echo '"bot-test-connect-to-gameserver3":{"state":"up"}' >> ${CURRENTPATH}/../other.json
else
    echo '"bot-test-connect-to-gameserver3":{"state":"down","reason":"Failed at empty datapack: '${RETURNCODE}'"}' >> ${CURRENTPATH}/../other.json
fi
echo ',' >> ${CURRENTPATH}/../other.json;sleep 15


BINPATH=/home/user/Desktop/CatchChallenger/tools/build-bot-test-connect-to-gameserver-Desktop_llvm-Debug2/
cd ${BINPATH}


testtogameserver 0 ${BINPATH}/bot-test-connect-to-gameserver
RETURNCODE=$?
if [ ${RETURNCODE} -eq 0 ]
then
    echo '"bot-test-connect-to-gameserver4":{"state":"up"}' >> ${CURRENTPATH}/../other.json
else
    echo '"bot-test-connect-to-gameserver4":{"state":"down","reason":"Failed at empty datapack: '${RETURNCODE}'"}' >> ${CURRENTPATH}/../other.json
fi
echo ',' >> ${CURRENTPATH}/../other.json;sleep 15


testtogameserver 1 ${BINPATH}/bot-test-connect-to-gameserver
RETURNCODE=$?
if [ ${RETURNCODE} -eq 0 ]
then
    echo '"bot-test-connect-to-gameserver5":{"state":"up"}' >> ${CURRENTPATH}/../other.json
else
    echo '"bot-test-connect-to-gameserver5":{"state":"down","reason":"Failed at empty datapack: '${RETURNCODE}'"}' >> ${CURRENTPATH}/../other.json
fi
echo ',' >> ${CURRENTPATH}/../other.json;sleep 15


testtogameserver 2 ${BINPATH}/bot-test-connect-to-gameserver
RETURNCODE=$?
if [ ${RETURNCODE} -eq 0 ]
then
    echo '"bot-test-connect-to-gameserver6":{"state":"up"}' >> ${CURRENTPATH}/../other.json
else
    echo '"bot-test-connect-to-gameserver6":{"state":"down","reason":"Failed at empty datapack: '${RETURNCODE}'"}' >> ${CURRENTPATH}/../other.json
fi
#echo ',' >> ${CURRENTPATH}/../other.json;sleep 15




echo '}' >> ${CURRENTPATH}/../other.json
