#!/bin/bash
testtogameserver () {
    mode=$1
    BINPATHFULL=$2
    NAME=$3
    DATAPACK=${4}
    echo Mode ${mode}

    echo '-----------------------------------------------------------'
    echo -e "--------------------------\e[1m${NAME}\e[0m-----------------------"
    if [ "${mode}" -eq 0 ]
    then
        echo -e '-------------------\e[1mTry empty datapack\e[0m----------------------'
        rm -Rf ${BINPATH}/datapack/
        mkdir ${BINPATH}/datapack/
    else
        if [ "${mode}" -eq 1 ]
        then
            echo -e '--------------------\e[1mTry full datapack\e[0m----------------------'
            rsync -art --delete ${DATAPACK} ${BINPATH}/datapack/
        else
            if [ "${mode}" -eq 2 ]
            then
                echo -e '--------------------\e[1mTry semi datapack\e[0m----------------------'
                rsync -art --delete ${DATAPACK} ${BINPATH}/datapack/
                rm ${BINPATH}/datapack/informations.xml
            else
                echo wrong mode
                exit 123
            fi
        fi
    fi
    echo '-----------------------------------------------------------'
    
    ${BINPATHFULL} ${BINPATH}/${NAME}
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

RANDTOKEN=`cat /dev/urandom | tr -dc 'a-zA-Z0-9' | fold -w ${1:-32} | head -n 1`
echo '{' > /tmp/other${RANDTOKEN}.json

NAME="bottest.xml"
BINPATH=/home/user/Desktop/CatchChallenger/tools/build-bot-test-connect-to-gameserver-Desktop-Debug/
DATAPACK=/home/user/Desktop/CatchChallenger/.test/
cd ${BINPATH}
testtogameserver 0 ${BINPATH}/bot-test-connect-to-gameserver ${NAME} ${DATAPACK}
RETURNCODE=$?
if [ ${RETURNCODE} -eq 0 ]
then
    echo '"bot-test-connect-to-gameserver1":{"state":"up"}' >> /tmp/other${RANDTOKEN}.json
else
    echo '"bot-test-connect-to-gameserver1":{"state":"down","reason":"Failed at empty datapack: '${RETURNCODE}'"}' >> /tmp/other${RANDTOKEN}.json
fi
echo ',' >> /tmp/other${RANDTOKEN}.json;sleep 15
testtogameserver 1 ${BINPATH}/bot-test-connect-to-gameserver ${NAME} ${DATAPACK}
RETURNCODE=$?
if [ ${RETURNCODE} -eq 0 ]
then
    echo '"bot-test-connect-to-gameserver2":{"state":"up"}' >> /tmp/other${RANDTOKEN}.json
else
    echo '"bot-test-connect-to-gameserver2":{"state":"down","reason":"Failed at empty datapack: '${RETURNCODE}'"}' >> /tmp/other${RANDTOKEN}.json
fi
echo ',' >> /tmp/other${RANDTOKEN}.json;sleep 15
testtogameserver 2 ${BINPATH}/bot-test-connect-to-gameserver ${NAME} ${DATAPACK}
RETURNCODE=$?
if [ ${RETURNCODE} -eq 0 ]
then
    echo '"bot-test-connect-to-gameserver3":{"state":"up"}' >> /tmp/other${RANDTOKEN}.json
else
    echo '"bot-test-connect-to-gameserver3":{"state":"down","reason":"Failed at empty datapack: '${RETURNCODE}'"}' >> /tmp/other${RANDTOKEN}.json
fi
echo ',' >> /tmp/other${RANDTOKEN}.json;sleep 15






NAME="bottest2.xml"
BINPATH=/home/user/Desktop/CatchChallenger/tools/build-bot-test-connect-to-gameserver-Desktop-Debug/
DATAPACK=/home/user/Desktop/CatchChallenger/.test/
cd ${BINPATH}
testtogameserver 0 ${BINPATH}/bot-test-connect-to-gameserver ${NAME} ${DATAPACK}
RETURNCODE=$?
if [ ${RETURNCODE} -eq 0 ]
then
    echo '"bot-test-connect-to-gameserver4":{"state":"up"}' >> /tmp/other${RANDTOKEN}.json
else
    echo '"bot-test-connect-to-gameserver4":{"state":"down","reason":"Failed at empty datapack: '${RETURNCODE}'"}' >> /tmp/other${RANDTOKEN}.json
fi
echo ',' >> /tmp/other${RANDTOKEN}.json;sleep 15
testtogameserver 1 ${BINPATH}/bot-test-connect-to-gameserver ${NAME} ${DATAPACK}
RETURNCODE=$?
if [ ${RETURNCODE} -eq 0 ]
then
    echo '"bot-test-connect-to-gameserver5":{"state":"up"}' >> /tmp/other${RANDTOKEN}.json
else
    echo '"bot-test-connect-to-gameserver5":{"state":"down","reason":"Failed at empty datapack: '${RETURNCODE}'"}' >> /tmp/other${RANDTOKEN}.json
fi
echo ',' >> /tmp/other${RANDTOKEN}.json;sleep 15
testtogameserver 2 ${BINPATH}/bot-test-connect-to-gameserver ${NAME} ${DATAPACK}
RETURNCODE=$?
if [ ${RETURNCODE} -eq 0 ]
then
    echo '"bot-test-connect-to-gameserver6":{"state":"up"}' >> /tmp/other${RANDTOKEN}.json
else
    echo '"bot-test-connect-to-gameserver6":{"state":"down","reason":"Failed at empty datapack: '${RETURNCODE}'"}' >> /tmp/other${RANDTOKEN}.json
fi
echo ',' >> /tmp/other${RANDTOKEN}.json;sleep 15




NAME="bottest3.xml"
BINPATH=/home/user/Desktop/CatchChallenger/tools/build-bot-test-connect-to-gameserver-Desktop-Debug/
DATAPACK=/home/user/Desktop/CatchChallenger/.test/
cd ${BINPATH}
testtogameserver 0 ${BINPATH}/bot-test-connect-to-gameserver ${NAME} ${DATAPACK}
RETURNCODE=$?
if [ ${RETURNCODE} -eq 0 ]
then
    echo '"bot-test-connect-to-gameserver7":{"state":"up"}' >> /tmp/other${RANDTOKEN}.json
else
    echo '"bot-test-connect-to-gameserver7":{"state":"down","reason":"Failed at empty datapack: '${RETURNCODE}'"}' >> /tmp/other${RANDTOKEN}.json
fi
echo ',' >> /tmp/other${RANDTOKEN}.json;sleep 15
testtogameserver 1 ${BINPATH}/bot-test-connect-to-gameserver ${NAME} ${DATAPACK}
RETURNCODE=$?
if [ ${RETURNCODE} -eq 0 ]
then
    echo '"bot-test-connect-to-gameserver8":{"state":"up"}' >> /tmp/other${RANDTOKEN}.json
else
    echo '"bot-test-connect-to-gameserver8":{"state":"down","reason":"Failed at empty datapack: '${RETURNCODE}'"}' >> /tmp/other${RANDTOKEN}.json
fi
echo ',' >> /tmp/other${RANDTOKEN}.json;sleep 15
testtogameserver 2 ${BINPATH}/bot-test-connect-to-gameserver ${NAME} ${DATAPACK}
RETURNCODE=$?
if [ ${RETURNCODE} -eq 0 ]
then
    echo '"bot-test-connect-to-gameserver9":{"state":"up"}' >> /tmp/other${RANDTOKEN}.json
else
    echo '"bot-test-connect-to-gameserver9":{"state":"down","reason":"Failed at empty datapack: '${RETURNCODE}'"}' >> /tmp/other${RANDTOKEN}.json
fi
echo ',' >> /tmp/other${RANDTOKEN}.json;sleep 15






NAME="bottest4.xml"
DATAPACK=/home/user/Desktop/CatchChallenger/CatchChallenger-datapack/
BINPATH=/home/user/Desktop/CatchChallenger/tools/build-bot-test-connect-to-gameserver-Desktop-Debug/
cd ${BINPATH}
testtogameserver 0 ${BINPATH}/bot-test-connect-to-gameserver ${NAME} ${DATAPACK}
RETURNCODE=$?
if [ ${RETURNCODE} -eq 0 ]
then
    echo '"bot-test-connect-to-gameserver10":{"state":"up"}' >> /tmp/other${RANDTOKEN}.json
else
    echo '"bot-test-connect-to-gameserver10":{"state":"down","reason":"Failed at empty datapack: '${RETURNCODE}'"}' >> /tmp/other${RANDTOKEN}.json
fi
echo ',' >> /tmp/other${RANDTOKEN}.json;sleep 15
testtogameserver 1 ${BINPATH}/bot-test-connect-to-gameserver ${NAME} ${DATAPACK}
RETURNCODE=$?
if [ ${RETURNCODE} -eq 0 ]
then
    echo '"bot-test-connect-to-gameserver11":{"state":"up"}' >> /tmp/other${RANDTOKEN}.json
else
    echo '"bot-test-connect-to-gameserver11":{"state":"down","reason":"Failed at empty datapack: '${RETURNCODE}'"}' >> /tmp/other${RANDTOKEN}.json
fi
echo ',' >> /tmp/other${RANDTOKEN}.json;sleep 15
testtogameserver 2 ${BINPATH}/bot-test-connect-to-gameserver ${NAME} ${DATAPACK}
RETURNCODE=$?
if [ ${RETURNCODE} -eq 0 ]
then
    echo '"bot-test-connect-to-gameserver12":{"state":"up"}' >> /tmp/other${RANDTOKEN}.json
else
    echo '"bot-test-connect-to-gameserver12":{"state":"down","reason":"Failed at empty datapack: '${RETURNCODE}'"}' >> /tmp/other${RANDTOKEN}.json
fi
echo ',' >> /tmp/other${RANDTOKEN}.json;sleep 15





NAME="bottest5.xml"
DATAPACK=/home/user/Desktop/CatchChallenger/CatchChallenger-datapack/
BINPATH=/home/user/Desktop/CatchChallenger/tools/build-bot-test-connect-to-gameserver-Desktop-Debug/
cd ${BINPATH}
testtogameserver 0 ${BINPATH}/bot-test-connect-to-gameserver ${NAME} ${DATAPACK}
RETURNCODE=$?
if [ ${RETURNCODE} -eq 0 ]
then
    echo '"bot-test-connect-to-gameserver13":{"state":"up"}' >> /tmp/other${RANDTOKEN}.json
else
    echo '"bot-test-connect-to-gameserver13":{"state":"down","reason":"Failed at empty datapack: '${RETURNCODE}'"}' >> /tmp/other${RANDTOKEN}.json
fi
echo ',' >> /tmp/other${RANDTOKEN}.json;sleep 15
testtogameserver 1 ${BINPATH}/bot-test-connect-to-gameserver ${NAME} ${DATAPACK}
RETURNCODE=$?
if [ ${RETURNCODE} -eq 0 ]
then
    echo '"bot-test-connect-to-gameserver14":{"state":"up"}' >> /tmp/other${RANDTOKEN}.json
else
    echo '"bot-test-connect-to-gameserver14":{"state":"down","reason":"Failed at empty datapack: '${RETURNCODE}'"}' >> /tmp/other${RANDTOKEN}.json
fi
echo ',' >> /tmp/other${RANDTOKEN}.json;sleep 15
testtogameserver 2 ${BINPATH}/bot-test-connect-to-gameserver ${NAME} ${DATAPACK}
RETURNCODE=$?
if [ ${RETURNCODE} -eq 0 ]
then
    echo '"bot-test-connect-to-gameserver15":{"state":"up"}' >> /tmp/other${RANDTOKEN}.json
else
    echo '"bot-test-connect-to-gameserver15":{"state":"down","reason":"Failed at empty datapack: '${RETURNCODE}'"}' >> /tmp/other${RANDTOKEN}.json
fi
#echo ',' >> /tmp/other${RANDTOKEN}.json;sleep 15




echo '}' >> /tmp/other${RANDTOKEN}.json

mv /tmp/other${RANDTOKEN}.json /tmp/other2.json

if [ `grep -F down /tmp/other2.json | wc -l` -ne 0 ]
then
    exit 1
else
    exit 0
fi

