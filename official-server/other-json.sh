#!/bin/bash
CURRENTPATH=`pwd`
BINPATH=/home/user/Desktop/CatchChallenger/tools/build-bot-test-connect-to-gameserver-Desktop_llvm-Debug/
cd ${BINPATH}

rm -Rf ${BINPATH}/datapack/
mkdir ${BINPATH}/datapack/
${BINPATH}/bot-test-connect-to-gameserver
RETURNCODE=$?
if [ ${RETURNCODE} -ne 0 ]
then
    echo "${BINPATH}/bot-test-connect-to-gameserver"
    echo '{"bot-test-connect-to-gameserver":{"state":"down","reason":"Failed at empty datapack: '${RETURNCODE}'"}}' > ${CURRENTPATH}/../other.json
    echo "Failed at empty datapack"
    exit
fi

sleep 60

rsync -art --delete ${BINPATH}/datapack-full/ ${BINPATH}/datapack/
${BINPATH}/bot-test-connect-to-gameserver
RETURNCODE=$?
if [ ${RETURNCODE} -ne 0 ]
then
    echo "${BINPATH}/bot-test-connect-to-gameserver"
    echo '{"bot-test-connect-to-gameserver":{"state":"down","reason":"Failed at full datapack: '${RETURNCODE}'"}}' > ${CURRENTPATH}/../other.json
    echo "Failed at full datapack"
    exit
fi
rsync -art --delete ${BINPATH}/datapack/ ${BINPATH}/datapack-full/

sleep 60

rm ${BINPATH}/datapack/informations.xml
${BINPATH}/bot-test-connect-to-gameserver
RETURNCODE=$?
if [ ${RETURNCODE} -ne 0 ]
then
    echo "${BINPATH}/bot-test-connect-to-gameserver"
    echo '{"bot-test-connect-to-gameserver":{"state":"down","reason":"Failed at partial datapack: '${RETURNCODE}'"}}' > ${CURRENTPATH}/../other.json
    echo "Failed at partial datapack"
    exit
fi

echo '{"bot-test-connect-to-gameserver":{"state":"up"}}' > ${CURRENTPATH}/../other.json
echo written into: ${CURRENTPATH}/../other.json
