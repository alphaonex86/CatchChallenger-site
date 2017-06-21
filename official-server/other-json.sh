#!/bin/bash
CURRENTPATH=`pwd`
BINPATH=/home/user/Desktop/CatchChallenger/tools/build-bot-test-connect-to-gameserver-Desktop_llvm-Debug/
cd ${BINPATH}

echo '-----------------------------------------------------------'
echo -e '-------------------\e[1mTry empty datapack\e[0m----------------------'
echo '-----------------------------------------------------------'

rm -Rf ${BINPATH}/datapack/
mkdir ${BINPATH}/datapack/
${BINPATH}/bot-test-connect-to-gameserver
RETURNCODE=$?
if [ ${RETURNCODE} -ne 0 ]
then
    echo "${BINPATH}/bot-test-connect-to-gameserver"
    echo '{"bot-test-connect-to-gameserver":{"state":"down","reason":"Failed at empty datapack: '${RETURNCODE}'"}}' > ${CURRENTPATH}/../other.json
    echo -e '\e[31m\e[1mFailed at empty datapack\e[39m\e[0m'
    exit
fi
echo -e '-------------------------Done:\e[32m\e[1mok\e[39m\e[0m---------------------------'

echo -e '--------------------------\e[33m\e[1mWait\e[39m\e[0m-----------------------------'
sleep 60

echo '-----------------------------------------------------------'
echo -e '--------------------\e[1mTry full datapack\e[0m----------------------'
echo '-----------------------------------------------------------'
rsync -art --delete ${BINPATH}/datapack-full/ ${BINPATH}/datapack/
${BINPATH}/bot-test-connect-to-gameserver
RETURNCODE=$?
if [ ${RETURNCODE} -ne 0 ]
then
    echo "${BINPATH}/bot-test-connect-to-gameserver"
    echo '{"bot-test-connect-to-gameserver":{"state":"down","reason":"Failed at full datapack: '${RETURNCODE}'"}}' > ${CURRENTPATH}/../other.json
    echo -e '\e[31m\e[1mFailed at full datapack\e[39m\e[0m'
    exit
fi
echo -e '-------------------------Done:\e[32m\e[1mok\e[39m\e[0m---------------------------'

echo -e '--------------------------\e[33m\e[1mWait\e[39m\e[0m-----------------------------'
sleep 60

echo '-----------------------------------------------------------'
echo -e '--------------------\e[1mTry semi datapack\e[0m----------------------'
echo '-----------------------------------------------------------'
rsync -art --delete ${BINPATH}/datapack/ ${BINPATH}/datapack-full/
rm ${BINPATH}/datapack/informations.xml
${BINPATH}/bot-test-connect-to-gameserver
RETURNCODE=$?
if [ ${RETURNCODE} -ne 0 ]
then
    echo "${BINPATH}/bot-test-connect-to-gameserver"
    echo '{"bot-test-connect-to-gameserver":{"state":"down","reason":"Failed at partial datapack: '${RETURNCODE}'"}}' > ${CURRENTPATH}/../other.json
    echo -e '\e[31m\e[1mFailed at partial datapack\e[39m\e[0m'
    exit
fi
echo -e '-------------------------Done:\e[32m\e[1mok\e[39m\e[0m---------------------------'
echo -e '\e[32m\e[1mAll is ok\e[39m\e[0m'

echo '{"bot-test-connect-to-gameserver":{"state":"up"}}' > ${CURRENTPATH}/../other.json
echo -e written into: "\e[1m${CURRENTPATH}/../other.json\e[0m"
