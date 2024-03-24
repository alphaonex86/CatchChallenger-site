cd '/home/user/Desktop/www/catchchallenger.first-world.info/'

rsync -avrt *.php root@763.vps.confiared.com:/home/first-world.info/catchchallenger/ --exclude=config.php --exclude=FGETNw3g7uSX3J9c.php 

ssh root@763.vps.confiared.com 'mv /home/first-world.info/catchchallenger/dynamic-part.php /home/first-world.info/catchchallenger/dynamic-parteVw1zjjIVwY2MmULjYFiHVdR.php;mv /home/first-world.info/catchchallenger/index.php /home/first-world.info/catchchallenger/indexeVw1zjjIVwY2MmULjYFiHVdR.php' &
ssh root@763.vps.confiared.com 'mv /home/first-world.info/catchchallenger/rss_global.php /home/first-world.info/catchchallenger/rss_globaleVw1zjjIVwY2MmULjYFiHVdR.php;mv /home/first-world.info/catchchallenger/official-server.php /home/first-world.info/catchchallenger/official-servereVw1zjjIVwY2MmULjYFiHVdR.php' &
wait
ssh root@763.vps.confiared.com "sed -i -r 's/dynamic-part.php/dynamic-parteVw1zjjIVwY2MmULjYFiHVdR.php/g' /home/first-world.info/catchchallenger/official-servereVw1zjjIVwY2MmULjYFiHVdR.php" &

rsync -avrt test/ root@763.vps.confiared.com:/home/first-world.info/catchchallenger/test/ &
rsync -avrt *.html root@763.vps.confiared.com:/home/first-world.info/catchchallenger/ --exclude=config.php &
rsync -avrt images/ root@763.vps.confiared.com:/home/first-world.info/catchchallenger/images/ &
rsync -avrt libs/ root@763.vps.confiared.com:/home/first-world.info/catchchallenger/libs/ '--exclude=*.log' &
rsync -avrt paiment/ root@763.vps.confiared.com:/home/first-world.info/catchchallenger/paiment/ '--exclude=*.log' &
rsync -avrt css/ root@763.vps.confiared.com:/home/first-world.info/catchchallenger/css/ &
rsync -avrt template/ root@763.vps.confiared.com:/home/first-world.info/catchchallenger/template/ &
rsync -avrt official-server/ root@763.vps.confiared.com:/home/first-world.info/catchchallenger/official-server/ --exclude=/datapack-explorer/ --exclude=/wikicache/ --exclude=*other-json.sh &
rsync -avrt wasm/ root@763.vps.confiared.com:/home/first-world.info/catchchallenger/wasm/ &

ssh root@763.vps.confiared.com '/usr/bin/wget https://catchchallenger.first-world.info/indexeVw1zjjIVwY2MmULjYFiHVdR.php -O /home/first-world.info/catchchallenger/index.html' &

wait

ssh root@763.vps.confiared.com 'chown www-data.www-data -Rf /home/first-world.info/catchchallenger/'
