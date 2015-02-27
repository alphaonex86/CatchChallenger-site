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

/* To add at mediawiki to enable:
$wgAllowImageTag = true;
$wgAllowExternalImages=true;
$wgExtraNamespacesIndex=500;
$wgExtraNamespaces[$wgExtraNamespacesIndex++] = "Items";
$wgExtraNamespaces[$wgExtraNamespacesIndex++] = "Maps";
$wgExtraNamespaces[$wgExtraNamespacesIndex++] = "Bots";
$wgExtraNamespaces[$wgExtraNamespacesIndex++] = "Monsters";
$wgExtraNamespaces[$wgExtraNamespacesIndex++] = "Industries";
$wgExtraNamespaces[$wgExtraNamespacesIndex++] = "Zones";
$wgExtraNamespaces[$wgExtraNamespacesIndex++] = "Quests";
$wgExtraNamespaces[$wgExtraNamespacesIndex++] = "Buffs";
$wgExtraNamespaces[$wgExtraNamespacesIndex++] = "Skills";
$wgExtraNamespaces[$wgExtraNamespacesIndex++] = "Monsters type";
And into skins/Vector.php:into parent::initPage() put:
$out->addHeadItem( 'screen','<link rel="stylesheet" type="text/css" media="screen" href="/css/datapack-explorer.css" />');
Code to add list to the main page:
* [[Bots list]]
* [[Buffs list]]
* [[Crafting list]]
* [[Industries list]]
* [[Items list]]
* [[Maps list]]
* [[Monsters list]]
* [[Monsters types]]
* [[Plants list]]
* [[Quests list]]
* [[Skills list]]
* [[Starters]]
*/
$wikivarsapp['apiURL']='http://localhost/mediawiki/api.php';
$wikivarsapp['username']='admin';
$wikivarsapp['password']='admin';
$wikivarsapp['generatefullpage']=false;
