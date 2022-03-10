<?php
$postgres_login='postgres';
$postgres_pass='';

$postgres_db_site=array('host'=>'localhost','database'=>'catchchallenger_site');
$postgres_db_login=array('host'=>'2803:1920::7:2','database'=>'catchchallenger_login');
$postgres_db_base=array('host'=>'2803:1920::7:2','database'=>'catchchallenger_base');
$postgres_db_tree=array(
'common1'=>array('host'=>'2803:1920::7:2','database'=>'catchchallenger_common',
    'servers'=>array(
            'server1'=>array('host'=>'2803:1920::7:2','database'=>'catchchallenger_server'),
        )
    ),
);

$loginserverlist=array(
    array('host'=>'catchchallenger-gateway','port'=>33261,'step'=>'protocol'),
    array('host'=>'gateway.first-world.info','port'=>42490,'step'=>'connect'/*can be: connect, protocol (default)*/),
    array('host'=>'catchchallenger-login-1','port'=>42012),
    array('host'=>'catchchallenger-login-proxy-1','port'=>42012)
);
$mirrorserverlist=array(
    //the first is considered as the clean and refence version
    'http://catchchallenger-site-datapack.portable-datacenter.first-world.info/datapack/',
);
$singledatapacklisttest=array(
);

$base_datapack_explorer_site_path='/official-server/datapack-explorer/';
$base_datapack_site_path='/datapack/';
$base_datapack_site_http='http://amber';
$datapack_path='../datapack/';
$datapack_path_wikicache='wikicache/';
$datapack_source_url='https://github.com/alphaonex86/CatchChallenger-datapack';
$git_source_program='/home/user/Desktop/CatchChallenger/git/';//to have the commit order
$map_generator='/home/user/Desktop/CatchChallenger/tools/build-map2png-Desktop-Debug/map2png';
//$png_compress='/home/user/Desktop/CatchChallenger/tools/datapack-compressor/png-compress.sh';
$datapack_explorer_local_path='datapack-explorer/';
$smtp_server='smtp.gmail.com';
$smtp_login='brule.herman@gmail.com';
$smtp_password='rqmvalstaddsvzpg';
$smtp_port=587;
$benchmark_key='eYGhNVwlKq2ErXlL';
$sales_email='sales@first-world.info';
$admin_email='catchchallenger@first-world.info';

//see doc/REAME-mediawiki.txt
$wikivarsapp=array(
    /*array('wikiFolder'=>'wen',//resolv with: $base_datapack_site_http.'/'.$wikivarsapp[0]['wikiFolder'].'/api.php';
        'username'=>'bot',
        'password'=>'bot1234',
        'lang'=>'en',
        'generatefullpage'=>true,
        ),
    array('wikiFolder'=>'wfr',//resolv with: $base_datapack_site_http.'/'.$wikivarsapp[0]['wikiFolder'].'/api.php';
        'username'=>'bot',
        'password'=>'bot1234',
        'lang'=>'fr',
        'generatefullpage'=>true,
        ),*/
);

$otherjsonfile='/tmp/other.json';
//$gameserverfile='gameserver.json';
$statsserversock='/home/user/Desktop/CatchChallenger/tools/build-stats-Desktop-Debug/catchchallenger-stats.sock';
$loginserverfile='loginserver.json';
$previously_know_server_file='previously_know_server.json';
$mirrorserverfile='mirrorserver.json';
$contentstatfile='official-server/datapack-explorer/contentstat.json';
$backupfile='backup.json';
