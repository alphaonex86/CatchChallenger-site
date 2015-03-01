<?php
//rename into config.php and customize
$postgres_host='localhost';
$postgres_login='root';
$postgres_pass='root';
$postgres_db='catchchallenger';
$base_datapack_explorer_site_path='/official-server/datapack-explorer/';
$base_datapack_site_path='/datapack/';
$base_datapack_site_http='http://localhost';
$datapack_path='../datapack/';
$git_source_program='';//to have the commit order
//$map_generator='/usr/bin/map2png';
//$png_compress='/usr/bin/CatchChallenger/tools/datapack-compressor/png-compress.sh';
//$png_compress_zopfli='';
$png_compress_zopfli_level=100;
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
$wikivarsapp[0]['wikiFolder']='mediawiki';//resolv with: $base_datapack_site_http.'/'.$wikivarsapp[0]['wikiFolder'].'/api.php';
$wikivarsapp[0]['username']='admin';
$wikivarsapp[0]['password']='admin';
$wikivarsapp[0]['lang']='en';
$wikivarsapp[0]['generatefullpage']=false;
