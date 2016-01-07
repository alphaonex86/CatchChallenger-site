<?php
//rename into config.php and customize
$postgres_login='root';
$postgres_pass='root';

$postgres_db_site=array('host'=>'localhost','database'=>'catchchallenger_site');
$postgres_db_login=array('host'=>'localhost','database'=>'catchchallenger_login');
$postgres_db_base=array('host'=>'localhost','database'=>'catchchallenger_base');
$postgres_db_tree=array(
'common1'=>array('host'=>'localhost','database'=>'catchchallenger_common',
    'servers'=>array(
            'server1'=>array('host'=>'localhost','database'=>'catchchallenger_server'),
        )
    ),
);

$postgres_db_server='catchchallenger';
$base_datapack_explorer_site_path='/official-server/datapack-explorer/';
$base_datapack_site_path='/datapack/';
$base_datapack_site_http='http://localhost';
$datapack_path='../datapack/';
$git_source_program='';//to have the commit order
//$map_generator='/usr/bin/map2png';
//$png_compress='/usr/bin/CatchChallenger/tools/datapack-compressor/png-compress.sh';
$datapack_explorer_local_path='datapack-explorer/';
$smtp_server='';
$smtp_login='';
$smtp_password='';
//change it:
$benchmark_key='nyZqpuuV22LlrUA0';
$sales_email='';
$admin_email='';

$nxt_secretPhrase='';
$nxt_seller='';
$nxt_product_id='';

//see doc/REAME-mediawiki.txt
$wikivarsappvar=0;
$wikivarsapp[$wikivarsappvar]['wikiFolder']='mediawiki';//resolv with: $base_datapack_site_http.'/'.$wikivarsapp[0]['wikiFolder'].'/api.php';
$wikivarsapp[$wikivarsappvar]['username']='admin';
$wikivarsapp[$wikivarsappvar]['password']='admin';
$wikivarsapp[$wikivarsappvar]['lang']='en';
$wikivarsapp[$wikivarsappvar]['generatefullpage']=false;
$wikivarsappvar++;
